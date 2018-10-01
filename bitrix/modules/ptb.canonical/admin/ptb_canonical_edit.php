<?
// ################################################
// Company: NicLab
// Site: https://www.psdtobitrix.ru
// Copyright (c) 2013-2017 NicLab
// ################################################


require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Ptb\Canonical\ListTable;

Loc::loadMessages(__FILE__);

global $APPLICATION, $CACHE_MANAGER;

$moduleId = "ptb.canonical";
Loader::includeModule($moduleId);

$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("PTB_CANONICAL_SETTINGS_ACCESS"));
}

ClearVars();

$errorMessage = "";
$bVarsFromForm = false;

$ID = IntVal($ID);

if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid()) {

    if (isset($_REQUEST['ptb_canonical_edit_apply']) || isset($_REQUEST['ptb_canonical_edit_save'])) {
        $arFields = $_REQUEST['PTBCANONICAL'];
        if (!isset($arFields['ACTIVE'])) {
            $arFields['ACTIVE'] = 'N';
        }
        if (!isset($arFields['USE_REGEXP'])) {
            $arFields['USE_REGEXP'] = 'N';
        }
        $result = $ID > 0 ? ListTable::update($ID, $arFields) : ListTable::add($arFields);

        if ($result->isSuccess()) {

            if (defined('BX_COMP_MANAGED_CACHE')) {
                $CACHE_MANAGER->ClearByTag("ptb_canonical");
            }

            if (isset($_REQUEST['ptb_canonical_edit_save'])) {
                LocalRedirect('ptb_canonical_list.php?lang=' . LANG . GetFilterParams("filter_", false));
            } else {
                LocalRedirect('ptb_canonical_edit.php?ID=' . $result->getId() . 'lang=' . LANG . GetFilterParams("filter_", false));
            }
        } else {
            $errors = $result->getErrorMessages();
        }
    }

    if (isset($_REQUEST['ptb_canonical_cancel'])) {
        LocalRedirect('ptb_canonical_list.php?lang=' . LANG . GetFilterParams("filter_", false));
    }
}

$siteList = array();
$rsSites = \CSite::GetList($by = "sort", $order = "asc", Array());
while ($arRes = $rsSites->Fetch()) {
    $siteList[$arRes["ID"]] = $arRes["NAME"];
}

if ($ID > 0) {
    $arItem = ListTable::getByPrimary($ID, array(
        'select' => array(
            'ID',
            'SITE_ID',
            'ACTIVE',
            'DATE_CREATE',
            'CREATED_BY',
            'TIMESTAMP_X',
            'MODIFIED_BY',
            'PAGE',
            'CANONICAL',
            'USE_REGEXP',
            'SORT',
            'MODIFIED_BY_USER.NAME',
            'MODIFIED_BY_USER.LAST_NAME',
            'MODIFIED_BY_USER.LOGIN',
            'CREATED_BY_USER.NAME',
            'CREATED_BY_USER.LAST_NAME',
            'CREATED_BY_USER.LOGIN'
        )
    ))->fetch();
}

$APPLICATION->SetTitle(Loc::getMessage($ID > 0 ? 'PTB_CANONICAL_EDIT_TITLE_EDIT' : 'PTB_CANONICAL_EDIT_TITLE_ADD'));
require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


$aMenu = array(
    array(
        "TEXT" => Loc::getMessage('PTB_CANONICAL_EDIT_MENU_LIST'),
        "LINK" => "ptb_canonical_list.php?lang=" . LANG . GetFilterParams("filter_"),
        "ICON" => "btn_list"
    )
);

if ($ID > 0) {
    $aMenu[] = array(
        "SEPARATOR" => "Y"
    );

    $aMenu[] = array(
        "TEXT" => Loc::getMessage('PTB_CANONICAL_EDIT_MENU_ADD'),
        "LINK" => "ptb_canonical_edit.php?lang=" . LANG . GetFilterParams("filter_"),
        "ICON" => "btn_new"
    );

    $aMenu[] = array(
        "TEXT" => Loc::getMessage("PTB_CANONICAL_EDIT_MENU_DELETE"),
        "LINK" => "javascript:if(confirm('" . Loc::getMessage("PTB_CANONICAL_EDIT_MENU_DELETE_CONF") . "')) window.location='ptb_canonical_list.php?ID=" . $ID . "&action=delete&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
        "WARNING" => "Y",
        "ICON" => "btn_delete"
    );
}
$context = new \CAdminContextMenu($aMenu);
$context->Show();

