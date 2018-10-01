<?if(!check_bitrix_sessid()) return;?>
<?

if ($GLOBALS['as_error']) {
	CAdminMessage::ShowMessage($GLOBALS['as_error']);
}

echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<p><?=GetMessage('AS_MODULE_TRUEBANNER_INSTALL_FINISHED');?></p>
<form action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
<form>