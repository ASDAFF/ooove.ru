<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");
$APPLICATION->SetTitle("");
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
$theme = COption::GetOptionString("main", "wizard_eshop_bootstrap_theme_id", "blue", SITE_ID);

// START WebSEO.kz Michael Nossov:
$wsasset = \Bitrix\Main\Page\Asset::getInstance();
$wscanonical = 'https://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $APPLICATION->GetCurPage(true));
$wspagenum = '';
if(isset($_REQUEST['PAGEN_1']) && !empty($_REQUEST['PAGEN_1']) && intval($_REQUEST['PAGEN_1']) > 1){
	$wscanonical .= '?PAGEN_1='.$_REQUEST['PAGEN_1'];
	// Если эта страница с пагинацией, то добавляем в Title и Description фразу "Страница 2", "Страница 3" и т.д. (кроме первой страницы)
	$wspagenum = ' → Страница '.$_REQUEST['PAGEN_1'];
	$wsdesc = $APPLICATION->GetProperty('description');
	$APPLICATION->SetPageProperty('description', $wsdesc.$wspagenum);
}
// ко всем страницам сайта добавляем канонический URL
$wsasset->addString('<link rel="canonical" href="' . $wscanonical . '">');
// END WebSEO.kz

?><!DOCTYPE html>
<html xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>

<meta name="robots" content="index, follow"/>
	<meta name="yandex-verification" content="e7c67a9aedeebaae" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR?>favicon.ico" />
	<?$APPLICATION->ShowHead();?>
	<?
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/colors.css", true);
	$APPLICATION->SetAdditionalCSS("/bitrix/css/main/bootstrap.css");
	$APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");
	?>
	<title><?$APPLICATION->ShowTitle()?><?=$wspagenum?></title>
 
</head>

<body class="bx-background-image bx-theme-<?=$theme?>" <?=$APPLICATION->ShowProperty("backgroundImage")?>>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>


	 <?$APPLICATION->IncludeComponent(
	"coffeediz:schema.org.Rating", 
	".default", 
	array(
		"AGGREGATE_RATING" => "Y",
		"BESTRATING" => "10",
		"COMPONENT_TEMPLATE" => ".default",
		"ITEMPROP" => "N",
		"ITEMREVIEWED" => "OrganizationAndPlace",
		"ITEMREVIEWED_ADDRESS" => "4536-й проезд",
		"ITEMREVIEWED_COUNTRY" => "Россия",
		"ITEMREVIEWED_FAX" => "",
		"ITEMREVIEWED_LOCALITY" => "Мытищи",
		"ITEMREVIEWED_LOGO" => "",
		"ITEMREVIEWED_NAME" => "ООО ВОСТОЧНЫЙ ЭКСПРЕСС",
		"ITEMREVIEWED_PHONE" => array(
			0 => "8",
			1 => " ",
			2 => "(",
			3 => "4",
			4 => "9",
			5 => "9",
			6 => ")",
			7 => " ",
			8 => "7",
			9 => "0",
			10 => "9",
			11 => "-",
			12 => "0",
			13 => "4",
			14 => "-",
			15 => "0",
			16 => "4",
			17 => "",
		),
		"ITEMREVIEWED_POST_CODE" => "",
		"ITEMREVIEWED_REGION" => "Московская область",
		"ITEMREVIEWED_SITE" => "ooove.ru",
		"ITEMREVIEWED_TYPE" => "Organization",
		"RAITINGCOUNT" => "1567",
		"RATINGVALUE" => "9",
		"REVIEWCOUNT" => "",
		"SHOW" => "Y",
		"WORSTRATING" => "1",
		"COMPOSITE_FRAME_MODE" => "Y",
		"COMPOSITE_FRAME_TYPE" => "STATIC"
	),
	false
);?>

<div class="bx-wrapper" id="bx_eshop_wrap">
	<header class="bx-header">
		<div class="bx-header-section">

			<div class="row">
				<div class="col-md-12 hidden-xs">
					<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"catalog_horizontal_ooove", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_THEME" => "site",
		"CACHE_SELECTED_ITEMS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"COMPONENT_TEMPLATE" => "catalog_horizontal_ooove",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
				</div>
			</div>
