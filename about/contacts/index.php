<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Наши контакты");
$APPLICATION->SetTitle("Как нас найти");
?><div class="row">
	<div class="col-xs-12">


		<p>
 <b>Телефон:</b>&nbsp;8 (499) 709 04 04, +7 903 542 12 22<br>
 <b>e-mail:</b>&nbsp;<a href="mailto:sale@ooove.ru">sale@ooove.ru</a><br>
 <b>Адрес:</b>&nbsp;Московская область, г. Мытищи,  ул. Угольная, д.6
<br><br>
<a href="/about/contacts/en/">Translate to English</a>
		</p>

<iframe src="https://yandex.ru/map-widget/v1/-/CBagfAF1hB" width="100%" height="400" frameborder="0"></iframe>
<br>
 		<h2>Задать вопрос</h2>
		 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback", 
	"eshop", 
	array(
		"EMAIL_TO" => "sale@ooove.ru",
		"EVENT_MESSAGE_ID" => array(
		),
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"REQUIRED_FIELDS" => array(
		),
		"USE_CAPTCHA" => "Y",
		"COMPONENT_TEMPLATE" => "eshop",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
	</div>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>