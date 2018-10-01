<?
// ################################################
// Company: NicLab
// Site: https://www.psdtobitrix.ru
// Copyright (c) 2013-2017 NicLab
// ################################################


require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Ptb\Canonical\ListTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Entity\ExpressionField;

Loc::loadMessages(__FILE__);

global $APPLICATION, $DB, $CACHE_MANAGER;

$moduleId = "ptb.canonical";
Loader::includeModule($moduleId);

$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("PTB_CANONICAL_LIST_ACCESS"));
}

$APPLICATION->SetTitle(Loc::getMessage('PTB_CANONICAL_LIST_TITLE'));

$sTableID = "tbl_ptb_canonical_list";

$oSort = new \CAdminSorting($sTableID, "ID", "asc");

$lAdmin = new \CAdminList($sTableID, $oSort);

$arFilterFields = array(
    'filter_site_id',
    'filter_active',
    'filter_regexp',
    'filter_page',
    'filter_canonical'
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if ($filter_active) {
    $arFilter["ACTIVE"] = (($filter_active == "Y") ? "Y" : "N");
}

if ($filter_regexp) {
    $arFilter["REGEXP"] = (($filter_regexp == "Y") ? "Y" : "N");
}

if ($filter_site_id) {
    $arFilter["=SITE_ID"] = $filter_site_id;
}

if ($filter_page) {
    $arFilter["%PAGE"] = $filter_page;
}

if ($filter_canonical) {
    $arFilter["%CANONICAL"] = $filter_canonical;
}

if ($filter_created_by) {
    $arFilter["=CREATED_BY"] = $filter_created_by;
}

if ($filter_modified_by) {
    $arFilter["=MODIFIED_BY"] = $filter_modified_by;
}

if ($filter_date_create_from) {
    $arFilter[">=DATE_CREATE"] = $filter_date_create_from;
}

if ($filter_date_create_to) {
    if ($arDate = ParseDateTime($filter_date_create_to, CSite::GetDateFormat("FULL", SITE_ID))) {
        if (StrLen($filter_date_create_to) < 11) {
            $arDate["HH"] = 23;
            $arDate["MI"] = 59;
            $arDate["SS"] = 59;
        }

        $filter_date_create_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
        $arFilter["<=DATE_CREATE"] = $filter_date_create_to;
    }
}

if ($filter_timestam_x_from) {
    $arFilter[">=TIMESTAMP_X"] = $filter_timestam_x_from;
}

if ($filter_timestam_x_to) {
    if ($arDate = ParseDateTime($filter_timestam_x_to, CSite::GetDateFormat("FULL", SITE_ID))) {
        if (StrLen($filter_timestam_x_to) < 11) {
            $arDate["HH"] = 23;
            $arDate["MI"] = 59;
            $arDate["SS"] = 59;
        }

        $filter_timestam_x_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
        $arFilter["<=TIMESTAMP_X"] = $filter_timestam_x_to;
    }
}

$bSuccess = false;
if ($lAdmin->EditAction()) {

    foreach ($FIELDS as $ID => $arFields) {
        $DB->StartTransaction();
        $ID = (int) $ID;

        if (! $lAdmin->IsUpdated($ID))
            continue;

        $result = ListTable::update($ID, $arFields);

        if (! $result->isSuccess()) {
            if ($error = $result->getErrorMessages()) {
                $lAdmin->AddUpdateError($error, $ID);
            } else {
                $lAdmin->AddUpdateError(Loc::getMessage("PTB_CANONICAL_LIST_UPDATE_ERROR"), $ID);
            }

            $DB->Rollback();
        } else {
            $bSuccess = true;
        }

        $DB->Commit();
    }
}

if (($arID = $lAdmin->GroupAction())) {

    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rs = ListTable::getList(array(
            'filter' => $arFilter,
            'select' => array(
                'id'
            )
        ));
        while ($arItem = $rs->fetch()) {
            $arID[] = $arItem['ID'];
        }
    }

    foreach ($arID as $ID) {
        if (strlen($ID) <= 0)
            continue;

        switch ($_REQUEST['action']) {
            case "delete":
                @set_time_limit(0);

                $DB->StartTransaction();

                $result = ListTable::delete($ID);
                if (! $result->isSuccess()) {
                    $DB->Rollback();

                    if ($error = $result->getErrorMessages()) {
                        $lAdmin->AddGroupError($error, $ID);
                    } else {
                        $lAdmin->AddGroupError(Loc::getMessage("PTB_CANONICAL_LIST_DELETE_ERROR"), $ID);
                    }
                } else {
                    $bSuccess = true;
                }

                $DB->Commit();

                break;

            case "activate":
            case "deactivate":

                $result = ListTable::update($ID, array(
                    "ACTIVE" => (($_REQUEST['action'] == "activate") ? "Y" : "N")
                ));

                if (! $result->isSuccess()) {
                    if ($error = $result->getErrorMessages()) {
                        $lAdmin->AddGroupError($error, $ID);
                    } else {
                        $lAdmin->AddGroupError(Loc::getMessage("PTB_CANONICAL_LIST_ACTIVATE_ERROR"), $ID);
                    }
                } else {
                    $bSuccess = true;
                }

                break;
        }
    }
}

