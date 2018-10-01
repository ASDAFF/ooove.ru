<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('imyie.popupadv');

if($_REQUEST["tabControl_active_tab"]=="imyie_popupadv")
{
	//echo"<pre>";print_r($_REQUEST);echo"</pre>";
	if(isset($_REQUEST["cancel"]))
		LocalRedirect( 'imyie_popupadv.php?lang='.LANG );
	$ID = CIMYIEPPADVUtils::SaveUpdateContent();
	if(!$ID)
	{
		$arErrors[] = GetMessage("IMYIE_ERROR_ADD_UPDATE");
	} else {
		if(isset($_REQUEST["save"]) && $ID>0)
			LocalRedirect( 'imyie_popupadv.php?lang='.LANG );
		elseif(isset($_REQUEST["apply"]) && $ID>0)
			LocalRedirect( 'imyie_popupadv_edit.php?ID='.$ID.'&lang='.LANG );
	}
}

// tabs list
$aTabs = array(
	array(
		"DIV" => "imyie_popupadv",
		"TAB" => GetMessage("IMYIE_TAB1_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("IMYIE_POPUPADV_TAB1_DESCRIPTION")
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

// context menu
$aContext = array(
	array(
		"TEXT" => GetMessage("IMYIE_CONTEXT_MENU_LIST"),
		"LINK" => "imyie_popupadv.php?lang=".LANG,
		"TITLE" => "",
		"ICON" => "btn_list",
	),
);
$oMenu = new CAdminContextMenu($aContext);

// set page title
$APPLICATION->SetTitle( GetMessage("IMYIE_POPUPADV_PAGE_TITLE") );

// include prolog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// show errors
if(count($arErrors)>0)
{
	CAdminMessage::ShowMessage( implode('<br />', $arErrors) );
}

// show context menu
$oMenu->Show();

// show form
?>
<form id="imyie_popupadv" method="POST" action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="imyie_popupadv">
<?

// sessid_id checker
echo bitrix_sessid_post();

// tabs header
$tabControl->Begin();





//___________________________________________________________________________________________
// tab
//___________________________________________________________________________________________
if(IntVal($_REQUEST["ID"])>0)
{
	$dbRes = CIMYIEPPADVPopupAdv::GetByID(IntVal($_REQUEST["ID"]));
	$arBanner = $dbRes->Fetch();
}
$tabControl->BeginNextTab();
?>
<?if($arBanner["ID"]>0):?>
<input type="hidden" name="ID" value="<?=$arBanner["ID"]?>" />
<?endif;?>
	<tr>
		<td width="40%" valign="top" align="right"><?=GetMessage("IMYIE_ACTIVE")?>:</td>
		<td width="60%"><input type="checkbox" name="active" value="Y"<?if(($arBanner["ID"]>0 && $arBanner["ACTIVE"]=="Y") || IntVal($_REQUEST["ID"])<1):?> checked="checked"<?endif;?> /></td>
	</tr>
	<tr>
		<td width="40%" valign="top" align="right"><span class="required">*</span><?=GetMessage("IMYIE_NAME")?>:</td>
		<td width="60%"><input type="text" name="name" value="<?=$arBanner["NAME"]?>" /></td>
	</tr>
	<tr>
		<td width="40%" valign="top" align="right"><span class="required">*</span><?=GetMessage("IMYIE_PRIORITY")?>:</td>
		<td width="60%"><input type="text" name="priority" value="<?=$arBanner["PRIORITY"]?>" /><?=GetMessage("IMYIE_PRIORITY_NOTE")?></td>
	</tr>
	<tr>
		<td width="40%" valign="top" align="right"><span class="required">*</span><?=GetMessage("IMYIE_CONTENT")?>:</td>
		<td width="60%"><?CIMYIEPPADVUtils::ShowEditor($arBanner["CONTENT"])?></td>
	</tr>
<input type="hidden" name="lang" value="<?=LANG?>">







<?
// tab bottons
$tabControl->Buttons();?>
<input type="submit" name="save" value="<?=GetMessage("IMYIE_BUTTON_SAVE")?>">
<input type="submit" name="apply" value="<?=GetMessage("IMYIE_BUTTON_APPLY")?>">
<input type="submit" name="cancel" value="<?=GetMessage("IMYIE_BUTTON_CANCEL")?>">
<?
// tab footer
$tabControl->End();

// note
echo BeginNote();?>
<span class="required">*</span><?=GetMessage("REQUIRED_FIELDS")?><br />
<?echo EndNote();

// include epilog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>