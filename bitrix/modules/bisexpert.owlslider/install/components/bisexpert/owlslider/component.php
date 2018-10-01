<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arParams["MAIN_TYPE"] = (isset($arParams["MAIN_TYPE"]) ? trim($arParams["MAIN_TYPE"]) : "");

if (!$arParams["MAIN_TYPE"]) {
    ShowMessage(GetMessage("BISEXPERT_OWLSLIDER_OSIBKA_VY_NE_VYBRAL"));
    return;
}
if ($arParams["MAIN_TYPE"]=='advert') {
    if (!$arParams["ADVERT_TYPE"]) {
        ShowMessage(GetMessage("BISEXPERT_OWLSLIDER_OSIBKA_VY_NE_VYBRAL1"));
        return;
    }
}

if ($arParams["MAIN_TYPE"]=='iblock') {
    if (!$arParams["IBLOCK_TYPE"]) {
        ShowMessage(GetMessage("BISEXPERT_OWLSLIDER_OSIBKA_VY_NE_VYBRAL2"));
        return;
    }
    if (!$arParams["IBLOCK_ID"]) {
        ShowMessage(GetMessage("BISEXPERT_OWLSLIDER_OSIBKA_VY_NE_VYBRAL3"));
        return;
    }
}

$arParams["ADVERT_TYPE"] = (isset($arParams["ADVERT_TYPE"]) ? trim($arParams["ADVERT_TYPE"]) : "");

if ($arParams['INCLUDE_SUBSECTIONS']!="N") $arParams['INCLUDE_SUBSECTIONS'] = "Y";

if (!$arParams['COUNT']) $arParams['COUNT'] = 8;
if (!strlen($arParams['SPECIAL_CODE'])) $arParams['SPECIAL_CODE'] = "unic";
if (!strlen($arParams['AUTO_PLAY_SPEED'])) $arParams['AUTO_PLAY_SPEED'] = "5000";
if (!strlen($arParams['SLIDE_SPEED'])) $arParams['SLIDE_SPEED'] = "200";
if (!strlen($arParams['PAGINATION_SPEED'])) $arParams['PAGINATION_SPEED'] = "800";
if (!strlen($arParams['REWIND_SPEED'])) $arParams['REWIND_SPEED'] = "1000";

if (!strlen($arParams['SCROLL_COUNT'])) $arParams['SCROLL_COUNT'] = "8";
if (!strlen($arParams['NAVIGATION_TEXT_BACK'])) $arParams['NAVIGATION_TEXT_BACK'] = GetMessage("BISEXPERT_OWLSLIDER_NAZAD");
if (!strlen($arParams['NAVIGATION_TEXT_NEXT'])) $arParams['NAVIGATION_TEXT_NEXT'] = GetMessage("BISEXPERT_OWLSLIDER_VPERED");

if (!strlen($arParams['WIDTH_RESIZE'])) $arParams['WIDTH_RESIZE'] = "";
if (!strlen($arParams['HEIGHT_RESIZE'])) $arParams['HEIGHT_RESIZE'] = "";
if ($arParams['IS_PROPORTIONAL']!="N") $arParams['IS_PROPORTIONAL'] = "Y"; else $arParams['IS_PROPORTIONAL'] = "N";