if ($bSuccess && defined('BX_COMP_MANAGED_CACHE')) {
    $CACHE_MANAGER->ClearByTag("ptb_canonical");
}

$usePageNavigation = true;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel')
{
	$usePageNavigation = false;
}
else
{
	$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
	));

	if ($navyParams['SHOW_ALL'])
	{
		$usePageNavigation = false;
	}
	else
	{
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}
}


$nav = new \Bitrix\Main\UI\AdminPageNavigation("nav-culture");

$params = array(
    'filter' => $arFilter,
    'order' => array(
        $by => $order
    ),
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
    ),
    'count_total' => true
);

$countQuery = new Query('\Ptb\Canonical\ListTable');
$countQuery->addSelect(new ExpressionField('CNT', 'COUNT(1)'));
$countQuery->setFilter($arFilter);
$totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
unset($countQuery);
$totalCount = (int)$totalCount['CNT'];
if ($totalCount > 0)
{
    $totalPages = ceil($totalCount/$navyParams['SIZEN']);
    
    if ($navyParams['PAGEN'] > $totalPages)
        $navyParams['PAGEN'] = $totalPages;
    $getListParams['limit'] = $navyParams['SIZEN'];
    $getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}
else
{
    $navyParams['PAGEN'] = 1;
    $getListParams['limit'] = $navyParams['SIZEN'];
    $getListParams['offset'] = 0;
}

if ($usePageNavigation) {
    $params['offset'] = $getListParams['offset'];
    $params['limit'] = $getListParams['limit'];
}

$dbResultList = ListTable::getList($params);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
if ($usePageNavigation) {
    $dbResultList->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $dbResultList->NavRecordCount = $totalCount;
    $dbResultList->NavPageCount = $totalPages;
    $dbResultList->NavPageNomer = $navyParams['PAGEN'];
} else {
    $dbResultList->NavStart();
}

$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage('PTB_CANONICAL_LIST_PAGINATION')));

