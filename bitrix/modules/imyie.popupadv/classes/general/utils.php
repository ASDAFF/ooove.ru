<?
IncludeModuleLangFile(__FILE__);

class CIMYIEPPADVUtils
{
	function ShowEditor($CONTENT)
	{
		$data_type = COption::GetOptionInt("imyie.popupadv", "data_type",1);
		switch($data_type){
			case 1:
				$cont = unserialize($CONTENT);
				echo '<input type="hidden" name="data_type" value="1" />';
				echo '<input type="text" name="content_banner" value="'.$cont["content_banner"].'" /> ';
				echo '<input type="button" value="'.GetMessage("IMYIE_BTN_FILEDIALOG").'" OnClick="BtnFileDialogOpen301()">';
				CAdminFileDialog::ShowScript(
					Array(
						"event" => "BtnFileDialogOpen301",
						"arResultDest" => array("FORM_NAME" => "imyie_popupadv", "FORM_ELEMENT_NAME" => "content_banner"),
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
				echo ''.GetMessage("DATA_TYPE_1_BANNER").'<br /><br />';
				echo '<input type="text" name="url" value="'.$cont["url"].'" /> '.GetMessage("DATA_TYPE_1_URL").'<br /><br />';
				echo '<label><input type="checkbox" name="blank" value="Y"';
				if($cont["blank"]=="Y") echo ' checked="checked"';
				echo ' /> '.GetMessage("DATA_TYPE_1_BLANK").'</label>';
				break;
			case 2:
				echo '<input type="hidden" name="data_type" value="2" />';
				$cont = unserialize($CONTENT);
				CFileMan::AddHTMLEditorFrame(
					"CONTENT",
					$cont["CONTENT"],
					"CONTENT_TYPE",
					$cont["CONTENT_TYPE"],
					array("height" => 350),
					"N",
					0,
					"",
					"",
					false,
					true,
					false,
					array()
				);
				break;
			case 3:
				$cont = unserialize($CONTENT);
				echo '<input type="hidden" name="data_type" value="3" />';
				echo '<input type="text" name="content_banner" value="'.$cont["CONTENT"].'" /> ';
				echo '<input type="button" value="'.GetMessage("IMYIE_BTN_FILEDIALOG").'" OnClick="BtnFileDialogOpen301()">';
				CAdminFileDialog::ShowScript(
					Array(
						"event" => "BtnFileDialogOpen301",
						"arResultDest" => array("FORM_NAME" => "imyie_popupadv", "FORM_ELEMENT_NAME" => "content_banner"),
						"arPath" => array("SITE" => SITE_ID, "PATH" => ""),
						"select" => 'F',// F - file only, D - folder only
						"operation" => 'O',// O - open, S - save
						"showUploadTab" => false,
						"showAddToMenuTab" => false,
						"fileFilter" => 'php',
						"allowAllFiles" => true,
						"SaveConfig" => true,
					)
				);
				echo ''.GetMessage("DATA_TYPE_3_INCLUDE").'<br />';
				break;
		}
	}
	
	function SetDefaultSettings()
	{
		COption::SetOptionString("imyie.popupadv", "onoff", "N");
		COption::SetOptionInt("imyie.popupadv", "data_type", 1);
		COption::SetOptionString("imyie.popupadv", "sesscook", "session");
		COption::SetOptionInt("imyie.popupadv", "timeinterval", 7200);
		COption::SetOptionString("imyie.popupadv", "show_close", "N");
		COption::SetOptionString("imyie.popupadv", "close_overlay", "Y");
		COption::SetOptionString("imyie.popupadv", "img_close_path", "/bitrix/images/imyie.popupadv/imyie-popupadv-close.png");
		COption::SetOptionInt("imyie.popupadv", "img_close_width", 38 );
		COption::SetOptionInt("imyie.popupadv", "img_close_height", 38 );
		COption::SetOptionString("imyie.popupadv", "img_overlay_path", "/bitrix/images/imyie.popupadv/imyie-popupadv-overlay.png");
	}
	
	function SaveUpdateContent()
	{
		$data_type = COption::GetOptionInt("imyie.popupadv", "data_type");
		switch($data_type){
			case 1:
				return CIMYIEPPADVUtils::_SaveContentOne();
				break;
			case 2:
				return CIMYIEPPADVUtils::_SaveContentTwo();
				break;
			case 3:
				return CIMYIEPPADVUtils::_SaveContentThree();
				break;
		}
	}
	
	function _SaveContentOne()
	{
		if(IntVal($_REQUEST["ID"])<1 && $_REQUEST["content_banner"]!="")
		{
			$arContent = array(
				"content_banner" => $_REQUEST["content_banner"],
				"url" => $_REQUEST["url"],
				"blank" => $_REQUEST["blank"]=="Y"?"Y":"N",
			);
			$content = serialize($arContent);
			$priority = ( IntVal($_REQUEST["priority"])>100 ? 100 : $_REQUEST["priority"] );
			$priority = ( IntVal($_REQUEST["priority"])<10 ? 10 : $_REQUEST["priority"] );
			$arFields = array(
				"ACTIVE" => $_REQUEST["active"]=="Y"?"Y":"N",
				"NAME" => htmlspecialchars($_REQUEST["name"]),
				"PRIORITY" => $priority,
				"CONTENT" => $content,
			);
			$ID = CIMYIEPPADVPopupAdv::Add($arFields);
			return $ID;
		} elseif($_REQUEST["content_banner"]!="") {
			$ID = IntVal($_REQUEST["ID"]);
			$arContent = array(
				"content_banner" => $_REQUEST["content_banner"],
				"url" => $_REQUEST["url"],
				"blank" => $_REQUEST["blank"]=="Y"?"Y":"N",
			);
			$content = serialize($arContent);
			$priority = ( IntVal($_REQUEST["priority"])>100 ? 100 : $_REQUEST["priority"] );
			$priority = ( IntVal($_REQUEST["priority"])<10 ? 10 : $_REQUEST["priority"] );
			$arFields = array(
				"ACTIVE" => $_REQUEST["active"]=="Y"?"Y":"N",
				"NAME" => htmlspecialchars($_REQUEST["name"]),
				"PRIORITY" => $priority,
				"CONTENT" => $content,
			);
			CIMYIEPPADVPopupAdv::Update($ID,$arFields);
			return $ID;
		} else {
			return FALSE;
		}
	}
	
	function _SaveContentTwo()
	{
		if(IntVal($_REQUEST["ID"])<1)
		{
			if($_REQUEST["CONTENT_TYPE"]=="text")
				$data = htmlspecialchars($_REQUEST["CONTENT"]);
			else
				$data = $_REQUEST["CONTENT"];
			$content = serialize(array("CONTENT"=>$data,"CONTENT_TYPE"=>$_REQUEST["CONTENT_TYPE"]=="text"?"text":"html"));
			$priority = ( IntVal($_REQUEST["priority"])>100 ? 100 : $_REQUEST["priority"] );
			$priority = ( IntVal($_REQUEST["priority"])<10 ? 10 : $_REQUEST["priority"] );
			$arFields = array(
				"ACTIVE" => $_REQUEST["active"]=="Y"?"Y":"N",
				"NAME" => htmlspecialchars($_REQUEST["name"]),
				"PRIORITY" => $priority,
				"CONTENT" => $content,
			);
			$ID = CIMYIEPPADVPopupAdv::Add($arFields);
			return $ID;
		} else {
			$ID = IntVal($_REQUEST["ID"]);
			if($_REQUEST["CONTENT_TYPE"]=="text")
				$data = htmlspecialchars($_REQUEST["CONTENT"]);
			else
				$data = $_REQUEST["CONTENT"];
			$content = serialize(array("CONTENT"=>$data,"CONTENT_TYPE"=>$_REQUEST["CONTENT_TYPE"]=="text"?"text":"html"));
			$priority = ( IntVal($_REQUEST["priority"])>100 ? 100 : $_REQUEST["priority"] );
			$priority = ( IntVal($_REQUEST["priority"])<10 ? 10 : $_REQUEST["priority"] );
			$arFields = array(
				"ACTIVE" => $_REQUEST["active"]=="Y"?"Y":"N",
				"NAME" => htmlspecialchars($_REQUEST["name"]),
				"PRIORITY" => $priority,
				"CONTENT" => $content,
			);
			CIMYIEPPADVPopupAdv::Update($ID,$arFields);
			return $ID;
		}
	}
	
	function _SaveContentThree()
	{
		if(IntVal($_REQUEST["ID"])<1)
		{
			$content = serialize(array("CONTENT"=>$_REQUEST["content_banner"]));
			$priority = ( IntVal($_REQUEST["priority"])>100 ? 100 : $_REQUEST["priority"] );
			$priority = ( IntVal($_REQUEST["priority"])<10 ? 10 : $_REQUEST["priority"] );
			$arFields = array(
				"ACTIVE" => $_REQUEST["active"]=="Y"?"Y":"N",
				"NAME" => htmlspecialchars($_REQUEST["name"]),
				"PRIORITY" => $priority,
				"CONTENT" => $content,
			);
			$ID = CIMYIEPPADVPopupAdv::Add($arFields);
			return $ID;
		} else {
			$ID = IntVal($_REQUEST["ID"]);
			$content = serialize(array("CONTENT"=>$_REQUEST["content_banner"]));
			$priority = ( IntVal($_REQUEST["priority"])>100 ? 100 : $_REQUEST["priority"] );
			$priority = ( IntVal($_REQUEST["priority"])<10 ? 10 : $_REQUEST["priority"] );
			$arFields = array(
				"ACTIVE" => $_REQUEST["active"]=="Y"?"Y":"N",
				"NAME" => htmlspecialchars($_REQUEST["name"]),
				"PRIORITY" => $priority,
				"CONTENT" => $content,
			);
			CIMYIEPPADVPopupAdv::Update($ID,$arFields);
			return $ID;
		}
	}
}
?>