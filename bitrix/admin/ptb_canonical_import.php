<?
$file = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/ptb.canonical/admin/ptb_canonical_import.php';

if (! file_exists($file)) {
    $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/ptb.canonical/admin/ptb_canonical_import.php';
}

require_once ($file);
?>