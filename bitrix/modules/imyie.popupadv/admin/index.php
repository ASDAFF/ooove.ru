<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

$yaPage = "imyie_popupadv.php";
$POPUPADVModule_bannerCounts = 3;

CModule::IncludeModule('imyie.popupadv');
CModule::IncludeModule('iblock');

/* _____________________________ POST part _____________________________ */
//echo"<pre>";print_r($_REQUEST);echo"</pre><br />";
if($_REQUEST["tabControl_active_tab"]=="imyie_popupadv_module")
{
	$arSettings = array();
	for($i=1;$i<($POPUPADVModule_bannerCounts+1);$i++)
	{
		$arSettings["BANNERS"][$i] = array(
			"ACTIVE" => htmlspecialchars($_REQUEST["popupadv_active".$i]),
			"IMAGE" => $_REQUEST["popupadv_image".$i],
			"LINK" => $_REQUEST["popupadv_link".$i],
			"TARGET" => htmlspecialchars($_REQUEST["popupadv_target".$i]),
			"PROFESSIONAL" => ($_REQUEST["popupadv_probanner".$i]),
			"PROFESSIONAL_TYPE" => ($_REQUEST["popupadv_probanner_type".$i]),
		);
	}
	foreach($arSettings["BANNERS"] as $key => $val)
	{
		CIMYIEPopupAdvModule::Update( $key, $val );
	}
} elseif($_REQUEST["tabControl_active_tab"]=="imyie_popupadv_iblock")
{
	COption::SetOptionInt("imyie.popupadv", "iblock_id", htmlspecialchars($_REQUEST["IBLOCK_ID"]));
	COption::SetOptionString("imyie.popupadv", "sort_type", htmlspecialchars($_REQUEST["sort_type"]));
	if(isset($_REQUEST["install_demo_iblock"]))
	{
		CIMYIEPopupAdvIBlock::InstallDEMO();
	}
} elseif($_REQUEST["tabControl_active_tab"]=="imyie_popupadv_advestering")
{
	
}

