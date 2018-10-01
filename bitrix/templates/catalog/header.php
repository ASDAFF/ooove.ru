<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");
$wizTemplateId = COption::GetOptionString("main", "wizard_template_id", "eshop_adapt_horizontal", SITE_ID);
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
	<link rel="icon" type="image/x-icon" href="/favicon.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<?//$APPLICATION->ShowHead();
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
	$APPLICATION->ShowMeta("robots", false, true);
	// $APPLICATION->ShowMeta("keywords", false, true);
	$APPLICATION->ShowMeta("description", false, true);
	$APPLICATION->ShowCSS(true, true);
	?>

	<?
	$APPLICATION->ShowHeadStrings();
	$APPLICATION->ShowHeadScripts();
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/script.js");
	?>
	<link href="<?=SITE_TEMPLATE_PATH?>/styles_710.css" type="text/css" rel="stylesheet" />
	<link href="<?=SITE_TEMPLATE_PATH?>/styles_1240.css" type="text/css" rel="stylesheet" />
	<title><?$APPLICATION->ShowTitle()?></title>
	
	<link rel="canonical" href="<?echo $APPLICATION->GetCurDir();?>" />
	
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>

<div class="top-panel">
	<div class="top-panel-inner wrp clearfix">
		<div class="top-menu">
			<?$APPLICATION->IncludeComponent('bitrix:menu', "top_menu", array(
					"ROOT_MENU_TYPE" => "top",
					"MENU_CACHE_TYPE" => "Y",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(),
					"MAX_LEVEL" => "1",
					"USE_EXT" => "N",
					"ALLOW_MULTI_SELECT" => "N"
				)
			);?>
		</div>

		<div class="login-box">
			<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "eshop_adapt1", array(
				"REGISTER_URL" => SITE_DIR."login/",
				"FORGOT_PASSWORD_URL" => "",
				"PROFILE_URL" => SITE_DIR."personal/",
				"SHOW_ERRORS" => "N"
				),
				false
			);?>
		
			<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "template", array(
	"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
	"PATH_TO_PERSONAL" => SITE_DIR."personal/",
	"SHOW_PERSONAL_LINK" => "N",
	"SHOW_NUM_PRODUCTS" => "Y",
	"SHOW_TOTAL_PRICE" => "N",
	"SHOW_PRODUCTS" => "N",
	"POSITION_FIXED" => "N"
	),
	false
);?>
		</div>
	</div>
</div>

<div class="header">
	<div class="header-inner wrp clearfix">
		<div class="logo">
			<?$APPLICATION->IncludeFile(
				SITE_DIR."include/company_name.php",
				Array(),
				Array("MODE"=>"html")
			);?>
		</div>
		
		<div class="slogan-box">
			<?$APPLICATION->IncludeFile(
				SITE_DIR."include/slogan.php",
				Array(),
				Array("MODE"=>"html")
			);?>
		</div>
	</div>
</div>

<div class="middle">
	<div class="middle-inner wrp">
		<div id="menu" class="main-menu">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "main-menu", array(
				"ROOT_MENU_TYPE" => "middle",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "middle",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "Y"
				),
				false
			);?>
		</div>
		
		<div id="menu-mobile" class="top-menu-mobile">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "main-menu", array(
				"ROOT_MENU_TYPE" => "mobile",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "mobile",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "Y"
				),
				false
			);?>
		</div>
		
		<div class="search">
			<!--<?$APPLICATION->IncludeComponent("bitrix:search.title", "visual", array(
					"NUM_CATEGORIES" => "1",
					"TOP_COUNT" => "5",
					"CHECK_DATES" => "N",
					"SHOW_OTHERS" => "N",
					"PAGE" => SITE_DIR."catalog/",
					"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS") ,
					"CATEGORY_0" => array(
						0 => "iblock_catalog",
					),
					"CATEGORY_0_iblock_catalog" => array(
						0 => "all",
					),
					"CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
					"SHOW_INPUT" => "Y",
					"INPUT_ID" => "title-search-input",
					"CONTAINER_ID" => "search",
					"PRICE_CODE" => array(
						0 => "BASE",
					),
					"SHOW_PREVIEW" => "Y",
					"PREVIEW_WIDTH" => "75",
					"PREVIEW_HEIGHT" => "75",
					"CONVERT_CURRENCY" => "Y"
				),
				false
			);?>-->
		</div>
	</div>
</div>

<div class="catalog-box wrp">

<h1><?$APPLICATION->ShowTitle(false)?></h1>

<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "template-chain", array(
	"START_FROM" => "0",
	"PATH" => "",
	"SITE_ID" => "-"
	),
	false
);?>

