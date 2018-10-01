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

Loc::loadMessages(__FILE__);

global $APPLICATION, $CACHE_MANAGER;

$moduleId = "ptb.canonical";
Loader::includeModule($moduleId);

$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("PTB_CANONICAL_SETTINGS_ACCESS"));
}

$siteList = array();
$rsSites = \CSite::GetList($by = "sort", $order = "asc", Array());
while ($arRes = $rsSites->Fetch()) {
    $siteList[] = array(
        "ID" => $arRes["ID"],
        "NAME" => $arRes["NAME"]
    );
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid()) {
    if ($_REQUEST["ptb_canonical_save"]) {
        foreach (array(
            'ptb_canonical_active',
            'ptb_canonical_default',
            'ptb_canonical_404',
            'ptb_canonical_for_sites',
            'ptb_canonical_cache_time',
            'ptb_canonical_query',
            'ptb_server_request_uri',
            'ptb_get_params',
            'ptb_get_delete_params'
        ) as $field) {
            $value = $_REQUEST[$field];
            if (in_array($field, array(
                'ptb_canonical_cache_time'
            ))) {
                $value = (int) $value;
            } elseif(in_array($field, array('ptb_get_delete_params'))) {
                $value = trim($value);
            } else {
                $value = $value == 'Y' ? 'Y' : 'N';
            }

            Option::set($moduleId, $field, $value);
        }

        foreach ($siteList as $site) {
            $siteData = $_REQUEST['ptb_canonical_site_data'][$site['ID']];

            foreach (array(
                'ptb_canonical_active',
                'ptb_canonical_default',
                'ptb_canonical_404',
                'ptb_canonical_cache_time',
                'ptb_canonical_query',
                'ptb_server_request_uri',
                'ptb_get_params',
                'ptb_get_delete_params'
            ) as $field) {
                $value = $siteData[$field];

                if (in_array($field, array(
                    'ptb_canonical_cache_time'
                ))) {
                    $value = (int) $value;
                } elseif(in_array($field, array('ptb_get_delete_params'))) {
                    $value = trim($value);
                } else {
                    $value = $value == 'Y' ? 'Y' : 'N';
                }

                Option::set($moduleId, $field, $value, $site['ID']);
            }
        }

        if (defined('BX_COMP_MANAGED_CACHE')) {
            $CACHE_MANAGER->ClearByTag("ptb_canonical");
        }
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}

$APPLICATION->SetTitle(Loc::getMessage('PTB_CANONICAL_SETTINGS_TITLE'));
require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$aTabs = array(
    array(
        "DIV" => "ptb_canonical",
        "TAB" => Loc::getMessage("PTB_CANONICAL_SETTINGS_TAB"),
        "TITLE" => Loc::getMessage("PTB_CANONICAL_SETTINGS_TAB")
    )
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>
<style>
#ptb_canonical_settings td {
	width: 50%;
}
</style>
<form id="ptb_canonical_settings" method="POST"
	action="<?=$APPLICATION->GetCurPageParam('lang='.LANG, array('lang'))?>"
	name="ptb_canonical_settings">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();

$bForSite = Option::get($moduleId, 'ptb_canonical_for_sites', 'N') == 'Y';
?>
	<tr>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_for_sites"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_SITES");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_for_sites" onClick="SelectForSites(this)"
			<?=$bForSite ? ' checked' : ''?> name="ptb_canonical_for_sites" /></td>
	</tr>

	<tr id="ptb_canonical_tr_active_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_active"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_ACTIVE");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_active"
			<?=Option::get($moduleId, 'ptb_canonical_active', 'N', '-') == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_active" /></td>
	</tr>
	<tr id="ptb_canonical_tr_default_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_default"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_SET_DEFAULT");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_default"
			<?=Option::get($moduleId, 'ptb_canonical_default', 'N', '-') == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_default" /></td>
	</tr>
	<tr id="ptb_canonical_tr_404_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_404"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_SET_WITH_404");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_404"
			<?=Option::get($moduleId, 'ptb_canonical_404', 'N', '-') == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_404" /></td>
	</tr>
	<tr id="ptb_canonical_tr_cache_time_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_cache_time"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_CACHE_TIME");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="text"
			value="<?=Option::get($moduleId, 'ptb_canonical_cache_time', '86400', '-');?>"
			id="ptb_canonical_cache_time" name="ptb_canonical_cache_time" /></td>
	</tr>
	<tr id="ptb_canonical_tr_query_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label for="ptb_canonical_query"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_QUERY");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_query"
			<?=Option::get($moduleId, 'ptb_canonical_query', 'Y', '-') == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_query" /></td>
	</tr>
	<tr id="ptb_canonical_tr_server_request_uri_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label for="ptb_server_request_uri"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_REQUEST_URI");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_server_request_uri"
			<?=Option::get($moduleId, 'ptb_server_request_uri', 'N', '-') == 'Y' ? ' checked' : ''?>
			name="ptb_server_request_uri" />&nbsp;<span
			class="adm-favorites-cap-hint-text"><?=Loc::getMessage('PTB_CANONICAL_SETTINGS_REQUEST_URI_HINT') ?></span></td>
	</tr>
	<tr id="ptb_canonical_tr_get_params_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label for="ptb_get_params"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_GET_PARAMS");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_get_params"
			<?=Option::get($moduleId, 'ptb_get_params', 'N', '-') == 'Y' ? ' checked' : ''?>
			name="ptb_get_params" /></td>
	</tr>
	<tr id="ptb_canonical_tr_get_delete_params_all"
		<?=$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l" valign="top"><label for="ptb_get_delete_params"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_DELETE_GET_PARAMS");?></label>:</td>
		<td class="adm-detail-content-cell-r">
			<textarea id="ptb_get_delete_params" name="ptb_get_delete_params" rows="10" cols="30"><?=Option::get($moduleId, 'ptb_get_delete_params', '', '-') ?></textarea>
		</td>
	</tr>
