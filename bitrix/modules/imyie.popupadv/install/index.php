<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class imyie_popupadv extends CModule
{
    var $MODULE_ID = "imyie.popupadv";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function imyie_popupadv()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
	
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = "1.0.0";
            $this->MODULE_VERSION_DATE = "2012.01.01";
        }

		$this->MODULE_NAME = GetMessage("IMYIE_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IMYIE_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("IMYIE_INSTALL_COPMPANY_NAME");
        $this->PARTNER_URI  = "http://imyie.ru/";
	}

	// Install functions
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule("imyie.popupadv");
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/imyie.popupadv/install/db/".$DBType."/install.sql");
		CModule::IncludeModule("imyie.popupadv");
		CIMYIEPPADVUtils::SetDefaultSettings();
		return TRUE;
	}

	function InstallFiles()
	{	
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.popupadv/install/copyfiles/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.popupadv/install/copyfiles/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.popupadv/install/copyfiles/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		return TRUE;
	}

	function InstallPublic()
	{
		return TRUE;
	}

	function InstallEvents()
	{
		//RegisterModuleDependences("main", "OnEpilog", "imyie.popupadv", "CIMYIEPPADVShower", "HandlerOnProlog");
		return TRUE;
	}

	// UnInstal functions
	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/imyie.popupadv/install/db/".$DBType."/uninstall.sql");
		UnRegisterModule("imyie.popupadv");
		return TRUE;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/admin/imyie_popupadv.php");
		DeleteDirFilesEx("/bitrix/images/imyie.popupadv");
		DeleteDirFilesEx("/bitrix/themes/.default/icons/imyie.popupadv");
		DeleteDirFilesEx("/bitrix/themes/.default/imyie.popupadv.css");
		return TRUE;
	}

	function UnInstallPublic()
	{
		return TRUE;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences("main", "OnEpilog", "imyie.popupadv", "CIMYIEPPADVShower", "HandlerOnProlog");
		COption::RemoveOption("imyie.popupadv");
		return TRUE;
	}

    function DoInstall()
    {
		global $APPLICATION, $step;
		$this->InstallDB();
		$this->InstallFiles();
		$this->InstallEvents();
		$this->InstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.popupadv/install/install.php");
    }

    function DoUninstall()
    {
		global $APPLICATION, $step;
		$this->UnInstallPublic();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("SPER_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.popupadv/install/uninstall.php");
    }
}
?>