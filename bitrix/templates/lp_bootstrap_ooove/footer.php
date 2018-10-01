
					<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
				</div>
			</div><!--//row-->

			<div class="row action" id="callback">
				<div class="col-md-6 action_block1">ЗАДАЙТЕ СВОЙ<BR>ВОПРОС<BR><span style="font-size:30%;">ЗАПОЛНИТЕ, ПОЖАЛУЙСТА, ФОРМУ<br>И МЫ ПЕРЕЗВОНИМ ВАМ</span></div>
				<div class="col-md-6">
 <?$APPLICATION->IncludeComponent(
	"kreattika:forms.feedback", 
	"ooove_feedback", 
	array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CHECK_FIELD_EMAIL" => "Y",
		"CHECK_FIELD_NAME" => "Y",
		"CHECK_FIELD_PHONE" => "Y",
		"CHECK_FIELD_TEXT" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"EMAIL_TO" => "sale@ooove.ru",
		"EVENT_MESSAGE_ID" => array(
			0 => "51",
		),
		"FIELD_EMAIL_TITLE" => "",
		"FIELD_NAME_TITLE" => "",
		"FIELD_PHONE_TITLE" => "",
		"FIELD_TEXT_TITLE" => "",
		"OK_TEXT" => "Ваше сообщение принято. Наши менеджеры свяжутся с Вами в самое ближайшее время. Удачного дня.",
		"SUBMIT_TITLE" => "Отправить",
		"USE_CAPTCHA" => "Y",
		"USE_FIELD_EMAIL" => "Y",
		"USE_FIELD_NAME" => "Y",
		"USE_FIELD_PHONE" => "Y",
		"USE_FIELD_TEXT" => "Y",
		"COMPONENT_TEMPLATE" => "ooove_feedback"
	),
	false
);?></div>
			</div>

<div class="ooove-slider">
<?$APPLICATION->IncludeComponent("bisexpert:owlslider", "template_ooove", array(
	"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"MAIN_TYPE" => "iblock",
		"COUNT" => "5",
		"WIDTH_RESIZE" => "1900",
		"HEIGHT_RESIZE" => "400",
		"IS_PROPORTIONAL" => "Y",
		"ENABLE_OWL_CSS_AND_JS" => "Y",
		"ENABLE_JQUERY" => "Y",
		"RESPONSIVE" => "Y",
		"COMPOSITE" => "Y",
		"AUTO_PLAY" => "Y",
		"AUTO_PLAY_SPEED" => "5000",
		"SCROLL_COUNT" => "1",
		"SPECIAL_CODE" => "unic",
		"AUTO_HEIGHT" => "Y",
		"TRANSITION_TYPE_FOR_ONE_ITEM" => "fade",
		"SLIDE_SPEED" => "200",
		"PAGINATION_SPEED" => "800",
		"REWIND_SPEED" => "1000",
		"STOP_ON_HOVER" => "Y",
		"IMAGE_CENTER" => "Y",
		"NAVIGATION" => "N",
		"NAVIGATION_TYPE" => "text",
		"PAGINATION" => "Y",
		"PAGINATION_NUMBERS" => "N",
		"DRAG_BEFORE_ANIM_FINISH" => "Y",
		"MOUSE_DRAG" => "Y",
		"TOUCH_DRAG" => "Y",
		"ITEMS_SCALE_UP" => "Y",
		"COMPONENT_TEMPLATE" => "template_ooove",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "6",
		"LINK_URL_PROPERTY_ID" => "0",
		"TEXT_PROPERTY_ID" => "0",
		"SECTION_ID" => "0",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SORT_FIELD_1" => "id",
		"SORT_DIR_1" => "asc",
		"SORT_FIELD_2" => "",
		"SORT_DIR_2" => "asc",
		"RANDOM_TRANSITION" => "Y",
		"RANDOM" => "N",
		"SHOW_DESCRIPTION_BLOCK" => "N",
		"DISABLE_LINK_DEV" => "N",
		"NAVIGATION_TEXT_BACK" => "назад",
		"NAVIGATION_TEXT_NEXT" => "вперед"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
	</div>

		<div style="background:white;padding:50px;">
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "sect",
						"AREA_FILE_SUFFIX" => "bottom",
						"AREA_FILE_RECURSIVE" => "N",
						"EDIT_MODE" => "html",
					),
					false,
					Array('HIDE_ICONS' => 'Y')
				);?>
