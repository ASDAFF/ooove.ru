<?if(!$USER->IsAdmin())	return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('imyie.popupadv');

if(isset($_REQUEST["save"]) && $_REQUEST["tabControl_active_tab"]=="imyie_tab1")
{
	$arErrors = array();
	///////////////////////////
	$dbRes = CIMYIEPPADVPopupAdv::GetList(array("ID"=>"ASC"),array());
	$bn_cnt = $dbRes->SelectedRowsCount();
	///////////////////////////
	$onoff = htmlspecialchars($_REQUEST["onoff"]);
	$onoff == "Y" ? $onoff == "Y" : $onoff == "N";
	if($onoff=="Y")
		RegisterModuleDependences("main", "OnEpilog", "imyie.popupadv", "CIMYIEPPADVShower", "HandlerOnProlog");
	else
		UnRegisterModuleDependences("main", "OnEpilog", "imyie.popupadv", "CIMYIEPPADVShower", "HandlerOnProlog");
	$show_close = htmlspecialchars($_REQUEST["show_close"]);
	$show_close == "Y" ? $show_close == "Y" : $show_close == "N";
	$close_overlay = htmlspecialchars($_REQUEST["close_overlay"]);
	$close_overlay == "Y" ? $close_overlay == "Y" : $close_overlay == "N";
	$sesscook = htmlspecialchars($_REQUEST["sesscook"]);
	$sesscook == "session" ? $sesscook == "session" : $sesscook == "cookie";
	COption::SetOptionString("imyie.popupadv", "onoff", $onoff);
	$data_type = COption::GetOptionInt("imyie.popupadv", "data_type");
	if($data_type==$_REQUEST["data_type"])
	{
		COption::SetOptionInt("imyie.popupadv", "data_type", IntVal($_REQUEST["data_type"]));
	} elseif($data_type!=$_REQUEST["data_type"] && $bn_cnt<1)
	{
		COption::SetOptionInt("imyie.popupadv", "data_type", IntVal($_REQUEST["data_type"]));
	} else {
		$arErrors[] = GetMessage("CANT_CHANGE_DATA_TYPE");
	}
	COption::SetOptionString("imyie.popupadv", "sesscook", $sesscook);
	COption::SetOptionInt("imyie.popupadv", "timeinterval", IntVal($_REQUEST["timeinterval"]));
	COption::SetOptionString("imyie.popupadv", "show_close", $show_close);
	COption::SetOptionString("imyie.popupadv", "close_overlay", $close_overlay);
	COption::SetOptionString("imyie.popupadv", "img_close_path", $_REQUEST["img_close_path"]);
	COption::SetOptionInt("imyie.popupadv", "img_close_width", IntVal($_REQUEST["img_close_width"]) );
	COption::SetOptionInt("imyie.popupadv", "img_close_height", IntVal($_REQUEST["img_close_height"]) );
	COption::SetOptionString("imyie.popupadv", "img_overlay_path", $_REQUEST["img_overlay_path"]);
	COption::SetOptionString("imyie.popupadv", "addjquery", ($_REQUEST["addjquery"]=="Y" ? "Y" : "N"));
	if(count($arErrors)<1)
		LocalRedirect( 'settings.php?lang='.LANG.'&mid=imyie.popupadv' );
}

