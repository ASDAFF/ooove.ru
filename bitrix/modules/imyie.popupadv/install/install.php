<?if(!check_bitrix_sessid()) return;?>
<?
IncludeModuleLangFile(__FILE__);
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
<br />
<form action="settings.php" method="get">
	<input type="hidden" name="mid" value="imyie.popupadv">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage("MOD_SETTINGS")?>">
</form>