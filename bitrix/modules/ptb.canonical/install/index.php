<?
IncludeModuleLangFile(__FILE__);

class ptb_canonical extends CModule
{

    const MODULE_ID = 'ptb.canonical';

    var $MODULE_ID = 'ptb.canonical';

    var $MODULE_VERSION;

    var $MODULE_VERSION_DATE;

    var $MODULE_NAME;

    var $MODULE_DESCRIPTION;

    var $MODULE_CSS;

    var $strError = '';

    function ptb_canonical()
    {
        $arModuleVersion = array();
        include (dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("ptb.canonical_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ptb.canonical_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("ptb.canonical_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("ptb.canonical_PARTNER_URI");
    }

    function InstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;

        if (! $DB->Query("SELECT 'x' FROM ptb_canonical_list WHERE 1=0", true)) {
            $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/db/" . strtolower($DB->type) . "/install.sql");
        }

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        } else {}

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        if (! array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y") {
            $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/db/" . strtolower($DB->type) . "/uninstall.sql");
        }

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }

        return true;
    }

    function InstallEvents()
    {
        RegisterModuleDependences('main', 'OnEpilog', self::MODULE_ID, '\Ptb\Canonical\Handlers', 'setCanonical');
        return true;
    }

    function UnInstallEvents()
    {
        UnRegisterModuleDependences('main', 'OnEpilog', self::MODULE_ID, '\Ptb\Canonical\Handlers', 'setCanonical');
        return true;
    }

    function InstallFiles()
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin')) {
        if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.') {
                        continue;
                    }
                    copy($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $item);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function UnInstallFiles()
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.') {
                        continue;
                    }
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $item);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallEvents();
        RegisterModule(self::MODULE_ID);
    }

    function DoUninstall()
    {
        global $APPLICATION, $step;

        $step = IntVal($step);

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . "_UNINSTALL_TITLE"), $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . self::MODULE_ID . "/install/unstep1.php");
        } elseif ($step == 2) {
            UnRegisterModule(self::MODULE_ID);

            $this->UnInstallDB(array(
                "savedata" => $_REQUEST["savedata"]
            ));
            $this->UnInstallFiles();
            $this->UnInstallEvents();

            $APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . "_UNINSTALL_TITLE"), $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . self::MODULE_ID . "/install/unstep2.php");
        }
    }
}
?>