$aTabs = array(
    array(
        "DIV" => "ptb_canonical_edit",
        "TAB" => GetMessage("PTB_CANONICAL_EDIT_TAB"),
        "TITLE" => GetMessage("PTB_CANONICAL_EDIT_TAB")
    )
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if ($errors) {
    echo \CAdminMessage::ShowMessage(Array(
        "DETAILS" => implode('<br>', $errors),
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("PTB_CANONICAL_EDIT_ERROR"),
        "HTML" => true
    ));

    foreach ($_REQUEST['PTBCANONICAL'] as $key => $value) {
        $arItem[$key] = $value;
    }
}
?>
<form id="ptb_canonical_edit" method="POST"
	action="<?=$APPLICATION->GetCurPageParam('lang='.LANG, array('lang'))?>"
	name="ptb_canonical_edit">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();

?>

<?if ($ID > 0): ?>
	<tr>
		<td class="adm-detail-content-cell-l" width="40%"><label><?=Loc::getMessage("PTB_CANONICAL_EDIT_ID");?></label>:</td>
		<td class="adm-detail-content-cell-r"><?=$ID ?></td>
	</tr>
	<tr>
		<td class="adm-detail-content-cell-l" width="40%"><label><?=Loc::getMessage("PTB_CANONICAL_EDIT_DATE_CREATE");?></label>:</td>
		<td class="adm-detail-content-cell-r"><?=is_object($arItem['DATE_CREATE']) ? $arItem['DATE_CREATE']->toString() : $arItem['DATE_CREATE'] ?></td>
	</tr>
	<tr>
		<td class="adm-detail-content-cell-l" width="40%"><label><?=Loc::getMessage("PTB_CANONICAL_EDIT_CREATED_BY");?></label>:</td>
		<td class="adm-detail-content-cell-r">
		<?
    $fieldValue = '[<a href="/bitrix/admin/user_edit.php?ID=' . $arItem['CREATED_BY'] . '&lang=' . LANG . '">' . $arItem['CREATED_BY'] . '</a>] ';
    $fieldValue .= '(' . $arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_LOGIN'] . ') ' . $arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_NAME'] . ((strlen($arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_NAME']) <= 0 || strlen($arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_LAST_NAME']) <= 0) ? "" : " ") . $arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_LAST_NAME'] . "<br>";
    echo $fieldValue;
    ?>
		</td>
	</tr>
	<tr>
		<td class="adm-detail-content-cell-l" width="40%"><label><?=Loc::getMessage("PTB_CANONICAL_EDIT_TIMESTAMP_X");?></label>:</td>
		<td class="adm-detail-content-cell-r"><?=is_object($arItem['TIMESTAMP_X']) ? $arItem['TIMESTAMP_X']->toString() : $arItem['TIMESTAMP_X'] ?></td>
	</tr>
	<tr>
		<td class="adm-detail-content-cell-l" width="40%"><label><?=Loc::getMessage("PTB_CANONICAL_EDIT_MODIFIED_BY");?></label>:</td>
		<td class="adm-detail-content-cell-r">
		  <?
    $fieldValue = '[<a href="/bitrix/admin/user_edit.php?ID=' . $arItem['MODIFIED_BY'] . '&lang=' . LANG . '">' . $arItem['MODIFIED_BY'] . '</a>] ';
    $fieldValue .= '(' . $arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_LOGIN'] . ') ' . $arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_NAME'] . ((strlen($arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_NAME']) <= 0 || strlen($arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_LAST_NAME']) <= 0) ? "" : " ") . $arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_LAST_NAME'] . "<br>";
    echo $fieldValue;
    ?>
		</td>
	</tr>