<br>

			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<div class="bx-logo">
						<a href="<?=SITE_DIR?>">
							<div class="hidden-xs">
							<img src="/include/ooove_logo.png" srcset="/include/ooove_log.png x2" style="max-height:50px;" alt="Запчасти для китайских автомобилей оптом в Москве">
							</div>
							<div class="hidden-lg hidden-md hidden-sm">
								<img src="/include/ooove_logo.png" style="max-height:30px;text-align:center;" alt="Запчасти для китайских автомобилей оптом в Москве">
							</div>

						</a>

					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-4 hidden-xs">
				</div>
				<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">
				</div>
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 hidden-xs">
					<div class="ooove-basket">
					<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line", 
	".default", 
	array(
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/",
		"SHOW_PERSONAL_LINK" => "N",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"SHOW_PRODUCTS" => "N",
		"POSITION_FIXED" => "N",
		"SHOW_AUTHOR" => "Y",
		"PATH_TO_REGISTER" => SITE_DIR."login/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/",
		"COMPONENT_TEMPLATE" => ".default",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
		"SHOW_EMPTY_VALUES" => "Y",
		"HIDE_ON_BASKET_PAGES" => "N",
		"COMPOSITE_FRAME_MODE" => "Y",
		"COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB"
	),
	false
);?>
				</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12" style="margin-top:20px;margin-bottom:15px;">
					<div class="bx-inc-orginfo-phone">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?>
					</div>
					<div class="bx-worktime">
						<div class="bx-worktime-prop">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?>
						</div>
					</div>
				</div>
			</div>
		</div>

<?if($APPLICATION->GetCurPage() == "/"):?>
		<div class="partners">
			<div class="partners_title">ЛУЧШИЕ УСЛОВИЯ ДЛЯ ПАРТНЕРСТВА</div>
			<div class="partners_podtitle">ФИРМЕННЫЕ ЗАПЧАСТИ ДЛЯ КИТАЙСКИХ АВТОМОБИЛЕЙ</div>
            <div class="row">
				<div class="col-md-3"><a href="/promo/" style="color:white"><i class="fa fa-tags partners_block1"></i><br><span class="partners_block2">Гарантированно низкие цены</span></a></div>
				<div class="col-md-3"><a href="/promo/" style="color:white"><i class="fa fa fa-truck partners_block1"></i><br><span class="partners_block2">Оперативная доставка</span></a></div>
				<div class="col-md-3"><a href="/promo/" style="color:white"><i class="fa fa-phone partners_block1"></i><br><span class="partners_block2">Поддержка наших клиентов</span></a></div>
				<div class="col-md-3"><a href="/promo/" style="color:white"><i class="fa fa-pie-chart partners_block1"></i><br><span class="partners_block2">Отсрочка платежа</span></a></div>
			</div>
		</div>
<?endif;?>

	</header>

	<div class="workarea">
		<div class="container bx-content-seection">

			<?if ($curPage != SITE_DIR."index.php"):?>
			<div class="row">
				<div class="col-lg-12">
					<?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"visual", 
	array(
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "5",
		"CHECK_DATES" => "Y",
		"SHOW_OTHERS" => "N",
		"PAGE" => SITE_DIR."catalog/",
		"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "5",
		),
		"CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "search",
		"PRICE_CODE" => array(
			0 => "Опт №2",
		),
		"SHOW_PREVIEW" => "Y",
		"PREVIEW_WIDTH" => "75",
		"PREVIEW_HEIGHT" => "75",
		"CONVERT_CURRENCY" => "Y",
		"COMPONENT_TEMPLATE" => "visual",
		"ORDER" => "date",
		"USE_LANGUAGE_GUESS" => "N",
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"CURRENCY_ID" => "RUB",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
				</div>
			</div>
			<?endif?>


			<?if ($curPage != SITE_DIR."index.php"):?>
			<div class="row">
				<div class="col-lg-12" id="navigation">
					<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
							"START_FROM" => "0",
							"PATH" => "",
							"SITE_ID" => "-"
						),
						false,
						Array('HIDE_ICONS' => 'Y')
					);?>
				</div>
			</div>
			<h1 class="bx-title dbg_title" id="pagetitle"><?=$APPLICATION->ShowTitle(false);?></h1>
			<?endif?>

			<div class="row">
				<div class="bx-content">