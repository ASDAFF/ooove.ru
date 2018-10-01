<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");
$APPLICATION->SetTitle("");
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
$theme = COption::GetOptionString("main", "wizard_eshop_bootstrap_theme_id", "blue", SITE_ID);
?><!DO CTYPE html>
<html xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
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
	<title><?$APPLICATION->ShowTitle()?></title>
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
					<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog_horizontal_ooove_white", Array(
	"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"MENU_THEME" => "site",	// Тема меню
		"CACHE_SELECTED_ITEMS" => "N",
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"MAX_LEVEL" => "3",	// Уровень вложенности меню
		"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
		"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		"COMPONENT_TEMPLATE" => "catalog_horizontal",
		"COMPOSITE_FRAME_MODE" => "A",	// Голосование шаблона компонента по умолчанию
		"COMPOSITE_FRAME_TYPE" => "AUTO",	// Содержимое компонента
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
				</div>
			</div>

			<div class="row">
				<div class="col-md-12" style="margin-top:90px;margin-bottom:15px;">
					<div class="bx-inc-orginfo-phone">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?>
					</div>
					<div class="bx-worktime">
						<div class="bx-worktime-prop">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?>
						</div>
					</div>
					<div>
						<a href="/catalog" class="btn btn-default btn-lg" style="margin:20px 0px">Перейти в магазин</a>
					</div>
				</div>
			</div>
		</div>
	</header>

	<div class="promo_button">
		<div class="row">
			<div class="col-md-4">
				<b><a href="/price/"><i class="fa fa-file-excel-o"></i> Скачать прайс-лист</a></b>
			</div>
			<div class="col-md-4">
 <a href="/about/howto/"><i class="fa fa-question-circle"></i> Как оформить заказ</a>
			</div>
			<div class="col-md-4">
 <a href="#callback"><i class="fa fa-envelope-o"></i> Задать вопрос</a>
			</div>
		</div>
	</div>

	<div class="workarea promo">
			<div class="row">
				<div class="bx-content container">