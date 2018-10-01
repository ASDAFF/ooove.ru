<?

$file = $_SERVER['DOCUMENT_ROOT'].'/local/modules/ptb.canonical/admin/ptb_canonical_list.php';

if (!file_exists($file)) {
    $file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/ptb.canonical/admin/ptb_canonical_list.php';
}

require_once($file);
?>