<h2>Новое поступление</h2>
 <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	".default", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => ".default",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "id",
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "",
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "Y",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_TYPE_ID" => "catalog",
		"INCLUDE_SUBSECTIONS" => "A",
		"LABEL_PROP" => array(
		),
		"LINE_ELEMENT_COUNT" => "5",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
		),
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "0",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
			3 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "desc",
		"OFFERS_SORT_ORDER2" => "desc",
		"OFFER_ADD_PICT_PROP" => "-",
		"OFFER_TREE_PROPS" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
		),
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "round",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "4",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "Опт №2",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "",
		),
		"SECTION_CODE" => "",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"TEMPLATE_THEME" => "blue",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[{\"CLASS_ID\":\"CondCatQuantity\",\"DATA\":{\"logic\":\"Great\",\"value\":5}}]}",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"PROPERTY_CODE_MOBILE" => array(
			0 => "CML2_ARTICLE",
		),
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false}]",
		"ENLARGE_PRODUCT" => "STRICT",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"SHOW_SLIDER" => "Y",
		"LABEL_PROP_MOBILE" => "",
		"LABEL_PROP_POSITION" => "top-left",
		"DISCOUNT_PERCENT_POSITION" => "bottom-right",
		"SHOW_MAX_QUANTITY" => "N",
		"RCM_TYPE" => "personal",
		"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
		"SHOW_FROM_SECTION" => "N",
		"LAZY_LOAD" => "N",
		"LOAD_ON_SCROLL" => "N",
		"COMPATIBLE_MODE" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"ENLARGE_PROP" => "-",
		"SLIDER_INTERVAL" => "3000",
		"SLIDER_PROGRESS" => "N",
		"DISPLAY_COMPARE" => "N"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?>
	</div>


		</div><!--//workarea-->

		<footer class="bx-footer">

			<div class="bx-footer-section container bx-center-section ooove-footer">
				<div class="col-sm-5 col-md-3 col-md-push-6">
					<h4 class="bx-block-title"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/about_title.php"), false);?></h4>
					<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom_menu", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_TYPE" => "A",
		"CACHE_SELECTED_ITEMS" => "N",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"COMPONENT_TEMPLATE" => "bottom_menu",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
				</div>


				<div class="col-sm-5 col-md-3">
					<h4 class="bx-block-title">Для партнеров</h4><br>
						<div>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/personal.php"), false);?>
						</div>


<!--
					<h4 class="bx-block-title"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/catalog_title.php"), false);?></h4>
					<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_menu", array(
	"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => "",
		"CACHE_SELECTED_ITEMS" => "N",
		"MAX_LEVEL" => "1",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"COMPONENT_TEMPLATE" => "bottom_menu",
		"CHILD_MENU_TYPE" => "left",
		"COMPOSITE_FRAME_MODE" => "Y",
		"COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "N"
	)
);?>
-->

				</div>


				<div class="col-sm-5 col-md-3 col-md-push-3">
					<div style="padding: 20px;background:#eaeaeb;color:black;">
						<p>OOOVE это:</p>
						<ul align="left">
						<li>Крупный поставщик качественных запчастей для китайских автомобилей</li>
						<li>Официальные поставки запчастей из Китая</li>
						</ul>
						</div>
					<div id="bx-composite-banner" style="padding-top: 20px"></div>

<!-- Yandex.Metrika informer -->
<a href="https://metrika.yandex.ru/stat/?id=41240939&amp;from=informer"
target="_blank" rel="nofollow"><img src="https://informer.yandex.ru/informer/41240939/3_1_FFFFFFFF_FFFFFFFF_0_pageviews"
	style="width:88px; height:31px; border:0; margin-top:20px;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" class="ym-advanced-informer" data-cid="41240939" data-lang="ru" /></a>
<!-- /Yandex.Metrika informer -->

				</div>
				<div class="col-sm-5 col-md-3 col-md-pull-9">
					<div class="bx-inclogofooter">
						<div class="bx-inclogofooter-block">
							<a class="bx-inclogofooter-logo" href="<?=SITE_DIR?>">
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo_mobile.php"), false);?>
							</a>
						</div>
						<div class="bx-inclogofooter-block">
							<div class="bx-inclogofooter-tel"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></div>
							<div class="bx-inclogofooter-worktime"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></div>
						</div>
					</div>
				</div>
			</div>
			<div class="bx-footer-bottomline">
				<div class="bx-footer-section container">
					<div class="col-sm-6"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/copyright.php"), false);?></div>
					<div class="col-sm-6 bx-up"><a href="jav * ascript:void(0)" data-role="eshopUpButton"><i class="fa fa-caret-up"></i> <?=GetMessage("FOOTER_UP_BUTTON")?></a></div>
				</div>
			</div>


		</footer>
		<div class="col-xs-12 hidden-lg hidden-md hidden-sm">
			<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "", array(
					"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
					"PATH_TO_PERSONAL" => SITE_DIR."personal/",
					"SHOW_PERSONAL_LINK" => "N",
					"SHOW_NUM_PRODUCTS" => "Y",
					"SHOW_TOTAL_PRICE" => "Y",
					"SHOW_PRODUCTS" => "N",
					"POSITION_FIXED" =>"Y",
					"POSITION_HORIZONTAL" => "center",
					"POSITION_VERTICAL" => "bottom",
					"SHOW_AUTHOR" => "Y",
					"PATH_TO_REGISTER" => SITE_DIR."login/",
					"PATH_TO_PROFILE" => SITE_DIR."personal/"
				),
				false,
				array()
			);?>
		</div>
	</div> <!-- //bx-wrapper -->

<script>
	BX.ready(function(){
		var upButton = document.querySelector('[data-role="eshopUpButton"]');
		BX.bind(upButton, "click", function(){
			var windowScroll = BX.GetWindowScrollPos();
			(new BX.easing({
				duration : 500,
				start : { scroll : windowScroll.scrollTop },
				finish : { scroll : 0 },
				transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
				step : function(state){
					window.scrollTo(0, state.scroll);
				},
				complete: function() {
				}
			})).animate();
		})
	});
</script>


<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter41240939 = new Ya.Metrika({
                    id:41240939,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true,
                    trackHash:true,
                    ecommerce:"dataLayer"
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/41240939" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<script> (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','//www.google-analytics.com/analytics.js','ga'); ga('create', 'UA-97504356-1', 'auto'); ga('send', 'pageview'); </script>

</body>
</html>