if ($arParams['RESPONSIVE']!="N") $arParams['RESPONSIVE'] = "Y"; else $arParams['RESPONSIVE'] = "N";
if ($arParams['COMPOSITE']!="N") $arParams['COMPOSITE'] = "Y"; else $arParams['COMPOSITE'] = "N";
if ($arParams['AUTO_PLAY']!="N") $arParams['AUTO_PLAY'] = "Y"; else $arParams['AUTO_PLAY'] = "N";
if ($arParams['STOP_ON_HOVER']!="N") $arParams['STOP_ON_HOVER'] = "Y"; else $arParams['STOP_ON_HOVER'] = "N";
if ($arParams['IMAGE_CENTER']!="Y") $arParams['IMAGE_CENTER'] = "N"; else $arParams['IMAGE_CENTER'] = "Y";
if ($arParams['RANDOM']!="Y") $arParams['RANDOM'] = "N"; else $arParams['RANDOM'] = "Y";
if ($arParams['SHOW_DESCRIPTION_BLOCK']!="Y") $arParams['SHOW_DESCRIPTION_BLOCK'] = "N"; else $arParams['SHOW_DESCRIPTION_BLOCK'] = "Y";
if ($arParams['RANDOM_TRANSITION']!="Y") $arParams['RANDOM_TRANSITION'] = "N"; else $arParams['RANDOM_TRANSITION'] = "Y";
if ($arParams['ENABLE_JQUERY']!="N") $arParams['ENABLE_JQUERY'] = "Y"; else $arParams['ENABLE_JQUERY'] = "N";
if ($arParams['NAVIGATION']!="N") $arParams['NAVIGATION'] = "Y"; else $arParams['NAVIGATION'] = "N";
if ($arParams['PAGINATION']!="N") $arParams['PAGINATION'] = "Y"; else $arParams['PAGINATION'] = "N";
if ($arParams['PAGINATION_NUMBERS']!="N") $arParams['PAGINATION_NUMBERS'] = "Y"; else $arParams['PAGINATION_NUMBERS'] = "N";
if ($arParams['DRAG_BEFORE_ANIM_FINISH']!="N") $arParams['DRAG_BEFORE_ANIM_FINISH'] = "Y"; else $arParams['DRAG_BEFORE_ANIM_FINISH'] = "N";
if ($arParams['MOUSE_DRAG']!="N") $arParams['MOUSE_DRAG'] = "Y"; else $arParams['MOUSE_DRAG'] = "N";
if ($arParams['TOUCH_DRAG']!="N") $arParams['TOUCH_DRAG'] = "Y"; else $arParams['TOUCH_DRAG'] = "N";
if ($arParams['AUTO_HEIGHT']!="N") $arParams['AUTO_HEIGHT'] = "Y"; else $arParams['AUTO_HEIGHT'] = "N";
if ($arParams['ITEMS_SCALE_UP']!="N") $arParams['ITEMS_SCALE_UP'] = "Y"; else $arParams['ITEMS_SCALE_UP'] = "N";
if ($arParams['ENABLE_OWL_CSS_AND_JS']!="N") $arParams['ENABLE_OWL_CSS_AND_JS'] = "Y"; else $arParams['ENABLE_OWL_CSS_AND_JS'] = "N";

$arResult = array();
$arResult['ITEMS'] = array();

if ($this->StartResultCache(false, $USER->GetGroups())) {

    if ($arParams['MAIN_TYPE']=='iblock') {
        CModule::IncludeModule("iblock");

        foreach ($arParams as $k=>$param) {
            if (strpos($k, "SORT_FIELD_", 0) === 0) {
                $i = str_replace("SORT_FIELD_", "", $k);
                if (strlen($param)) {
                    $arSorting[strtoupper($param)] = $arParams['SORT_DIR_'.$i];
                }
            }
        }

        if (!$arSorting) $arSorting['SORT']="ASC";

        $arFilter = array(
            'IBLOCK_ID'=>$arParams['IBLOCK_ID'],
            'ACTIVE'=>"Y",
            'ACTIVE_DATE'=>"Y"
        );

        if ($arParams['SECTION_ID']) $arFilter['SECTION_ID'] = $arParams['SECTION_ID'];
        if ($arParams['INCLUDE_SUBSECTIONS']=="Y") $arFilter['INCLUDE_SUBSECTIONS'] = "Y";

        $rs = CIBlockElement::GetList(
            $arSorting,
            $arFilter,
            false,
            array('nTopCount'=>$arParams['COUNT']),
            array(
                'ID',
                'NAME',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'PROPERTY_*'
            )
        );

        while ($item = $rs->GetNext()) {
            $arResult['ITEMS'][] = $item;
        }
    }
    elseif ($arParams['MAIN_TYPE']=='advert') {
        $arFilter = Array(
            //"TYPE" => $arParams["ADVERT_TYPE"],
            "TYPE_SID" => $arParams["ADVERT_TYPE"],
            "ACTIVE" => "Y"
        );

        $rsBanners = CAdvBanner::GetList($by="s_weight", $order="desc", $arFilter, $is_filtered, "N");

        while($arBannerCur = $rsBanners->GetNext())	{
            array_push($arResult["ITEMS"], $arBannerCur);
        }
    }

    $this->IncludeComponentTemplate();
}

if ($arParams['ENABLE_JQUERY'] == 'Y') {
    $APPLICATION->AddHeadScript("/bitrix/js/bisexpert.owlslider/jquery-1.9.1.min.js");
}
?>

<?if ($arParams['ENABLE_OWL_CSS_AND_JS'] == 'Y'):?>
    <?
    $APPLICATION->SetAdditionalCSS($this->GetPath()."/theme/owl.carousel.css");
    $APPLICATION->SetAdditionalCSS($this->GetPath()."/theme/owl.theme.css");
    $APPLICATION->SetAdditionalCSS($this->GetPath()."/theme/owl.transitions.css");
    ?>
    <script src="/bitrix/js/bisexpert.owlslider/owl.carousel.min.js" type="text/javascript"></script>
<?endif;?>


