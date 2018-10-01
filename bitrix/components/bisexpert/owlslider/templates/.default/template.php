<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arParams['COMPOSITE'] == 'Y'):?>
<?if(method_exists($this, 'createFrame')) $frame = $this->createFrame()->begin(GetMessage('COMPOSITE_INIT'));?>
<?endif;?>

<?
    if (!function_exists('JSParamValueBool')) {
        function JSParamValueBool($whatToCheck) {
            $jsBool = 'false';
            if ($whatToCheck) {
                if ($whatToCheck=='Y') {
                    $jsBool = 'true';
                }
            }
            return $jsBool;
        }
    }
?>

<?if (count($arResult['ITEMS']) > 0):?>
	<table class="owl-main-wrap">
		<tr>
			<td class="owl-main-wrap__inner">
				<div id="owl-carousel-<?=$arParams['SPECIAL_CODE'];?>" class="owl-carousel <?if ($arParams['RESPONSIVE']=='Y') echo 'owl-theme--responsive';?> <?if ($arParams['NAVIGATION_TYPE']=='arrows') echo 'owl-theme--arrows';?> <?if ($arParams['IMAGE_CENTER']=='Y') echo 'owl-theme--center';?>">
					<?foreach($arResult['ITEMS'] as $item):?>
						<div class="owl-carousel__item">

                            <?if ($item['AD_TYPE'] == 'flash'):?>
                                <div class="owl-carousel__object-item">
                                    <a target="<?=$item['ITEM_URL_TARGET'];?>" class="owl-carousel__object-item__link" href="<?=$item['ITEM_URL'];?>"></a>
                                    <figure>
                                        <object type="application/x-shockwave-flash" data="<?=$item['PICTURE_RESIZED']['src'];?>" height="<?=$item['PICTURE_RESIZED']['height'];?>" width="<?=$item['PICTURE_RESIZED']['width'];?>">
                                            <param name="movie" value="<?=$item['PICTURE_RESIZED']['src'];?>" />
                                            <param name="quality" value="high"/>
                                            <param name="wmode" value="opaque" />
                                            <embed style="z-index:0;" src="<?=$item['PICTURE_RESIZED']['src'];?>" height="<?=$item['PICTURE_RESIZED']['height'];?>" width="<?=$item['PICTURE_RESIZED']['width'];?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="opaque">
                                        </object>
                                    </figure>
                                </div>
							<?elseif ($item['AD_TYPE'] == 'html'):?>
								<div class="owl-carousel__html-item">
									<?=$item['CODE'];?>
								</div>
                            <?else:?>
                                <?if ($item['ITEM_URL'] != ''):?>
                                <a target="<?=$item['ITEM_URL_TARGET'];?>" class="owl-carousel__item-link" href="<?=$item['ITEM_URL'];?>">
                                <?endif;?>
                                    <img class="adaptive-img" title="<?=$item['ITEM_TEXT'];?>" alt="<?=$item['NAME'];?>" src="<?=$item['PICTURE_RESIZED']['src'];?>" />
                                <?if ($item['ITEM_URL'] != ''):?>
                                </a>
                                <?endif;?>
                            <?endif;?>

                            <?if ($arParams['SHOW_DESCRIPTION_BLOCK'] == 'Y'):?>
                            <span class="owl-carousel__item-descr">
                                <b><?=$item['NAME'];?></b><br/>
                                <?if ($item['NAME'] != $item['ITEM_TEXT']) {
                                    echo $item['ITEM_TEXT'];;
                                }
                                ?>
                            </span>
                            <?endif;?>
						</div>
					<?endforeach;?>
				</div>
                <?if ($arParams['DISABLE_LINK_DEV'] != 'Y'):?>
                    <a target="_blank" class="owl-dev-link" href="http://bis-expert.com/"><?=GetMessage('BISEXPERT_OWLSLIDER_DEV_LINK');?></a>
                <?endif;?>
			</td>
		</tr>
	</table>

    <script type="text/javascript">
        (function ($) {
            $(function() {

                $('#owl-carousel-<?=$arParams['SPECIAL_CODE'];?>').owlCarousel({
                    // Most important owl features
                    items : "<?=$arParams['SCROLL_COUNT']?>",
                    itemsCustom : false,
                    itemsDesktop : [1199,4],
                    itemsDesktopSmall : [980,3],
                    itemsTablet: [768,2],
                    itemsTabletSmall: false,
                    itemsMobile : [479,1],

                    <?if ($arParams['SCROLL_COUNT']==1):?>
                    singleItem: true,
                    <?endif;?>

                    itemsScaleUp : <?=JSParamValueBool($arParams['ITEMS_SCALE_UP'])?>,

                    //Basic Speeds
                    slideSpeed : <?=$arParams['SLIDE_SPEED'];?>,
                    paginationSpeed : <?=$arParams['PAGINATION_SPEED'];?>,
                    rewindSpeed : <?=$arParams['REWIND_SPEED'];?>,

                    //Autoplay
                    <?if ($arParams['AUTO_PLAY'] == 'Y'):?>
                    autoPlay: <?=$arParams['AUTO_PLAY_SPEED']?>,
                    <?else:?>
                    autoPlay: false,
                    <?endif;?>

                    stopOnHover : <?=JSParamValueBool($arParams['STOP_ON_HOVER'])?>,

                    // Navigation
                    navigation : <?=JSParamValueBool($arParams['NAVIGATION'])?>,
                    navigationText : ["<?=$arParams['NAVIGATION_TEXT_BACK']?>","<?=$arParams['NAVIGATION_TEXT_NEXT']?>"],
                    rewindNav : true,
                    scrollPerPage : false,

                    //Pagination
                    pagination : <?=JSParamValueBool($arParams['PAGINATION'])?>,
                    paginationNumbers: <?=JSParamValueBool($arParams['PAGINATION_NUMBERS'])?>,

                    // Responsive
                    responsive: <?=JSParamValueBool($arParams['RESPONSIVE'])?>,
                    responsiveRefreshRate : 200,
                    responsiveBaseWidth: window,

                    // CSS Styles
                    baseClass : "owl-carousel",
                    theme : "owl-theme",

                    //Lazy load
                    lazyLoad : false,
                    lazyFollow : true,
                    lazyEffect : "fade",

                    //Auto height
                    <?if ($arParams['SCROLL_COUNT']==1):?>
                    autoHeight : <?=JSParamValueBool($arParams['AUTO_HEIGHT'])?>,
                    <?endif;?>

                    //JSON
                    jsonPath : false,
                    jsonSuccess : false,

                    //Mouse Events
                    dragBeforeAnimFinish : <?=JSParamValueBool($arParams['DRAG_BEFORE_ANIM_FINISH'])?>,
                    mouseDrag :  <?=JSParamValueBool($arParams['MOUSE_DRAG'])?>,
                    touchDrag :  <?=JSParamValueBool($arParams['TOUCH_DRAG'])?>,

                    <?if ($arParams['SCROLL_COUNT']==1 && $arParams['TRANSITION_TYPE_FOR_ONE_ITEM']!='default' && $arParams['RANDOM_TRANSITION'] !='Y'):?>
                    //Transitions
                    transitionStyle : "<?=$arParams['TRANSITION_TYPE_FOR_ONE_ITEM']?>",
                    <?elseif ($arParams['TRANSITION_TYPE_FOR_ONE_ITEM']=='default'):?>
                    transitionStyle: false,
                    <?else:?>
                    transitionStyle: '',
                    <?endif;?>

                    // Other
                    addClassActive : false,

                    //Callbacks
                    beforeUpdate : false,
                    afterUpdate : false,
                    beforeInit: function(elem) {
                        <?if ($arParams['RANDOM']=='Y'):?>
                        elem.children().sort(function(){
                            return Math.round(Math.random()) - 0.5;
                        }).each(function(){
                            $(this).appendTo(elem);
                        });
                        <?endif;?>
                    },
                    afterInit: false,
                    beforeMove: false,
                    afterMove: function() {
                        <?if ($arParams['RANDOM_TRANSITION']=='Y'):?>
                        var transition = getRandomTransition(),
                            inClass = outClass = null;

                        if (this.currentItem >= this.prevItem ) {
                            this.inClass = transition.getInClass(true);
                            this.outClass = transition.getOutClass(true);
                        }
                        else {
                            this.inClass = transition.getInClass();
                            this.outClass = transition.getOutClass();
                        }
                        <?endif;?>
                    },
                    afterAction: false,
                    startDragging : false,
                    afterLazyLoad : false
                });

                <?if ($arParams['RANDOM_TRANSITION']=='Y'):?>
                //random animation util functions
                var tNames = ['softScale', 'pressAway', 'sideSwing', 'fortuneWheel', 'reveal', 'snapIn', 'letMeIn', 'stickIt', 'archiveMe', 'cliffDiving'];

                var transitions = (function () {
                    var obj = {};
                    for (var i = 0, len = tNames.length; i < len; i++)	 {
                        var tName = tNames[i];
                        obj[tName] = {
                            name: tName,
                            inClassNext: 'owl-' + tName + '-next-in',
                            outClassNext: 'owl-' + tName + '-next-out',
                            inClassPrev: 'owl-' + tName + '-prev-in',
                            outClassPrev: 'owl-' + tName + '-prev-out',
                            getInClass: function (next) {
                                return next !== undefined ? this.inClassNext : this.inClassPrev;
                            },
                            getOutClass: function (next) {
                                return next !== undefined ? this.outClassNext : this.outClassPrev;
                            }
                        }
                    }

                    return obj;
                })();

                function getRandomTransition() {
                    var idx = Math.floor(Math.random() * tNames.length);
                    return transitions[tNames[idx]];
                }
                <?endif;?>

                //responsive for flash
                var flashWrapItems = $('.owl-carousel__object-item');
                var flashItems = flashWrapItems.find("object, embed");
                var flashFluidItems = flashWrapItems.find('figure');

                if (flashWrapItems.length) {
                    flashItems.each(function() {
                        $(this)
                            // jQuery .data does not work on object/embed elements
                            .attr('data-aspectRatio', this.height / this.width)
                            .removeAttr('height')
                            .removeAttr('width');
                    });

                    $(window).resize(function() {
                        var newWidth = flashFluidItems.width();
                        flashItems.each(function() {
                            var $el = $(this);
                            $el
                                .width(newWidth)
                                .height(newWidth * $el.attr('data-aspectRatio'));
                        });
                    }).resize();
                }
            })
        }(jQuery));
    </script>
<?endif;?>


<?if ($arParams['COMPOSITE'] == 'Y'):?>
<?if(method_exists($this, 'createFrame')) $frame->end();?>
<?endif;?>