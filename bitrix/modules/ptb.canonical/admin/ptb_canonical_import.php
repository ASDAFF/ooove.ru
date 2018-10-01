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

Loc::loadMessages(__FILE__);

global $APPLICATION, $CACHE_MANAGER;

$moduleId = "ptb.canonical";
Loader::includeModule($moduleId);

$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("PTB_CANONICAL_IMPORT_ACCESS"));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid() && ($_REQUEST['params']['startGenerate'] == 'Y' || $_REQUEST["params"]['continueGenerate'] == 'Y')) {
    CUtil::JSPostUnescape();
    require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
    require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/csv_data.php");
    $APPLICATION->RestartBuffer();
    
    if (array_key_exists("params", $_REQUEST) && is_array($_REQUEST["params"]) && $_REQUEST["params"]['continueGenerate'] == 'Y') {
        foreach ($_REQUEST["params"] as $key => $value) {
            if (! is_array($value)) {
                $params[$key] = htmlspecialcharsbx($value);
            }
        }
    } else {
        $_SESSION['ptb_canonical'] = array(
            'errors' => array(),
            'count_success' => 0,
            'count_error' => 0,
            'count_skip' => 0
        );
        
        $params = Array(
            'file' => $_REQUEST['params']['file'],
            'step' => $_REQUEST['params']['step'] ?: 150,
            'currentStep' => 0,
            'checkIsset' => $_REQUEST['params']['checkIsset'] == 'Y' ? 'Y' : 'N',
            'filePos' => 0,
            'site' => $_REQUEST['params']['site']
        );
    }
    
    $params['currentStep'] ++;
    $errors = array();
    
    $file = $_SERVER['DOCUMENT_ROOT'] . $params['file'];
    
    if (! $params['site']) {
        $errors[] = Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_SITE');
    }
    
    if (! $file || ! file_exists($file)) {
        $errors[] = Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_FILE_NOT_EXIST');
    }
    
    if (strpos($file, '.csv', strlen($file) - 4) === false) {
        $errors[] = Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_FILE_CSV');
    }
    
    $isEnd = true;
    
    if (! $errors) {
        $csv = new \CCSVData('R', false);
        $csv->LoadFile($file);
        $csv->SetDelimiter(';');
        $csv->SetPos($params['filePos']);
        $i = 0;
        
        $currentLine = $params['currentStep'] * $params['step'];
        while ($line = $csv->Fetch()) {
            $isEnd = false;
            $params['filePos'] = $csv->GetPos();
            
            $page = trim($line[0]);
            $canonical = trim($line[1]);
            
            if (! $page || ! $canonical) {
                $_SESSION['ptb_canonical']['count_error'] ++;
                $_SESSION['ptb_canonical']['errors'][] = Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_LINE', [
                    '#NUM#' => $currentLine,
                    '#ERROR#' => Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_DATA')
                ]);
            } else {
                $isIsset = false;
                $item = null;
                
                if (! $isIsset) {
                    
                    if ($params['checkIsset'] == 'Y') {
                        $item = ListTable::getRow(array(
                            'filter' => array(
                                '=PAGE' => $page,
                                '=SITE_ID' => $params['site']
                            ),
                            'select' => array(
                                'ID'
                            )
                        ));
                        
                        if ($item) {
                            $_SESSION['ptb_canonical']['count_skip'] ++;
                        }
                    }
                    
                    if (! $item) {
                        $result = ListTable::add(array(
                            'PAGE' => $page,
                            'CANONICAL' => $canonical,
                            'SORT' => 500,
                            'USE_REGEXP' => 'N',
                            'SITE_ID' => $params['site']
                        ));
                        
                        if ($result->isSuccess()) {
                            $_SESSION['ptb_canonical']['count_success'] ++;
                        } else {
                            foreach ($result->getErrorMessages() as $error) {
                                $_SESSION['ptb_canonical']['errors'][] = Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_LINE', [
                                    '#NUM#' => $currentLine,
                                    '#ERROR#' => $error
                                ]);
                            }
                        }
                    }
                }
            }
            
            $i ++;
            $currentLine ++;
            
            if ($i == $params['step']) {
                break;
            }
        }
    }
    
    if ($errors) {
        \CAdminMessage::ShowMessage(array(
            "MESSAGE" => Loc::getMessage("PTB_CANONICAL_IMPORT_ERROR_TITLE"),
            "DETAILS" => implode("<br>", $errors),
            "HTML" => true,
            "TYPE" => "ERROR"
        ));
        
        if (defined('BX_COMP_MANAGED_CACHE')) {
            $CACHE_MANAGER->ClearByTag("ptb_canonical");
        }
        
        ?><script>window['importClass'].finish()</script><?
    } else {
        
        $details = [
            Loc::getMessage('PTB_CANONICAL_IMPORT_STEP_SUCCESS', array(
                '#COUNT#' => $_SESSION['ptb_canonical']['count_success']
            )),
            Loc::getMessage('PTB_CANONICAL_IMPORT_STEP_ERRORS', array(
                '#COUNT#' => $_SESSION['ptb_canonical']['count_error']
            ))
        ];
        
        if ($params['checkIsset'] == 'Y') {
            $details[] = Loc::getMessage('PTB_CANONICAL_IMPORT_STEP_SKIP', [
                '#COUNT#' => $_SESSION['ptb_canonical']['count_skip']
            ]);
        }
        
        if ($isEnd) {
            
            \CAdminMessage::ShowMessage(array(
                "MESSAGE" => Loc::getMessage("PTB_CANONICAL_IMPORT_OK_TITLE"),
                "DETAILS" => implode("<br>", $details),
                "HTML" => true,
                "TYPE" => "OK"
            ));
            
            if ($errors) {
                \CAdminMessage::ShowMessage(array(
                    "MESSAGE" => Loc::getMessage("PTB_CANONICAL_IMPORT_ERROR_TITLE"),
                    "DETAILS" => implode("<br>", $_SESSION['ptb_canonical']['errors']),
                    "HTML" => true,
                    "TYPE" => "ERROR"
                ));
            }
            
            if (defined('BX_COMP_MANAGED_CACHE')) {
                $CACHE_MANAGER->ClearByTag("ptb_canonical");
            }
            
            ?><script>window['importClass'].finish()</script><?
        } else {
            \CAdminMessage::ShowMessage(array(
                "MESSAGE" => Loc::getMessage("PTB_CANONICAL_IMPORT_OK_TITLE"),
                "DETAILS" => implode("<br>", $details),
                "HTML" => true,
                "TYPE" => "OK"
            ));
            
            ?><script>window['importClass'].request(<?=CUtil::PhpToJSObject(array_merge($params, array('continueGenerate' => 'Y'))) ?>)</script><?
        }
    }
    
    require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
    \CMain::ForkActions();
    die();
}

