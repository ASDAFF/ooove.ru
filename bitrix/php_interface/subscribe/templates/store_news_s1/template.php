<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $SUBSCRIBE_TEMPLATE_RUBRIC;
$SUBSCRIBE_TEMPLATE_RUBRIC=$arRubric;
global $APPLICATION;
?>
<STYLE type=text/css>
.text {font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px; color: #1C1C1C; font-weight: normal;}
.newsdata{font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #346BA0; text-decoration:none;}
H1 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#346BA0; font-size:15px; font-weight:bold; line-height: 16px; margin-bottom: 1mm;}
</STYLE>

<p>Добрый день!</p>
<p>Свежий прайс-лист в формате Excel мы выложили специально для вас <a href="http://ooove.ru/download/ooove_ru_opt_price.xls
">по этой ссылке</a>.</p>
<p>Ждем Ваших заказов!</p>
<br>
<P>Новости магазина</P>
<P><?$SUBSCRIBE_TEMPLATE_RESULT = $APPLICATION->IncludeComponent(
	"bitrix:subscribe.news",
	"",
	Array(
		"SITE_ID" => "s1",
		"IBLOCK_TYPE" => "news",
		"ID" => "1",
		"SORT_BY" => "ACTIVE_FROM",
		"SORT_ORDER" => "DESC",
	),
	null,
	array(
		"HIDE_ICONS" => "Y",
	)
);?></P>
<br>
<p>Вы получаете эту рассылку, так как дали свое согласие получать на этот e-mail прайс-лист компании ООО "Восточный экспресс" на сайте ooove.ru.
	<br><a href="http://ooove.ru/personal/subscribe/">Отписаться от получения прайс-листа</a>
</p>

<?

if($SUBSCRIBE_TEMPLATE_RESULT)
	return array(
		"SUBJECT"=>$SUBSCRIBE_TEMPLATE_RUBRIC["NAME"],
		"BODY_TYPE"=>"html",
		"CHARSET"=>"UTF-8",
		"DIRECT_SEND"=>"Y",
		"FROM_FIELD"=>$SUBSCRIBE_TEMPLATE_RUBRIC["FROM_FIELD"],
	);
else
	return false;
?>