$lAdmin->AddHeaders(array(
    array(
        "id" => "ID",
        "content" => "ID",
        "sort" => "ID",
        "default" => true
    ),
    array(
        "id" => "SITE_ID",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_SITE_ID"),
        "sort" => "SITE_ID",
        "default" => true
    ),
    array(
        "id" => "ACTIVE",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_ACTIVE"),
        "sort" => "ACTIVE",
        "default" => true
    ),
    array(
        "id" => "DATE_CREATE",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_DATE_CREATE"),
        "sort" => "DATE_CREATE",
        "default" => true
    ),
    array(
        "id" => "CREATED_BY",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_CREATED_BY"),
        "sort" => "CREATED_BY",
        "default" => true
    ),
    array(
        "id" => "TIMESTAMP_X",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_TIMESTAMP_X"),
        "sort" => "TIMESTAMP_X",
        "default" => true
    ),
    array(
        "id" => "MODIFIED_BY",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_MODIFIED_BY"),
        "sort" => "MODIFIED_BY",
        "default" => true
    ),
    array(
        "id" => "PAGE",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_PAGE"),
        "sort" => "PAGE",
        "default" => true
    ),
    array(
        "id" => "CANONICAL",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_CANONICAL"),
        "sort" => "CANONICAL",
        "default" => true
    ),
    array(
        "id" => "USE_REGEXP",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_USE_REGEXP"),
        "sort" => "USE_REGEXP",
        "default" => true
    ),
    array(
        "id" => "SORT",
        "content" => Loc::getMessage("PTB_CANONICAL_LIST_SORT"),
        "sort" => "CREATED_BY",
        "default" => true
    )
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$arSites = array();
$dbSiteList = CSite::GetList(($b = "sort"), ($o = "asc"));
while ($arSite = $dbSiteList->Fetch()) {
    $arSites[$arSite["LID"]] = "[" . $arSite["LID"] . "] " . $arSite["NAME"];
}

while ($arItem = $dbResultList->fetch()) {
    $row = & $lAdmin->AddRow($arItem['ID'], $arItem, 'ptb_canonical_edit.php?ID=' . $arItem['ID'] . '&lang=' . LANG . GetFilterParams('filter_'));

    $row->AddField("ID", $arItem['ID']);

    $row->AddSelectField("SITE_ID", $arSites, array());

    $row->AddCheckField("ACTIVE");

    $row->AddField("DATE_CREATE", $arItem['DATE_CREATE']);

    $fieldValue = '[<a href="user_edit.php?ID=' . $arItem['CREATED_BY'] . '&lang=' . LANG . '">' . $arItem['CREATED_BY'] . '</a>] ';
    $fieldValue .= '(' . $arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_LOGIN'] . ') ' . $arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_NAME'] . ((strlen($arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_NAME']) <= 0 || strlen($arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_LAST_NAME']) <= 0) ? "" : " ") . $arItem['PTB_CANONICAL_LIST_CREATED_BY_USER_LAST_NAME'] . "<br>";

    $row->AddField("CREATED_BY", $fieldValue);

    $row->AddField("TIMESTAMP_X", $arItem['TIMESTAMP_X']);

    $fieldValue = '[<a href="user_edit.php?ID=' . $arItem['MODIFIED_BY'] . '&lang=' . LANG . '">' . $arItem['MODIFIED_BY'] . '</a>] ';
    $fieldValue .= '(' . $arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_LOGIN'] . ') ' . $arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_NAME'] . ((strlen($arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_NAME']) <= 0 || strlen($arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_LAST_NAME']) <= 0) ? "" : " ") . $arItem['PTB_CANONICAL_LIST_MODIFIED_BY_USER_LAST_NAME'] . "<br>";

    $row->AddField("MODIFIED_BY", $fieldValue);

    $fieldEdit = '<input type="text" name="FIELDS[' . $arItem['ID'] . '][PAGE]" value="' . htmlspecialcharsbx($arItem['PAGE']) . '" size="40">';

    $row->AddField("PAGE", $arItem['PAGE'], $fieldEdit);

    $fieldEdit = '<input type="text" name="FIELDS[' . $arItem['ID'] . '][CANONICAL]" value="' . htmlspecialcharsbx($arItem['CANONICAL']) . '" size="40">';

    $row->AddField("CANONICAL", $arItem['CANONICAL'], $fieldEdit);

    $row->AddCheckField("USE_REGEXP");

    $sortValue = $arItem['SORT'] > 0 ? $arItem['SORT'] : 500;
    $fieldEdit = '<input type="text" name="FIELDS[' . $arItem['ID'] . '][SORT]" value="' . htmlspecialcharsbx($sortValue) . '" size="8">';

    $row->AddField("SORT", $sortValue, $fieldEdit);

    $arActions = array(
        array(
            'ICON' => 'edit',
            'TEXT' => Loc::getMessage('MAIN_ADMIN_MENU_EDIT'),
            'ACTION' => $lAdmin->ActionRedirect('ptb_canonical_edit.php?ID=' . $arItem['ID'] . '&lang=' . LANG . GetFilterParams('filter_')),
            'DEFAULT' => true
        ),
        array(
            'ICON' => 'delete',
            'TEXT' => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
            'ACTION' => 'if(confirm(\'' . Loc::getMessage('PTB_CANONICAL_LIST_DELETE_CONF') . '\')) ' . $lAdmin->ActionRedirect('ptb_canonical_list.php?action=delete&ID=' . $arItem['ID'] . '&lang=' . LANGUAGE_ID . '&' . bitrix_sessid_get())
        )
    );

    $row->AddActions($arActions);
}