<?foreach ($siteList as $site): ?>
<tr class="heading" id="ptb_canonical_tr_heading_<?=$site['ID'] ?>"
		<?=!$bForSite ? ' style="display:none"' :'' ?>>
		<td colspan="2"><?=$site['NAME']?></td>
	</tr>
	<tr id="ptb_canonical_tr_active_<?=$site['ID'] ?>"
		<?=!$bForSite ? ' style="display:none"' :'' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_active_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_ACTIVE");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_active_<?=$site['ID'] ?>"
			<?=Option::get($moduleId, 'ptb_canonical_active', 'N', $site['ID']) == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_canonical_active]" /></td>
	</tr>
	<tr id="ptb_canonical_tr_default_<?=$site['ID'] ?>"
		<?=!$bForSite ? ' style="display:none"' :'' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_default_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_SET_DEFAULT");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_default_<?=$site['ID'] ?>"
			<?=Option::get($moduleId, 'ptb_canonical_default', 'N', $site['ID']) == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_canonical_default]" /></td>
	</tr>
	<tr id="ptb_canonical_tr_404_<?=$site['ID'] ?>"
		<?=!$bForSite ? ' style="display:none"' :'' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_404_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_SET_WITH_404");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_404_<?=$site['ID'] ?>"
			<?=Option::get($moduleId, 'ptb_canonical_404', 'N', $site['ID']) == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_canonical_404]" /></td>
	</tr>
	<tr id="ptb_canonical_tr_cache_time_<?=$site['ID'] ?>"
		<?=!$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_cache_time_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_CACHE_TIME");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="text"
			value="<?=Option::get($moduleId, 'ptb_canonical_cache_time', '86400', $site['ID']);?>"
			id="ptb_canonical_cache_time_<?=$site['ID'] ?>"
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_canonical_cache_time]" /></td>
	</tr>
	<tr id="ptb_canonical_tr_query_<?=$site['ID'] ?>"
		<?=!$bForSite ? ' style="display:none"' :'' ?>>
		<td class="adm-detail-content-cell-l"><label
			for="ptb_canonical_query_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_QUERY");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_canonical_query_<?=$site['ID'] ?>"
			<?=Option::get($moduleId, 'ptb_canonical_query', 'Y', $site['ID']) == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_canonical_query]" /></td>
	</tr>
	<tr id="ptb_canonical_tr_server_request_uri_<?=$site['ID'] ?>"
		<?=!$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label for="ptb_server_request_uri_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_REQUEST_URI");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_server_request_uri_<?=$site['ID'] ?>"
			<?=Option::get($moduleId, 'ptb_server_request_uri', 'N', $site['ID']) == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_server_request_uri]" />&nbsp;<span
			class="adm-favorites-cap-hint-text"><?=Loc::getMessage('PTB_CANONICAL_SETTINGS_REQUEST_URI_HINT') ?></span></td>
	</tr>
	<tr id="ptb_canonical_tr_get_params_<?=$site['ID'] ?>"
		<?=!$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l"><label for="ptb_get_params_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_GET_PARAMS");?></label>:</td>
		<td class="adm-detail-content-cell-r"><input type="checkbox" value="Y"
			id="ptb_get_params_<?=$site['ID'] ?>"
			<?=Option::get($moduleId, 'ptb_get_params', 'N', $site['ID']) == 'Y' ? ' checked' : ''?>
			name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_get_params]" /></td>
	</tr>
	<tr id="ptb_canonical_tr_get_delete_params_<?=$site['ID'] ?>"
		<?=!$bForSite? ' style="display: none"' : '' ?>>
		<td class="adm-detail-content-cell-l" valign="top"><label for="ptb_get_delete_params_<?=$site['ID'] ?>"><?=Loc::getMessage("PTB_CANONICAL_SETTINGS_DELETE_GET_PARAMS");?></label>:</td>
		<td class="adm-detail-content-cell-r">
			<textarea id="ptb_get_delete_params_<?=$site['ID'] ?>" name="ptb_canonical_site_data[<?=$site['ID'] ?>][ptb_get_delete_params]" rows="10" cols="30"><?=Option::get($moduleId, 'ptb_get_delete_params', '', $site['ID']) ?></textarea>
		</td>
	</tr>
