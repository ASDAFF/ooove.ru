<?
IncludeModuleLangFile(__FILE__);
Class bisexpert_owlslider extends CModule
{
	const MODULE_ID = 'bisexpert.owlslider';
	var $MODULE_ID = 'bisexpert.owlslider'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("bisexpert.owlslider_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("bisexpert.owlslider_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("bisexpert.owlslider_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("bisexpert.owlslider_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CBisexpertOwlslider', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CBisexpertOwlslider', 'OnBuildGlobalMenu');
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bisexpert.owlslider/install/components/bisexpert/owlslider",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bisexpert/owlslider/", true, true);
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bisexpert.owlslider/install/js",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/bisexpert.owlslider", true, true);
    }
	function UnInstallFiles()
	{

        DeleteDirFilesEx("/bitrix/components/bisexpert/owlslider");
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