$siteList = array();
$rsSites = \CSite::GetList($by = "sort", $order = "asc", Array());
while ($arRes = $rsSites->Fetch()) {
    $siteList[] = array(
        "ID" => $arRes["ID"],
        "NAME" => $arRes["NAME"]
    );
}

$APPLICATION->SetTitle(Loc::getMessage('PTB_CANONICAL_IMPORT_TITLE'));
require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$aTabs = array(
    array(
        "DIV" => "ptb_canonical",
        "TAB" => Loc::getMessage("PTB_CANONICAL_IMPORT_TITLE"),
        "TITLE" => Loc::getMessage("PTB_CANONICAL_IMPORT_TITLE")
    )
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

CJSCore::Init([
    'ajax'
]);

?>
<div id="ptb_canonical_result"></div>
<form id="ptb_canonical_import" method="POST"
	action="<?=$APPLICATION->GetCurPageParam('lang='.LANG, array('lang'))?>"
	name="ptb_canonical_import">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();

?>

<tr>
		<td width="40%"><label for="ptb_canonical_site"><?=Loc::getMessage("PTB_CANONICAL_IMPORT_SITE");?></label>:</td>
		<td><select id="ptb_canonical_site" name="ptb_canonical_site">
			<?foreach ($siteList as $site): ?>
			<option value="<?=$site['ID'] ?>">[<?=$site['ID'] ?>] <?=$site['NAME'] ?></option>
			<?endforeach; ?>
		</select></td>
	</tr>

	<tr>
		<td width="40%"><label for="ptb_canonical_step_count"><?=Loc::getMessage("PTB_CANONICAL_IMPORT_FILE");?></label>:</td>
		<td><input type="text" name="ptb_canonical_file"
			id="ptb_canonical_file" value="" size="30"> <input type="button"
			value="<?echo GetMessage("PTB_CANONICAL_IMPORT_FILE_OPEN"); ?>"
			OnClick="BtnClick()">
			<?

\CAdminFileDialog::ShowScript(array(
    "event" => "BtnClick",
    "arResultDest" => array(
        "FORM_NAME" => "ptb_canonical_import",
        "FORM_ELEMENT_NAME" => "ptb_canonical_file"
    ),
    "arPath" => array(
        "SITE" => SITE_ID,
        "PATH" => "/" . \COption::GetOptionString("main", "upload_dir", "upload")
    ),
    "select" => 'F', // F - file only, D - folder only
    "operation" => 'O', // O - open, S - save
    "showUploadTab" => true,
    "showAddToMenuTab" => false,
    "fileFilter" => 'csv',
    "allowAllFiles" => true,
    "SaveConfig" => true
));
?>
	</td>
	</tr>

	<tr>
		<td width="40%"><label for="ptb_canonical_step_count"><?=Loc::getMessage("PTB_CANONICAL_IMPORT_STEP_COUNT");?></label>:</td>
		<td><input type="text" name="ptb_canonical_step_count"
			id="ptb_canonical_step_count" value="150" /></td>
	</tr>

	<tr>
		<td width="40%"><label for="ptb_canonical_if_isset"><?=Loc::getMessage("PTB_CANONICAL_IMPORT_IF_ISSET");?></label>:</td>
		<td><input type="checkbox" name="ptb_canonical_if_isset"
			id="ptb_canonical_if_isset" value="Y" checked /></td>
	</tr>
	
<?
$tabControl->Buttons();
?>
<input type="button" id="ptb_start_import"
		value="<?echo Loc::getMessage("PTB_CANONICAL_IMPORT_BUTTON_START")?>"
		class="adm-btn-save" /> <input type="button" id="ptb_stop_import"
		value="<?=Loc::getMessage("PTB_CANONICAL_IMPORT_BUTTON_STOP")?>"
		disabled />
<?
$tabControl->End();
?>
</form>

<?
echo BeginNote();
echo Loc::getMessage('PTB_CANONICAL_IMPORT_NOTE');
echo EndNote();
?>

<script>
PTBImportClass = (function() {
	var ImportClass = function(parameters) {
		parameters = parameters || {};

		this.isStop = false;
		
		this.buttonStart = BX(parameters.buttonStart);
		this.buttonStop = BX(parameters.buttonStop);

		this.resultBlock = BX(parameters.resultBlock);

		this.fieldFile = BX(parameters.fieldFile);
		this.fieldStep = BX(parameters.fieldStep);
		this.fieldCheckIsset = BX(parameters.fieldCheckIsset);
		this.fieldSite = BX(parameters.fieldSite);
	};

	ImportClass.prototype.init = function() {
		BX.bind(this.buttonStart, 'click', BX.delegate(this.startImport, this));
		BX.bind(this.buttonStop, 'click', BX.delegate(this.stopImport, this));
	};

	ImportClass.prototype.startImport = function() {

		BX.cleanNode(this.resultBlock);

		this.isStop = false;
		
		var file = this.fieldFile.value;
		var step = this.fieldStep.value || 150;
		var checkIsset = this.fieldCheckIsset.checked || false;
		var site = this.fieldSite.value;
		
		this.fieldStep.value = step;

		if (file.length <= 0) {
			alert('<?=Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_FILE_EMPTY') ?>');
			return false;
		}

		if (file.indexOf(".csv", (file.length - 4)) == -1) {
			alert('<?=Loc::getMessage('PTB_CANONICAL_IMPORT_ERROR_FILE_CSV') ?>');
			return false;
		}

		BX.adjust(this.buttonStart, {props: {disabled: true}});
		BX.adjust(this.buttonStop, {props: {disabled: false}});

		this.request({
			startGenerate: 'Y',
			file: file,
			step: step,
			checkIsset: checkIsset ? 'Y' : 'N',
			site: site
		});
	};

	ImportClass.prototype.stopImport = function() {
		BX.adjust(this.buttonStart, {props: {disabled: false}});
		BX.adjust(this.buttonStop, {props: {disabled: true}});
		BX.cleanNode(this.resultBlock);
		this.isStop = true;
	};

	ImportClass.prototype.request = function(params) {
		if (!this.isStop) {
			var _this = this;
			BX.ajax.post(
				'<?=$APPLICATION->GetCurPage()?>',
				{
					sessid: BX.bitrix_sessid(),
					params: params
				},
				function (res) {	
					BX.adjust(_this.resultBlock, {html: res});
				}
			);
		}
	};

	ImportClass.prototype.finish = function() {
		this.isStop = true;
		BX.adjust(this.buttonStart, {props: {disabled: false}});
		BX.adjust(this.buttonStop, {props: {disabled: true}});
	};

	return ImportClass;
})();

BX.ready(function () {    
    window['importClass'] = new PTBImportClass({
    	buttonStart: 'ptb_start_import',
    	buttonStop: 'ptb_stop_import',
    	fieldFile: 'ptb_canonical_file',
    	fieldStep: 'ptb_canonical_step_count',
    	fieldCheckIsset: 'ptb_canonical_if_isset',
    	fieldSite: 'ptb_canonical_site',
    	resultBlock: 'ptb_canonical_result'
    });
    window['importClass'].init();
});
</script>
<?
require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>