$lAdmin->AddFooter(array(
    array(
        "title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
        "value" => $dbResultList->SelectedRowsCount()
    ),
    array(
        "counter" => true,
        "title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
        "value" => "0"
    )
));

$lAdmin->AddGroupActionTable(array(
    "delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
    "activate" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
    "deactivate" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE")
));

$aContext = array(
    array(
        "TEXT" => Loc::getMessage('PTB_CANONICAL_LIST_ADD'),
        "LINK" => "ptb_canonical_edit.php?lang=" . LANG,
        "TITLE" => Loc::getMessage('PTB_CANONICAL_LIST_ADD'),
        "ICON" => "btn_new"
    )
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

?><form name="find_form" method="GET"
	action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new \CAdminFilter($sTableID . "_filter", array(
    Loc::getMessage("PTB_CANONICAL_LIST_SITE_ID"),
    Loc::getMessage("PTB_CANONICAL_LIST_ACTIVE"),
    Loc::getMessage("PTB_CANONICAL_LIST_DATE_CREATE"),
    Loc::getMessage("PTB_CANONICAL_LIST_CREATED_BY"),
    Loc::getMessage("PTB_CANONICAL_LIST_TIMESTAMP_X"),
    Loc::getMessage("PTB_CANONICAL_LIST_MODIFIED_BY"),
    Loc::getMessage("PTB_CANONICAL_LIST_PAGE"),
    Loc::getMessage("PTB_CANONICAL_LIST_CANONICAL")
));

$oFilter->Begin();
?>
<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_SITE_ID")?>:</td>
		<td><select name="filter_site_id">
				<option value=""><?= htmlspecialcharsex(Loc::getMessage("PTB_CANONICAL_LIST_FILTER_ALL")); ?></option>
				<?foreach ($arSites as $id => $name): ?>
				<option value="<?=$id ?>"
					<?if ($filter_site_id == $id) echo " selected"?>><?= htmlspecialcharsex($name) ?></option>
				<?endforeach; ?>
			</select></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_ACTIVE")?></td>
		<td><select name="filter_active">
				<option value=""><?= htmlspecialcharsex(Loc::getMessage("PTB_CANONICAL_LIST_FILTER_ALL")); ?></option>
				<option value="Y" <?if ($filter_active == "Y") echo " selected"?>><?= htmlspecialcharsex(GetMessage("PTB_CANONICAL_LIST_FILTER_ACTIVE")) ?></option>
				<option value="N" <?if ($filter_active == "N") echo " selected"?>><?= htmlspecialcharsex(GetMessage("PTB_CANONICAL_LIST_FILTER_DEACTIVE")) ?></option>
		</select></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_DATE_CREATE")?></td>
		<td>
			<?=CalendarPeriod("filter_date_create_from", $filter_date_create_from, "filter_date_create_to", $filter_date_create_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_CREATED_BY")?>:</td>
		<td>
			<?=FindUserID("filter_created_by", $filter_created_by, "", "find_form");?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_TIMESTAMP_X")?></td>
		<td>
			<?=CalendarPeriod("filter_timestam_x_from", $filter_timestam_x_from, "filter_timestam_x_to", $$filter_timestam_x_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_MODIFIED_BY")?>:</td>
		<td>
			<?=FindUserID("filter_modified_by", $filter_modified_by, "", "find_form");?>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_PAGE")?></td>
		<td><input type="text" name="filter_page" value="<?=$filter_page ?>"
			size="50" /></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("PTB_CANONICAL_LIST_CANONICAL")?></td>
		<td><input type="text" name="filter_canonical"
			value="<?=$filter_canonical ?>" size="50" /></td>
	</tr>
<?
$oFilter->Buttons(array(
    "table_id" => $sTableID,
    "url" => $APPLICATION->GetCurPage(),
    "form" => "find_form"
));
$oFilter->End();
?>
</form><?

$lAdmin->DisplayList();

require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>