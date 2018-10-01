<?
// ################################################
// Company: NicLab
// Site: https://www.psdtobitrix.ru
// Copyright (c) 2013-2017 NicLab
// ################################################
?>
<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = array(
    'parent_menu' => 'global_menu_marketing',
    'text' => GetMessage('PTB_CANONICAL_MENU_ROOT'),
    'icon' => '',
    'title' => GetMessage('PTB_CANONICAL_MENU_ROOT'),
    'sort' => 500,
    "items_id" => "menu_ptb_canonical",
    'more_url' => array(
        'ptb_canonical_settings.php',
        'ptb_canonical_list.php',
        'ptb_canonical_import.php'
    ),
    'items' => array(
        array(
            'text' => GetMessage('PTB_CANONICAL_MENU_SETTINGS'),
            'title' => GetMessage('PTB_CANONICAL_MENU_SETTINGS'),
            'url' => 'ptb_canonical_settings.php?lang=' . LANGUAGE_ID
        ),
        array(
            'text' => GetMessage('PTB_CANONICAL_MENU_LIST'),
            'title' => GetMessage('PTB_CANONICAL_MENU_LIST'),
            'url' => 'ptb_canonical_list.php?lang=' . LANGUAGE_ID,
            'more_url' => array(
                'ptb_canonical_edit.php'
            )
        ),
        array(
            'text' => GetMessage('PTB_CANONICAL_MENU_IMPORT'),
            'title' => GetMessage('PTB_CANONICAL_MENU_IMPORT'),
            'url' => 'ptb_canonical_import.php?lang=' . LANGUAGE_ID
        )
    
    )
);

return $aMenu;
?>