<?endforeach;?>

<?
$tabControl->Buttons();
?>
<input type="submit" id="ptb_canonical_save" name="ptb_canonical_save"
		value="<?=Loc::getMessage("PTB_CANONICAL_SETTINGS_BUTTON_SAVE")?>"
		class="adm-btn-save" />
<?
$tabControl->End();
?>
</form>
<?
echo BeginNote();
echo Loc::getMessage("PTB_CANONICAL_SETTINGS_NOTE");
echo EndNote();
?>
<script>

var tdActiveAll = BX('ptb_canonical_tr_active_all'),
	tdDefaultAll = BX('ptb_canonical_tr_default_all'),
	td404All = BX('ptb_canonical_tr_404_all'),
	tdCacheTimeAll = BX('ptb_canonical_tr_cache_time_all'),
	tdQueryAll = BX('ptb_canonical_tr_query_all');
	tdServerRequestAll = BX('ptb_canonical_tr_server_request_uri_all');
	tdGetParams = BX('ptb_canonical_tr_get_params_all');
	tdGetDeleteParams = BX('ptb_canonical_tr_get_delete_params_all');

function SelectForSites(_this) {
	if(_this.checked) {
		tdActiveAll.style.display='none';
		tdDefaultAll.style.display='none';
		tdCacheTimeAll.style.display='none';
		tdQueryAll.style.display='none';
		td404All.style.display='none';
		tdServerRequestAll.style.display='none';
		tdGetParams.style.display='none';
		tdGetDeleteParams.style.display='none';
		showSites();
	} else {
		tdActiveAll.style.display='';
		tdDefaultAll.style.display='';
		tdCacheTimeAll.style.display='';
		tdQueryAll.style.display='';
		td404All.style.display='';
		tdServerRequestAll.style.display='';
		tdGetParams.style.display='';
		tdGetDeleteParams.style.display='';
		hideSites();
	}
}
function hideSites() {
	<?foreach ($siteList as $site):?>
	BX('ptb_canonical_tr_active_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_default_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_heading_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_cache_time_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_query_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_404_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_server_request_uri_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_get_params_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	BX('ptb_canonical_tr_get_delete_params_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='none';
	<?endforeach;?>
}
function showSites() {
	<?foreach ($siteList as $site):?>
	BX('ptb_canonical_tr_active_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_default_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_heading_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_cache_time_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_query_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_404_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_server_request_uri_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_get_params_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	BX('ptb_canonical_tr_get_delete_params_<?= htmlspecialcharsbx($site["ID"]);?>').style.display='';
	<?endforeach;?>
}
</script>
<?
require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>