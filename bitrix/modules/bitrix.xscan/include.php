<?
IncludeModuleLangFile(__FILE__);

Class CBitrixXscan 
{
	static $var = '\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
	static $spaces = "[ \r\t\n]*";

	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
//			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while(false !== $item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$MODULE_ID.'_'.$item))
						file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');

					$arFiles[] = $item;
				}

				sort($arFiles);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => GetMessage("BITRIX_XSCAN_SEARCH"),
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		$aModuleMenu[] = $aMenu;
	}

	function CheckFile($f)
	{
		global $LAST_REG;

		static $me;
		if (!$me)
			$me = realpath(__FILE__);
		if (realpath($f) == $me)
			return false;

		if (self::SystemFile($f))
			return false;

		# CODE 100
		if (basename($f) == '.htaccess')
		{
			$str = file_get_contents($f);
			$res = preg_match('#<(\?|script)#i',$str,$regs);
			$LAST_REG = $regs[0];
			return $res ? '[100] htaccess' : false;
		}

		# CODE 110
		if (preg_match('#^/upload/.*\.php$#',str_replace($_SERVER['DOCUMENT_ROOT'], '' ,$f)))
		{
			return '[110] php file in upload dir';
		}

		if (!preg_match('#\.php$#',$f,$regs))
			return false;

		# CODE 200
		if (false === $str = file_get_contents($f))
			return '[200] read error';

		$str = preg_replace('#/\*.*?\*/#s', '', $str);
		$str = preg_replace('#[\r\n][ \t]*//.*#m', '', $str);
		$str = preg_replace('/[\r\n][ \t]*#.*/m', '', $str);

		# CODE 300
		if (preg_match('#[^a-z:](eval|assert|create_function|ob_start)'.self::$spaces.'\(([^\)]*)\)#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (preg_match('#\$(GLOBALS|_COOKIE|_GET|_POST|_REQUEST|[a-z_]{2,}[0-9]+)#', $regs[2],$regs))
			{
				return '[300] eval';
			}
		}

		# CODE 350
		if (preg_match('#'.self::$var.self::$spaces.'='.self::$spaces.'\$(GLOBALS|_COOKIE|_GET|_POST|_REQUEST)'.self::$spaces.'[^\[]#', $str,$regs))
		{
			$LAST_REG = $regs[0];
			if (self::StatVulnCheck($str))
				return '[350] global vars manipulation';
		}

		# CODE 400
		if (preg_match('#\$(USER|GLOBALS..USER..)->Authorize'.self::$spaces.'\([0-9]+\)#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[400] bitrix auth';
		}

		# CODE 500
		if (preg_match('#[\'"]php://filter#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[500] php wrapper';
		}

		# CODE 600
		if (preg_match('#(include|require)(_once)?'.self::$spaces.'\([^\)]+\.([a-z0-9]+).'.self::$spaces.'\)#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if ($regs[3] != 'php')
				return '[600] strange include';
		}

		# CODE 610
		if (preg_match('#\$__+[^a-z_]#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[610] strange vars';
		}

		# CODE 615
		if (preg_match('#\${["\']\\\\x[0-9]{2}[a-z0-9\\\\]+["\']}#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[615] hidden vars';
		}


		# CODE 620
		if (preg_match('#\$['."_\x80-\xff".']+'.self::$spaces.'=#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[620] binary vars';
		}

		# CODE 630
		if (preg_match('#[a-z0-9+=/\n\r]{255,}#im', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!preg_match('#data:image/[^;]+;base64,[a-z0-9+=/]{255,}#i', $str, $regs))
				return '[630] long line';
		}

		# CODE 640
		if (preg_match('#exif_read_data\(#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[640] strange exif';
		}

		# CODE 650
		if (preg_match('#[^\\\\]'.self::$var.self::$spaces.'\(#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (self::StatVulnCheck($str))
				return '[650] variable as a function';
		}

		# CODE 660
		if (preg_match('#'.self::$var.'('.$spaces.'\[[\'"]?[a-z0-9]+[\'"]?\])+'.$spaces.'\(#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (self::StatVulnCheck($str))
				return '[660] array member as a function';
		}

		# CODE 663
		if (preg_match("#^.*([\x01-\x08\x0b\x0c\x0f-\x1f])#m", $str, $regs))
		{
			$LAST_REG = $regs[1];
			if (!preg_match('#^\$ser_content = #', $regs[0]))
				return '[663] binary data';
		}

		# CODE 665
		if (preg_match_all('#(\\\\x[a-f0-9]{2}|\\\\[0-9]{2,3})#i', $str, $regs))
		{
			$LAST_REG = implode(" ", $regs[1]);
			if (strlen(implode('', $regs[1]))/filesize($f) > 0.1)
				return '[665] chars by code';
		}

		# CODE 700
		if (preg_match('#file_get_contents\(\$[^\)]+\);[^a-z]*file_put_contents#mi', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[700] file from variable';
		}

		# CODE 710
		if (preg_match('#file_get_contents\([\'"]https?://#mi', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[710] file from the Internet';
		}

		# CODE 800
		if (preg_match('#preg_replace\(\$_#mi', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[800] preg_replace pattern from variable';
		}

		# CODE 670
		if (preg_match('#('.self::$var.')'.$spaces.'\('.$spaces.self::$var.'#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			$src_var = $regs[1];
			while(preg_match('#\$'.str_replace('$', '', $src_var).$spaces.'='.$spaces.'('.self::$var.')#i', $str, $regs))
			{
				$src_var = str_replace('$', '', $regs[1]);
			}
			if (preg_match('#^(GLOBAL|_COOKIE|_GET|_POST|_REQUEST)$#', $src_var))
				return '[670] function from global var';
		}

		# CODE END
		return false;
	}

	function StatVulnCheck($str, $bAll = false)
	{
		$regular = $bAll ? '#\$?[a-z_]+#i' : '#'.self::$var.'#';
		if (!preg_match_all($regular, $str, $regs))
			return false;
		$ar0 = $regs[0];
		$ar1 = array_unique($ar0);
		$uniq = count($ar1)/count($ar0);

		$ar2 = array();
		foreach($ar1 as $var)
		{
			if ($bAll && function_exists($var))
				$p = 0;
			elseif ($bAll && preg_match('#^[a-z]{1,2}$#i', $var))
				$p = 1;
			elseif (preg_match('#^\$?(function|php|csv|sql|__DIR__|__FILE__|__LINE__|DBDebug|DBType|DBName|DBPassword|DBHost|APPLICATION)$#i', $var))
				$p = 0;
			elseif (preg_match('#__#', $var))
				$p = 1;
			elseif (preg_match('#^\$(ar|str)[A-Z]#', $var, $regs))
				$p = 0;
			elseif (preg_match_all('#([qwrtpsdfghjklzxcvbnm]{3,}|[a-z]+[0-9]+[a-z]+)#i', $var, $regs))
				$p = strlen(implode('',$regs[0]))/strlen($var) > 0.3;
			else
				$p = 0;

//			if ($p)
//				echo $var." => ".$p."<br>";
			$ar2[] = $p;
		}
		$prob = array_sum($ar2)/count($ar2);
		if ($prob < 0.3)
			return false;

		if (!$bAll)
			return self::StatVulnCheck($str, true);

		return true;
	}

	function Search($path)
	{
		$path = str_replace('\\','/',$path);
		do
		{
			$path = str_replace('//', '/', $path, $cnt);
		}
		while($cnt);

		if (time() - START_TIME > 10)
		{
			if (!defined('BREAK_POINT'))
				define('BREAK_POINT', $path);
			return;
		}

		if (defined('SKIP_PATH') && !defined('FOUND')) // проверим, годится ли текущий путь
		{
			if (0 !== self::bin_strpos(SKIP_PATH, dirname($path))) // отбрасываем имя или идём ниже 
				return;

			if (SKIP_PATH==$path) // путь найден, продолжаем искать текст
				define('FOUND',true);
		}

		if (is_dir($path)) // dir
		{
			$p = realpath($path);
			if (strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache') === 0
			|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/managed_cache') === 0
			|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/stack_cahe') === 0
			)
				return;

			if (is_link($path))
			{
				$d = dirname($path);
				if (strpos($p, $d) !== false || strpos($d, $p) !== false) // если симлинк ведет на папку внутри структуры сайта или на папку выше
					return true;
			}

			$dir = opendir($path);
			while($item = readdir($dir))
			{
				if ($item == '.' || $item == '..')
					continue;

				self::Search($path.'/'.$item);
			}
			closedir($dir);
		}
		else // file
		{
			if (!defined('SKIP_PATH') || defined('FOUND'))
				if ($res = self::CheckFile($path))
					self::Mark($path, $res);
		}
	}

	function SystemFile($f)
	{
		static $system = array(
			'/bitrix/modules/controller/install/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php',
			'/bitrix/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php',
			'/bitrix/modules/main/classes/general/update_class.php',
			'/bitrix/modules/main/classes/general/file.php',
			'/bitrix/modules/imconnectorserver/lib/connectors/telegrambot/emojiruleset.php',
			'/bitrix/modules/imconnectorserver/lib/connectors/facebook/emojiruleset.php',
		);
		foreach($system as $path)
			if (preg_match('#'.$path.'$#', $f))
				return true;
		return false;
	}

	function bin_strpos($s, $a)
	{
		if (function_exists('mb_orig_strpos'))
			return mb_orig_strpos($s, $a);
		return strpos($s, $a);
	}

	function Mark($f, $type)
	{
		if (false === file_put_contents(XSCAN_LOG, $f."\t".$type."\n", 8))
		{
			ShowError('Write error: '.XSCAN_LOG);
			die();
		}
	}

	function ShowMsg($str, $color = 'green')
	{
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => '',
			"DETAILS" => $str,
			"TYPE" => $color == 'green' ? "OK" : 'ERROR',
			"HTML" => true));
	}

	function HumanSize($s)
	{
		$i = 0;
		$ar = array('b','kb','M','G');
		while($s > 1024)
		{
			$s /= 1024;
			$i++;
		}
		return round($s,1).' '.$ar[$i];
	}

	function CheckBadLog()
	{
		if (file_exists(XSCAN_LOG))
		{
			CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED_FOUND"), 'red');
			echo GetMessage("BITRIX_XSCAN_DATA_IZMENENIA_JURNA").ConvertTimeStamp(filemtime(XSCAN_LOG), 'FULL');
			echo '<table width=80% border=1 style="border-collapse:collapse;border-color:#CCC">';
			echo '<tr>
				<th>'.GetMessage("BITRIX_XSCAN_NAME").'</th>
				<th>'.GetMessage("BITRIX_XSCAN_TYPE").'</th>
				<th>'.GetMessage("BITRIX_XSCAN_SIZE").'</th>
				<th>'.GetMessage("BITRIX_XSCAN_M_DATE").'</th>
				<th></th>
				</tr>';

			$ar = file(XSCAN_LOG);
			foreach($ar as $line)
			{
				list($f, $type) = explode("\t", $line);
				{
					$code = preg_match('#\[([0-9]+)\]#', $type, $regs) ? $regs[1] : 0;
					$fu = urlencode(trim($f));
					$bInPrison = strpos('[100]', $type) === false;

					if (!file_exists($f) && file_exists($new_f = preg_replace('#\.php$#', '.ph_', $f)))
					{
						$bInPrison = false;
						$f = $new_f;
						$fu = urlencode(trim($new_f));
					}

					echo '<tr>
						<td><a href="?action=showfile&file='.$fu.'" title="'.GetMessage("BITRIX_XSCAN_SRC").'" target=_blank>'.htmlspecialcharsbx(str_replace($_SERVER['DOCUMENT_ROOT'], '', $f)).'</a></td>
						<td>'.htmlspecialcharsbx($type).'</td>
						<td>'.CBitrixXscan::HumanSize(filesize($f)).'</td>
						<td>'.ConvertTimeStamp(filemtime($f), 'FULL').'</td>
						<td>'.($bInPrison ? '<a href="?action=prison&file='.$fu.'&'.bitrix_sessid_get().'" onclick="if(!confirm(\''.GetMessage("BITRIX_XSCAN_WARN").'\'))return false;" title="'.GetMessage("BITRIX_XSCAN_QUESTION").'">' : '').GetMessage("BITRIX_XSCAN_KARANTIN").'</a></td>
						</tr>';
				}
			}
			echo '</table>';
		}
	}
}
?>
