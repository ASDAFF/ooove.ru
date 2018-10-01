<?
	global $MESS; 
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class sms_feedback extends CModule
{
    var $MODULE_ID = "sms.feedback";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function sms_feedback()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("MYMODULE_MODULE_NAME"); 
		$this->MODULE_DESCRIPTION = GetMessage("MYMODULE_MODULE_DESC"); 
		$this->PARTNER_URI = GetMessage("MYMODULE_PARTNER_URL");
		$this->PARTNER_NAME = GetMessage("MYMODULE_PARTNER_NAME");
    }

	function DoInstall() 
	{
		global $APPLICATION;
		if (!IsModuleInstalled("sms.feedback"))
      {
		$this->InstallFiles();
		$this->InstallDB();
	}
//	$APPLICATION->IncludeAdminFile(GetMessage("MYMODULE_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/step.php");
	 return true;
	}

	function DoUninstall()
	{

		global $APPLICATION;
		
		COption::RemoveOption("sms.feedback");
		
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$GLOBALS["errors"] = $this->errors;


		$APPLICATION->IncludeAdminFile(GetMessage("MYMODULE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/unstep2.php");
 return true;
	}


	function InstallFiles()
	{

		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/images",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/sms.feedback", true, true);
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
	
		return true;
	}	

	function UnInstallFiles()
	{

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/components/sms/feedback", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/sms/feedback");

		//DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sms.feedback/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		//DeleteDirFilesEx("/bitrix/themes/.default/icons/sms.feedback/");//icons
		//DeleteDirFilesEx("/bitrix/themes/.default/start_menu/sms.feedback/");//start menu icons
		//DeleteDirFilesEx("/bitrix/images/sms.feedback/");//images

		return true;
	}
	
	
	function InstallDB($arModuleParams = array()) 
		{
	   		RegisterModule("sms.feedback");
		//	RegisterModuleDependences("main", "OnAfterUserRegister", "sms.feedback", "MyClass", "OnAfterUserRegisterHandler");
			
			 return true;
		}
	
	function UnInstallDB()
		{
		   COption::RemoveOption("sms.feedback");
	//	   UnRegisterModuleDependences("main", "OnAfterUserRegister", "sms.feedback", "MyClass", "OnAfterUserRegisterHandler");
		   UnRegisterModule("sms.feedback");
		   
		   return true;
		}
	
	

} 

?>