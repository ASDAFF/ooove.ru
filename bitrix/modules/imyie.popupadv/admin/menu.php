<?IncludeModuleLangFile(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services",
	"text" => GetMessage("IMYIE_PAGE_POPUPADV_MENU"),
	"icon" => "imyie_gm_elem_popupadv",
	"title" => GetMessage("IMYIE_PAGE_IMAIL_POPUPADV_TITLE"),
	"url" => "imyie_popupadv.php?lang=".LANGUAGE_ID,
	"sort" => 9800,
	"more_url" => array(
		"imyie_popupadv_edit.php"
	),
);
return $aMenu;
?>