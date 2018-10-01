<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Наши контакты");
$APPLICATION->SetTitle("Как нас найти");
?><div class="row">
	<div class="col-xs-12">
		<p>
 <b>Телефон:</b>&nbsp;8 (499) 709 04 04<br>
 <b>e-mail:</b>&nbsp;<a href="mailto:sale@ooove.ru">sale@ooove.ru</a><br>
 <b>Адрес:</b>&nbsp;Московская область, г. Мытищи, 4536-й проезд. Заезд со стороны ул. Воронина
		</p>
		 <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2235.2557611954903!2d37.7562883156399!3d55.92760398060042!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTXCsDU1JzM5LjQiTiAzN8KwNDUnMzAuNSJF!5e0!3m2!1sru!2sru!4v1481884070192" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen></iframe><small><br>
 </small>
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