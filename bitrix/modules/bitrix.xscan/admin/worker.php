<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

define('XSCAN_LOG', $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.xscan/file_list.txt');
define('START_TIME', time()); // засекаем время старта

$strError = '';

$APPLICATION->SetTitle(GetMessage("BITRIX_XSCAN_SEARCH"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$START_PATH = $_REQUEST['START_PATH'];
if (!$START_PATH)
	$START_PATH = $_SERVER['DOCUMENT_ROOT'];

$action = $_REQUEST['action'];
if ($f = $_REQUEST['file'])
{
	$str = file_get_contents(XSCAN_LOG);
	if (!file_exists($f))
	{
		CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_FILE_NOT_FOUND").htmlspecialcharsbx($f), 'red');
		die();
	}
	else
	{
		if ($action == 'prison')
		{
			if (check_bitrix_sessid())
			{
				$new_f = preg_replace('#\.php$#', '.ph_', $f);
				if (rename($f, $new_f))
					CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_RENAMED").htmlspecialcharsbx($new_f));
				else
					CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_ERR_RENAME").htmlspecialcharsbx($f), 'red');
			}
			else
				CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_SESSIA_USTARELA_OBN"), 'red');
		}
		elseif ($action == 'showfile')
		{
			echo '<h2>'.GetMessage("BITRIX_XSCAN_FAYL").htmlspecialcharsbx($f).'</h2>';

			$LAST_REG = '';
			if ($res = CBitrixXscan::CheckFile($f))
			{
				$code = preg_replace('#[^0-9]#', '', $res);

				$str = '';
				$read = false;
				$rs = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.xscan/include.php', 'rb');
				while(false !== $l = fgets($rs))
				{
					if ($read && preg_match('/# CODE /', $l))
						break;
					if (preg_match('/# CODE '.$code.'/', $l))
						$read = true;
					if ($read)
						$str .= $l;
				}
				fclose($rs);

				if ($str)
				{
					echo '<div>'.GetMessage("BITRIX_XSCAN_POCEMU_VYBRAN_ETOT_F").'</div>';
					highlight_string("<"."?\n".$str."\n");
				}

				if ($LAST_REG)
				{
					echo '<div>'.GetMessage("BITRIX_XSCAN_PODOZRITELQNYY_KOD").'</div>';
					echo '<div style="border:1px solid #CCC;padding:10px">'.nl2br(htmlspecialcharsbx($LAST_REG)).'</div>';
				}
			}
			else
				CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_FAYL_NE_VYGLADIT_POD"), 'green');

			$str = file_get_contents($f);
			highlight_string($str);
			die();
		}
	}
}

if (!is_dir($START_PATH))
	$strError = GetMessage("BITRIX_XSCAN_NACALQNYY_PUTQ_NE_NA");

if ($_REQUEST['go'] && !$strError)
{
	if ($_REQUEST['break_point'])
		define('SKIP_PATH',htmlspecialcharsbx($_REQUEST['break_point'])); // промежуточный путь
	elseif (file_exists(XSCAN_LOG))
		unlink(XSCAN_LOG);

	CBitrixXscan::Search($START_PATH);
	if (defined('BREAK_POINT'))
	{
		?><form method=post id=postform action=?>
		<input type=hidden name=START_PATH value="<?=htmlspecialcharsbx($START_PATH)?>">
		<input type=hidden name=go value=Y>
		<input type=hidden name=break_point value="<?=htmlspecialcharsbx(BREAK_POINT)?>">
		</form>
		<?
		CBitrixXscan::ShowMsg('<b>'.GetMessage("BITRIX_XSCAN_IN_PROGRESS").'...</b><br>
		'.GetMessage("BITRIX_XSCAN_CURRENT_FILE").': <i>'.htmlspecialcharsbx(BREAK_POINT).'</i>');
		?>
		<script>window.setTimeout("document.getElementById('postform').submit()",500);</script><? // таймаут чтобы браузер показал текст
	}
	elseif (!file_exists(XSCAN_LOG))
		CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED"));
}
else
{
	if ($strError)
		CBitrixXscan::ShowMsg($strError, 'red');
	?><form method=post action=?>
		<?=GetMessage("BITRIX_XSCAN_NACALQNYY_PUTQ")?><input name=START_PATH value="<?=htmlspecialcharsbx($START_PATH)?>" size=60>
		<input type=submit name=go value="<?=GetMessage("BITRIX_XSCAN_START_SCAN")?>">
	</form><?
}

CBitrixXscan::CheckBadLog();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
?>
