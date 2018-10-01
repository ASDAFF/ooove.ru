<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('imyie.popupadv');

if($_REQUEST["action"]=="delete" && $_REQUEST["delete"]=="Y" && IntVal($_REQUEST["ID"])>0)
{
	CIMYIEPPADVPopupAdv::Delete(IntVal($_REQUEST["ID"]));
}

$sTableID = "imyie_popupadv_list";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilter = array();
$rsData = CIMYIEPPADVPopupAdv::GetList(array($by=>$order),$arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText( $rsData->GetNavPrint( GetMessage("IMYIE_PAGE_NAVI") ) );

$lAdmin->AddHeaders(
	array(
		array("id" => "ID", "content" => GetMessage("IMYIE_LIST_HEADER_ID"), "sort" => "id", "default" => true),
		array("id" => "NAME", "content" => GetMessage("IMYIE_LIST_HEADER_NAME"), "sort" => "name", "default" => true),
		array("id" => "ACTIVE", "content" => GetMessage("IMYIE_LIST_HEADER_ACTIVE"), "sort" => "active", "default" => true),
		array("id" => "PRIORITY", "content" => GetMessage("IMYIE_LIST_HEADER_PRIORITY"), "sort" => "priority", "default" => true),
	)
);

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);
	$row->AddViewField("ID", '<a href="imyie_popupadv_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>' );
	$row->AddViewField("NAME", '<a href="imyie_popupadv_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>' );
	if($f_ACTIVE=="Y")
		$row->AddViewField("ACTIVE", GetMessage("IMYIE_LIST_ACTIVE_Y") );
	else
		$row->AddViewField("ACTIVE", GetMessage("IMYIE_LIST_ACTIVE_N") );
	$row->AddField("PRIORITY", $f_PRIORITY );
	$arActions = Array();
	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => true,
		"TEXT" => GetMessage("IMYIE_ACTION_EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("imyie_popupadv_edit.php?ID=".$f_ID."&lang=".LANG)
	);
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT" => false,
		"TEXT" => GetMessage("IMYIE_ACTION_DELETE"),
		"ACTION" => "if(confirm('".GetMessage("IMYIE_ACTION_DELETE_OK")."')) ".$lAdmin->ActionRedirect("imyie_popupadv.php?ID=".$f_ID."&delete=Y&action=delete&lang=".LANG)
	);
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title" => GetMessage("IMYIE_LIST_FOOTER_COUNT"),"value" => $rsData->SelectedRowsCount()),
	)
);

$aContext = array(
	array(
		"TEXT" => GetMessage("IMYIE_CONTEXT_ADD"),
		"LINK" => "imyie_popupadv_edit.php?lang=".LANG,
		"TITLE" => "",
		"ICON" => "btn_new",
	),
	array(
		"TEXT" => GetMessage("IMYIE_CONTEXT_SETTINGS"),
		"LINK" => "settings.php?mid=imyie.popupadv&lang=".LANG,
		"TITLE" => "",
		"ICON" => "",
	),
);

// add context menu to table
$lAdmin->AddAdminContextMenu($aContext,false,false);
$lAdmin->CheckListMode();

// set page title
$APPLICATION->SetTitle( GetMessage("IMYIE_PAGE_TITLE") );

// include prolog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// show table
$lAdmin->DisplayList();

// include epilog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>