$aTabs = array(
	array("DIV" => "imyie_tab1", "TAB" => GetMessage("IMYIE_POPUPADV_SETTINGS"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_POPUPADV_SETTINGS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arrDataType = array(
	"1" => GetMessage("IMYIE_PARAM_DATA_TYPE_1"),
	"2" => GetMessage("IMYIE_PARAM_DATA_TYPE_2"),
	"3" => GetMessage("IMYIE_PARAM_DATA_TYPE_3"),
);
$arrSessCook = array(
	"session" => GetMessage("IMYIE_PARAM_SESSCOOK_SESSION"),
	"cookie" => GetMessage("IMYIE_PARAM_SESSCOOK_COOKIE"),
);
$arrSessCookValues = array(
	"" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_"),
	"1800" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_1800"),
	"3600" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_3600"),
	"7200" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_7200"),
	"10800" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_10800"),
	"18000" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_18000"),
	"86400" => GetMessage("IMYIE_PARAM_TIMEINTERVAL_86400"),
);
?>

<?
if(count($arErrors)>0)
{
	CAdminMessage::ShowMessage( implode('<br />', $arErrors) );
}
?>

<?$tabControl->Begin();?>

<script>
function timeChanged(val)
{
	if(val>0)
		document.getElementById('timeinterval').value = val;
}
</script>
<form name="imyie_popupadv_settings" method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
<?
$onoff = COption::GetOptionString("imyie.popupadv", "onoff");
$data_type = COption::GetOptionInt("imyie.popupadv", "data_type");
$sesscook = COption::GetOptionString("imyie.popupadv", "sesscook");
$timeinterval = COption::GetOptionInt("imyie.popupadv", "timeinterval");
$show_close = COption::GetOptionString("imyie.popupadv", "show_close");
$close_overlay = COption::GetOptionString("imyie.popupadv", "close_overlay");
$img_close_path = COption::GetOptionString("imyie.popupadv", "img_close_path");
$img_close_width = COption::GetOptionInt("imyie.popupadv", "img_close_width");
$img_close_height = COption::GetOptionInt("imyie.popupadv", "img_close_height");
$img_overlay_path = COption::GetOptionString("imyie.popupadv", "img_overlay_path");
$addjquery = COption::GetOptionString("imyie.popupadv", "addjquery");
?>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_ONOFF")?>:</td>
		<td valign="top" width="50%"><input type="checkbox" name="onoff" value="Y"<?if($onoff=="Y"):?> checked="checked"<?endif;?> /></td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_DATA_TYPE")?>:</td>
		<td valign="top" width="50%">
			<select name="data_type">
			<?foreach($arrDataType as $id => $type):?>
				<option value="<?=$id?>"<?if($id==$data_type):?> selected <?endif;?>><?=$type?></option>
			<?endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_SESSCOOK")?>:</td>
		<td valign="top" width="50%">
			<select name="sesscook">
			<?foreach($arrSessCook as $id => $type):?>
				<option value="<?=$id?>"<?if($id==$sesscook):?> selected <?endif;?>><?=$type?></option>
			<?endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_TIMEINTERVAL")?>:</td>
		<td valign="top" width="50%">
			<input type="text" name="timeinterval" id="timeinterval" value="<?=$timeinterval?>" />
			<select name="timeinterval_periods" onchange="timeChanged(this.value);">
			<?foreach($arrSessCookValues as $sec => $name):?>
				<option value="<?=$sec?>"<?if($sec==$timeinterval):?> selected <?endif;?>><?=$name?></option>
			<?endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_SHOW_CLOSE")?>:</td>
		<td valign="top" width="50%"><input type="checkbox" name="show_close" value="Y"<?if($show_close=="Y"):?> checked="checked"<?endif;?> /></td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_CLOSE_OVERLAY")?>:</td>
		<td valign="top" width="50%"><input type="checkbox" name="close_overlay" value="Y"<?if($close_overlay=="Y"):?> checked="checked"<?endif;?> /></td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_CLOSE_IMG_PATH")?></td>
		<td>
			<input type="text" name="img_close_path" value="<?=$img_close_path?>" />
			<input type="button" value="<?=GetMessage("IMYIE_BTN_FILEDIALOG")?>" OnClick="BtnFileDialogImgClose()">
			<?
				CAdminFileDialog::ShowScript(
					Array(
						"event" => "BtnFileDialogImgClose",
						"arResultDest" => array("FORM_NAME" => "imyie_popupadv_settings", "FORM_ELEMENT_NAME" => "img_close_path"),
						"arPath" => array("SITE" => SITE_ID, "PATH" => ""),
						"select" => 'F',// F - file only, D - folder only
						"operation" => 'O',// O - open, S - save
						"showUploadTab" => true,
						"showAddToMenuTab" => false,
						"fileFilter" => 'jpg,png,jpeg,gif',
						"allowAllFiles" => true,
						"SaveConfig" => true,
					)
				);
			?>
			<br /><br />
			<?=GetMessage("IMYIE_NOTE_CLOSE_IMAGE")?>: <input type="text" name="img_close_width" value="<?=$img_close_width?>" size="5" />px &nbsp; <input type="text" name="img_close_height" value="<?=$img_close_height?>" size="5" /> px
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_PARAM_OVERLAY_IMG_PATH")?></td>
		<td>
			<input type="text" name="img_overlay_path" value="<?=$img_overlay_path?>" />
			<input type="button" value="<?=GetMessage("IMYIE_BTN_FILEDIALOG")?>" OnClick="BtnFileDialogImgOverlay()">
			<?
				CAdminFileDialog::ShowScript(
					Array(
						"event" => "BtnFileDialogImgOverlay",
						"arResultDest" => array("FORM_NAME" => "imyie_popupadv_settings", "FORM_ELEMENT_NAME" => "img_overlay_path"),
						"arPath" => array("SITE" => SITE_ID, "PATH" => ""),
						"select" => 'F',// F - file only, D - folder only
						"operation" => 'O',// O - open, S - save
						"showUploadTab" => true,
						"showAddToMenuTab" => false,
						"fileFilter" => 'jpg,png,jpeg,gif',
						"allowAllFiles" => true,
						"SaveConfig" => true,
					)
				);
			?>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_ADD_JQUERY")?>:</td>
		<td valign="top" width="50%"><input type="checkbox" name="addjquery" value="Y"<?if($addjquery=="Y"):?> checked="checked"<?endif;?> /></td>
	</tr>
<?$tabControl->Buttons();?>
	<input type="submit" name="save" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<!--input type="submit" name="RestoreDefaults" value="<?=GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" title="<?=GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>"-->
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>