// сформируем список закладок
$aTabs = array(
	array(
		"DIV" => "imyie_popupadv_module",
		"TAB" => GetMessage("IMYIE_POPUPADV_TAB1_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("IMYIE_POPUPADV_TAB1_DESCRIPTION")
	),
	array(
		"DIV" => "imyie_popupadv_iblock",
		"TAB" => GetMessage("IMYIE_POPUPADV_TAB2_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("IMYIE_POPUPADV_TAB2_DESCRIPTION")
	),
	/*array(
		"DIV" => "imyie_popupadv_advestering",
		"TAB" => GetMessage("IMYIE_POPUPADV_TAB3_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("IMYIE_POPUPADV_TAB3_DESCRIPTION")
	),*/
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

// установим заголовок страницы
$APPLICATION->SetTitle( GetMessage("IMYIE_POPUPADV_PAGE_TITLE") );

// не забудем разделить подготовку данных и вывод
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");



// далее выводим собственно форму
?>
<form id="imyie_popupadv" method="POST" action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="imyie_popupadv">
<?

// проверка идентификатора сессии
echo bitrix_sessid_post();

// отобразим заголовки закладок
$tabControl->Begin();





//___________________________________________________________________________________________
// первая закладка - 
//___________________________________________________________________________________________
$tabControl->BeginNextTab();
$data_sim_or_pro = COption::GetOptionString("imyie.popupadv", "data_sim_or_pro", "simple");
?>
	<?for($i=1;$i<($POPUPADVModule_bannerCounts+1);$i++):
	$settings = CIMYIEPopupAdvModule::GetByID( $i );
	?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_POPUPADV_BANNER")?> <?=$i?></td>
	</tr>
	<tr>
		<td width="40%" valign="top"><?=GetMessage("IMYIE_POPUPADV_AVTIVITY")?>:</td>
		<td width="60%">
			<input type="checkbox" id="popupadv_active<?=$i?>" name="popupadv_active<?=$i?>" value="Y"<?if($settings["ACTIVE"]=="Y"):?> checked <?endif;?>>
		</td>
	</tr>
	<?if($data_sim_or_pro=="simple"):?>
		<tr>
			<td width="40%" valign="top"><span class="required">*</span> <?=GetMessage("IMYIE_POPUPADV_IMAGES")?>:</td>
			<td width="60%">
				<input type="text" id="popupadv_image<?=$i?>" name="popupadv_image<?=$i?>" size="50" value="<?=$settings["IMAGE"]?>">
				<input type="button" value=" <?=GetMessage("IMYIE_POPUPADV_FILEDIALOG")?> " OnClick="BtnFileDialogOpen<?=$i?>()">
				<?
				CAdminFileDialog::ShowScript(
					Array(
						"event" => "BtnFileDialogOpen".$i,
						"arResultDest" => array("FORM_NAME" => "imyie_popupadv", "FORM_ELEMENT_NAME" => "popupadv_image".$i),
						"arPath" => array("SITE" => SITE_ID, "PATH" => ""),
						"select" => 'F',// F - file only, D - folder only
						"operation" => 'O',// O - open, S - save
						"showUploadTab" => true,
						"showAddToMenuTab" => false,
						"fileFilter" => 'jpg,jpeg,png,gif',
						"allowAllFiles" => true,
						"SaveConfig" => true,
					)
				);
				?>
			</td>
		</tr>
		<tr>
			<td width="40%" valign="top"><sup><span class="required">1</span></sup> <?=GetMessage("IMYIE_POPUPADV_LINKS")?>:</td>
			<td width="60%">
				<input type="text" id="popupadv_link<?=$i?>" name="popupadv_link<?=$i?>" size="50" value="<?=$settings["LINK"]?>">
			</td>
		</tr>
		<tr>
			<td width="40%" valign="top"><?=GetMessage("IMYIE_POPUPADV_TARGET")?>:</td>
			<td width="60%">
				<input type="checkbox" id="popupadv_target<?=$i?>" name="popupadv_target<?=$i?>" value="Y"<?if($settings["TARGET"]=="Y"):?> checked <?endif;?>>
			</td>
		</tr>
	<?else:?>
		<tr>
			<td width="40%" valign="top"><?=GetMessage("IMYIE_POPUPADV_BANNER_PROFESSIONAL")?>:</td>
			<td width="60%">
				<?/*?><textarea name="popupadv_probanner<?=$i?>"><?=$settings["PROFESSIONAL"]?></textarea><?*/?>
				<?CFileMan::AddHTMLEditorFrame("popupadv_probanner".$i, $settings["PROFESSIONAL"], "popupadv_probanner_type".$i, $settings["PROFESSIONAL_TYPE"], 100);?>
			</td>
		</tr>
	<?endif;?>
	<?endfor;?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?





//___________________________________________________________________________________________
// вторая закладка - 
//___________________________________________________________________________________________
$iblock_isset = false;
$arSortType = array(
	"RAND" => GetMessage("IMYIE_POPUPADV_BANNER_SORT_TYPE1"),
	"SORT" => GetMessage("IMYIE_POPUPADV_BANNER_SORT_TYPE2"),
);
$IBLOCK_ID = COption::GetOptionInt("imyie.popupadv", "iblock_id");
if($IBLOCK_ID>0)
{
	$res = CIBlock::GetByID( $IBLOCK_ID );
	if($ar_res = $res->GetNext())
		$iblock_isset = true;
}
$sort_type = COption::GetOptionString("imyie.popupadv", "sort_type", "RAND");
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%" valign="top"><span class="required">*</span> <?=GetMessage("IMYIE_POPUPADV_IBLOCK_ID")?>:</td>
		<td width="60%"><?=GetIBlockDropDownList($IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID');?></td>
	</tr>
	<tr>
		<td width="40%" valign="top"><?=GetMessage("IMYIE_POPUPADV_BANNER_SORT")?>:</td>
		<td width="60%">
			<select name="sort_type">
			<?foreach($arSortType as $optionKey => $optionValue):?>
				<option value="<?=$optionKey?>"<?if($sort_type==$optionKey):?> selected <?endif;?>><?=$optionValue?></value>
			<?endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top"> </td>
		<td width="60%">
			<input type="submit" name="install_demo_iblock" value="<?=GetMessage("IMYIE_POPUPADV_INSTALL_BTN_IBLOCK")?>"<?if($iblock_isset):?> disabled<?endif;?> />
			<?if($IBLOCK_ID>0 && $iblock_isset):?><a href="iblock_list_admin.php?IBLOCK_ID=<?=$IBLOCK_ID?>&type=popupadv&lang=ru&filter_section=-1"><?=GetMessage("IMYIE_POPUPADV_BTN_GO_IBLOCK")?></a><?endif;?>
		</td>
	</tr>
<input type="hidden" name="lang" value="<?=LANG?>">
<?




/*
//___________________________________________________________________________________________
// третья закладка - 
//___________________________________________________________________________________________
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%" valign="top"><span class="required">*</span> 11111111111:</td>
		<td width="60%">222222222222</td>
	</tr>
<input type="hidden" name="lang" value="<?=LANG?>">
<?
*/





// завершение формы - вывод кнопок сохранения изменений
$tabControl->Buttons();?>
<input type="submit" name="save" value="<?=GetMessage("IMYIE_POPUPADV_BOTTONS_SAVE_VALUE")?>" title="<?=GetMessage("IMYIE_POPUPADV_BOTTONS_SAVE_VALUE")?>">
<?


// завершаем интерфейс закладок
$tabControl->End();

// информационная подсказка
echo BeginNote();?>
<span class="required">*</span><?=GetMessage("REQUIRED_FIELDS")?><br />
<sup><span class="required">1</span></sup><?=GetMessage("IMYIE_POPUPADV_NOTE1")?><br />
<?echo EndNote();

$dtype = COption::GetOptionString("imyie.popupadv", "data_type", "data_module");
?>
<script>
tabControl.SelectTab('<?if($dtype=="data_module"):?>imyie_popupadv_module<?elseif($dtype=="data_iblock"):?>imyie_popupadv_iblock<?elseif($dtype=="data_advertising"):?>imyie_popupadv_advestering<?endif;?>');
tabControl.<?if($dtype=="data_module"):?>EnableTab<?else:?>DisableTab<?endif;?>('imyie_popupadv_module');
tabControl.<?if($dtype=="data_iblock"):?>EnableTab<?else:?>DisableTab<?endif;?>('imyie_popupadv_iblock');
tabControl.<?if($dtype=="data_advertising"):?>EnableTab<?else:?>DisableTab<?endif;?>('imyie_popupadv_advestering');
</script>
<?

// завершение страницы
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
