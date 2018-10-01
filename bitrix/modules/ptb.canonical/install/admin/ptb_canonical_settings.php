<?

$file = $_SERVER['DOCUMENT_ROOT'].'/local/modules/ptb.canonical/admin/ptb_canonical_settings.php';

if (!file_exists($file)) {
    $file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/ptb.canonical/admin/ptb_canonical_settings.php';
}

require_once($file);
?>