<?endif ?>
	<tr>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_active"><?=Loc::getMessage("PTB_CANONICAL_EDIT_ACTIVE");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_active"
			<?=($arItem['ACTIVE'] == 'Y' || $ID <= 0) ? ' checked' : ''?> name="PTBCANONICAL[ACTIVE]" /></td>
	</tr>

	<tr class="adm-detail-required-field">
		<td class="adm-detail-content-cell-l" width="40%"><label
			for="ptb_canonical_for_sites"><?=Loc::getMessage("PTB_CANONICAL_EDIT_SITE_ID");?></label>:</td>
		<td class="adm-detail-content-cell-r">
		  <? ?>
		  <select id="ptb_canonical_for_sites" name="PTBCANONICAL[SITE_ID]">
		  <?foreach ($siteList as $id => $name): ?>
            <option value="<?=$id ?>"
					<?=$id == $arItem['SITE_ID'] ? ' selected' : ''?>><?=$name ?></option>
		  <?endforeach; ?>
		  </select>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_regexp"><?=Loc::getMessage("PTB_CANONICAL_EDIT_REQEXP");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_regexp"
			<?=$arItem['USE_REGEXP'] == 'Y' ? ' checked' : ''?> name="PTBCANONICAL[USE_REGEXP]" />&nbsp;&nbsp;&nbsp;<span
			class="adm-favorites-cap-hint-text"><?=Loc::getMessage('PTB_CANONICAL_EDIT_REQEXP_EXAMPLE') ?></span></td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_sort"><?=Loc::getMessage("PTB_CANONICAL_EDIT_SORT");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="text" value="<?=$arItem['SORT'] > 0 ? $arItem['SORT'] : 500?>"
			id="ptb_canonical_sort"
			name="PTBCANONICAL[SORT]" />&nbsp;&nbsp;&nbsp;<span
			class="adm-favorites-cap-hint-text"><?=Loc::getMessage('PTB_CANONICAL_EDIT_SORT_EXAMPLE') ?></span></td>
	</tr>

	<tr class="adm-detail-required-field">
		<td class="adm-detail-content-cell-l" width="40%"><label
			for="ptb_canonical_page"><?=Loc::getMessage("PTB_CANONICAL_EDIT_PAGE");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="text" size="50"
			value="<?=$arItem['PAGE'] ?>" id="ptb_canonical_page"
			name="PTBCANONICAL[PAGE]" />&nbsp;&nbsp;&nbsp;<span
			class="adm-favorites-cap-hint-text"><?=Loc::getMessage('PTB_CANONICAL_EDIT_PAGE_EXAMPLE') ?></span></td>
	</tr>

	<tr class="adm-detail-required-field">
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_canonical"><?=Loc::getMessage("PTB_CANONICAL_EDIT_CANONICAL");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="text" size="50"
			value="<?=$arItem['CANONICAL'] ?>" id="ptb_canonical_canonical"
			name="PTBCANONICAL[CANONICAL]" />&nbsp;&nbsp;&nbsp;<span
			class="adm-favorites-cap-hint-text"><?=Loc::getMessage('PTB_CANONICAL_EDIT_CANONICAL_EXAMPLE', array('#HOST#' => 'http'.(\CMain::isHttps() ? 's' : '').'://'.$_SERVER['HTTP_HOST'])) ?></span></td>
	</tr>
<?
$tabControl->Buttons();
?>
<input type="submit" name="ptb_canonical_edit_save"
		value="<?=Loc::getMessage("PTB_CANONICAL_EDIT_BUTTON_SAVE")?>"
		class="adm-btn-save" /> <input type="submit"
		name="ptb_canonical_edit_apply"
		value="<?=Loc::getMessage("PTB_CANONICAL_EDIT_BUTTON_APPLY")?>" /> <input
		type="submit" name="ptb_canonical_cancel"
		value="<?=Loc::getMessage("PTB_CANONICAL_EDIT_BUTTON_CANCEL")?>" />
<?
$tabControl->End();
?>
</form>
<?
require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>