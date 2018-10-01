<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("iblock");
?>

<?
$arWhereTakeData = array(
    "0" => GetMessage("BISEXPERT_OWLSLIDER_NE_VYBRANO"),
    "iblock" => GetMessage("BISEXPERT_OWLSLIDER_IZ_INFOBLOKOV")
);

if (CModule::IncludeModule("advertising")) {
    $arWhereTakeData['advert'] = GetMessage("BISEXPERT_OWLSLIDER_IZ_MODULA_REKLAMA");
}

// getting all types of iblocks
$arIBlockType = array("" => GetMessage("BISEXPERT_OWLSLIDER_VSE_TIPY"));
$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while ($arr=$rsIBlockType->Fetch()) {
	if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID)) {
		$arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
	}
}

// getting all iblocks
$arIBlock=array(0 => GetMessage("BISEXPERT_OWLSLIDER_VYBRATQ"));
$IBlockFilter = array("ACTIVE"=>"Y");
if ($arCurrentValues["IBLOCK_TYPE"]) $IBlockFilter["TYPE"] = $arCurrentValues["IBLOCK_TYPE"];
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), $IBlockFilter);
while($arr=$rsIBlock->Fetch()) {
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

// getting all sections
$arSections = array(false => GetMessage("BISEXPERT_OWLSLIDER_VSE_RAZDELY"));
if (intval($arCurrentValues['IBLOCK_ID']) > 0) {
	$rsSections = GetIBlockSectionList($arCurrentValues['IBLOCK_ID'], false, array('left_margin'=>"ASC", "NAME"=>"ASC"));
	while ($arSection = $rsSections->GetNext()) {
		$arSections[$arSection["ID"]] = str_repeat(". ", $arSection['DEPTH_LEVEL']-1)."[".$arSection["ID"]."] ".htmlspecialcharsback($arSection["NAME"]);
	}
}

// getting element properties
$arProperties = array(0=> GetMessage("BISEXPERT_OWLSLIDER_NE_ISPOLQZOVATQ"));
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr = $rsProp->Fetch()) $arProperties[$arr["ID"]] = "[".($arr["CODE"]?$arr["CODE"]:$arr["ID"])."] ".$arr["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
		"SOURCE" => array(
			"NAME" => GetMessage("BISEXPERT_OWLSLIDER_ISTOCNIK_DANNYH"),
		),
		"SORTING" => array(
			"NAME" => GetMessage("BISEXPERT_OWLSLIDER_SORTIROVKA_ELEMENTOV"),
		),
        "RESIZE" => array(
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_NASTROYKI_RESAYZA_IZ")
        ),
		"SLIDER" => array(
			"NAME" => GetMessage("BISEXPERT_OWLSLIDER_NASTROYKI_OTOBRAJENI"),
		),
	),
	"PARAMETERS" => array(
        "MAIN_TYPE" => array(
            "PARENT" => "SOURCE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_OTKUDA_BRATQ_ELEMENT"),
            "TYPE" => "LIST",
            "VALUES" => $arWhereTakeData,
            "REFRESH" => "Y",
        ),
        "COUNT" => array(
            "PARENT" => "SOURCE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_MAKSIMALQNOE_KOLICES"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "8",
        ),
        "WIDTH_RESIZE" => array(
            "PARENT" => "RESIZE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_MAKSIMALQNAA_SIRINA"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "",
        ),
        "HEIGHT_RESIZE" => array(
            "PARENT" => "RESIZE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_MAKSIMALQNAA_VYSOTA"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "",
        ),
        "IS_PROPORTIONAL" => array(
            "PARENT" => "RESIZE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SOHRANENIE_PROPORCIY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "ENABLE_OWL_CSS_AND_JS" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_VKLUCITQ_I_FA"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "ENABLE_JQUERY" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_VKLUCITQ_ESL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "RESPONSIVE" => array(
            "PARENT" => "SLIDER",
            "NAME" => "Responsive ".GetMessage("BISEXPERT_OWLSLIDER_REJIM"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "COMPOSITE" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_KOMPOZITNYY_REJIM"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "AUTO_PLAY" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_AVTO_START_POKAZA_SL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "AUTO_PLAY_SPEED" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SKOROST_AVTO_POKAZA"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "5000",
        ),
        "SCROLL_COUNT" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_KOLICESTVO_VIDIMYH_E"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "1",
        ),
        "SPECIAL_CODE" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_UNIKALQNYY_KOD_DLA_K"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "unic",
        ),
        "AUTO_HEIGHT" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_AVTO_VYRAVNIVANIE_PO"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "RANDOM_TRANSITION" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_RANDOM_TRANSITION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "TRANSITION_TYPE_FOR_ONE_ITEM" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_VID_ANIMACII_TOLQKO"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "default",
            "VALUES" => array(
                "default" => "default",
                "backSlide" => "backSlide",
                "fade" => "fade",
                "goDown" => "goDown",
                "fadeUp" => "fadeUp",
                "softScale-next" => "softScale-next",
                "softScale-prev" => "softScale-prev",
                "pressAway-next" => "pressAway-next",
                "pressAway-prev" => "pressAway-prev",
                "sideSwing-next" => "sideSwing-next",
                "sideSwing-prev" => "sideSwing-prev",
                "fortuneWheel-next" => "fortuneWheel-next",
                "fortuneWheel-prev" => "fortuneWheel-prev",
                "reveal-next" => "reveal-next",
                "reveal-prev" => "reveal-prev",
                "snapIn-next" => "snapIn-next",
                "snapIn-prev" => "snapIn-prev",
                "letMeIn-next" => "letMeIn-next",
                "letMeIn-prev" => "letMeIn-prev",
                "stickIt-next" => "stickIt-next",
                "stickIt-prev" => "stickIt-prev",
                "archiveMe-next" => "archiveMe-next",
                "archiveMe-prev" => "archiveMe-prev",
                "slideBehind-next" => "slideBehind-next",
                "slideBehind-prev" => "slideBehind-prev",
                "cliffDiving-next" => "cliffDiving-next",
                "cliffDiving-prev" => "cliffDiving-prev"
            ),
        ),
        "SLIDE_SPEED" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SKOROSTQ_ANIMACII_SL"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "200",
        ),
        "PAGINATION_SPEED" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SKOROSTQ_ANIMACII_PA"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "800",
        ),
        "REWIND_SPEED" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SKOROSTQ_OBRATNOY_PE"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "1000",
        ),
        "STOP_ON_HOVER" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_OSTANAVLIVATQ_SLAYDE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "IMAGE_CENTER" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_IMAGE_CENTER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "RANDOM" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_RANDOM"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "SHOW_DESCRIPTION_BLOCK" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SHOW_DESCRIPTION_BLOCK"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "NAVIGATION" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_POKAZATQ_NAVIGACIONN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "NAVIGATION_TYPE" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_NAVIGATION_TYPE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => "arrows",
            "VALUES" => array(
                "arrows" => GetMessage("BISEXPERT_OWLSLIDER_NAVIGATION_TYPE_ARROWS"),
                "text" => GetMessage("BISEXPERT_OWLSLIDER_NAVIGATION_TYPE_TEXT"),
            ),
            "REFRESH" => "Y",
        ),
        "PAGINATION" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_POKAZATQ_PAGINACIU"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "PAGINATION_NUMBERS" => array(
            "PARENT" => "SLIDER",
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_POKAZATQ_NOMERA_PAGI"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "DRAG_BEFORE_ANIM_FINISH" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_IGNORIROVATQ_ANIMACI"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "MOUSE_DRAG" => array(
            "PARENT" => "SLIDER",
            "NAME" => "Drag ".GetMessage("BISEXPERT_OWLSLIDER_MYSKOY_PO_KLIKU"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "TOUCH_DRAG" => array(
            "PARENT" => "SLIDER",
            "NAME" => "Drag ".GetMessage("BISEXPERT_OWLSLIDER_V_USTROYSTVAH"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "ITEMS_SCALE_UP" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_ESLI_VIDIMYH_ELEMENT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "DISABLE_LINK_DEV" => array(
            "PARENT" => "SLIDER",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_DISABLE_LINK_DEV"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
		"CACHE_TIME" => array("DEFAULT"=>3600),
	),
);

if ($arCurrentValues['MAIN_TYPE']=='advert') {
    $arAdvertTypeFields = Array("" =>GetMessage("BISEXPERT_OWLSLIDER_NE_VYBRANO1"));
    $res = CAdvType::GetList($by, $order, Array("ACTIVE" => "Y"),$is_filtered, "Y");
    while (is_object($res) && $ar = $res->GetNext())
    {
        $arAdvertTypeFields[$ar["SID"]] = "[".$ar["SID"]."] ".$ar["NAME"];
    }
    $arComponentParameters['PARAMETERS']['ADVERT_TYPE'] = array(
        "PARENT" => "SOURCE",
        "NAME" => GetMessage("BISEXPERT_OWLSLIDER_TIP_BANNEROV"),
        "TYPE" => "LIST",
        "VALUES" => $arAdvertTypeFields,
        "REFRESH" => "Y",
    );
}

if ($arCurrentValues['MAIN_TYPE']=='iblock') {
    $arComponentParameters['PARAMETERS']['IBLOCK_TYPE'] = array(
        "PARENT" => "SOURCE",
        "NAME" => GetMessage("BISEXPERT_OWLSLIDER_TIP_INFOBLOKA"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockType,
        "REFRESH" => "Y",
    );

    $arComponentParameters['PARAMETERS']['IBLOCK_ID'] = array(
        "PARENT" => "SOURCE",
        "NAME" => GetMessage("BISEXPERT_OWLSLIDER_INFOBLOK"),
        "TYPE" => "LIST",
        "MULTIPLE" => "N",
        "ADDITIONAL_VALUES" => "N",
        "VALUES" => $arIBlock,
        "REFRESH" => "Y",
    );

    if (intval($arCurrentValues['IBLOCK_ID']) > 0) {
        $arComponentParameters['PARAMETERS']['LINK_URL_PROPERTY_ID'] = array(
            "PARENT" => "SOURCE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SVOYSTVO_OTKUDA_BRAT"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arProperties,
            "REFRESH" => "N",
        );
        $arComponentParameters['PARAMETERS']['TEXT_PROPERTY_ID'] = array(
            "PARENT" => "SOURCE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_SVOYSTVO_OTKUDA_BRAT1"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arProperties,
            "REFRESH" => "N",
        );

        $arComponentParameters['PARAMETERS']['SECTION_ID'] = array(
            "PARENT" => "SOURCE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_RAZDEL"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arSections,
            "REFRESH" => "N",
        );

        $arComponentParameters['PARAMETERS']['INCLUDE_SUBSECTIONS'] = array(
            "PARENT" => "SOURCE",
            "NAME" => GetMessage("BISEXPERT_OWLSLIDER_VKLUCAA_DOCERNIE_RAZ"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        );

        $arSortingDir = array(
            'asc' => GetMessage("BISEXPERT_OWLSLIDER_PO_VOZRASTANIU"),
            'nulls,asc' => GetMessage("BISEXPERT_OWLSLIDER_PO_VOZRASTANIU_PUST"),
            'asc,nulls' => GetMessage("BISEXPERT_OWLSLIDER_PO_VOZRASTANIU_PUST1"),
            'desc' => GetMessage("BISEXPERT_OWLSLIDER_PO_UBYVANIU"),
            'nulls,desc' => GetMessage("BISEXPERT_OWLSLIDER_PO_UBYVANIU_PUSTYE"),
            'desc,nulls' => GetMessage("BISEXPERT_OWLSLIDER_PO_UBYVANIU_PUSTYE1"),
        );

        $arSortingFields = array(
            "id" => "ID",
            "timestamp_x" => GetMessage("BISEXPERT_OWLSLIDER_POSLEDNEE_IZMENENIE"),
            "name" => GetMessage("BISEXPERT_OWLSLIDER_NAZVANIE"),
            "active_from" => GetMessage("BISEXPERT_OWLSLIDER_DATA_NACALA_AKTIVNOS"),
            "active_to" => GetMessage("BISEXPERT_OWLSLIDER_DATA_OKONCANIA_AKTIV"),
            "show_counter_start" => GetMessage("BISEXPERT_OWLSLIDER_VREMA_PERVOGO_POKAZA"),
            "shows" => GetMessage("BISEXPERT_OWLSLIDER_USREDNENNOE_KOLICEST"),
            "rand" => GetMessage("BISEXPERT_OWLSLIDER_SLUCAYNYM_OBRAZOM"),
        );

        if ($arCurrentValues["IBLOCK_ID"] > 0) {
            $rsProps = CIBlockProperty::GetList(array("NAME"=>"ASC"), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"])); // ��������� �� ���� ��������� �����
            while ($arProp = $rsProps->GetNext()) {
                $arSortingFields['property_'.$arProp['ID']] = GetMessage("BISEXPERT_OWLSLIDER_SVOYSTVO").$arProp['NAME'];
            }
        }

        if (empty($arCurrentValues['SORT_FIELD_1'])) $arCurrentValues['SORT_FIELD_1'] = "id";
        $count = 1;
        do {
            if ($count == 2) {
                $arSortingFields = array_merge(array(""=>GetMessage("BISEXPERT_OWLSLIDER_NE_ISPOLQZOVATQ")), $arSortingFields);
            }
            $arComponentParameters['PARAMETERS']["SORT_FIELD_".$count] = array(
                "PARENT" => "SORTING",
                "NAME" => GetMessage("BISEXPERT_OWLSLIDER_POLE_DLA_SORTIROVKI").$count,
                "TYPE" => "LIST",
                "MULTIPLE" => "N",
                "ADDITIONAL_VALUES" => "N",
                "VALUES" => $arSortingFields,
                "REFRESH" => "Y",
            );
            if ($count == 1) {
                $arComponentParameters['PARAMETERS']["SORT_FIELD_".$count]['DEFAULT'] = "id";
            }
            $arComponentParameters['PARAMETERS']["SORT_DIR_".$count] = array(
                "PARENT" => "SORTING",
                "NAME" => GetMessage("BISEXPERT_OWLSLIDER_NAPRAVLENIE_SORTIROV").$count,
                "TYPE" => "LIST",
                "MULTIPLE" => "N",
                "DEFAULT" => "DESC",
                "ADDITIONAL_VALUES" => "N",
                "VALUES" => $arSortingDir,
                "REFRESH" => "N",
            );
            $count++;
        } while (strlen($arCurrentValues['SORT_FIELD_'.($count-1)]));
    }
}

if ($arCurrentValues['NAVIGATION_TYPE']=='text') {
    $arComponentParameters['PARAMETERS']['NAVIGATION_TEXT_BACK'] = array(
        "PARENT" => "SLIDER",
        "NAME" => GetMessage("BISEXPERT_OWLSLIDER_NAZVANIE_DLA_KNOPKI"),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "ADDITIONAL_VALUES" => "N",
        "DEFAULT" => GetMessage("BISEXPERT_OWLSLIDER_NAZAD"),
    );

    $arComponentParameters['PARAMETERS']['NAVIGATION_TEXT_NEXT'] = array(
        "PARENT" => "SLIDER",
        "NAME" => GetMessage("BISEXPERT_OWLSLIDER_NAZVANIE_DLA_KNOPKI1"),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "ADDITIONAL_VALUES" => "N",
        "DEFAULT" => GetMessage("BISEXPERT_OWLSLIDER_VPERED"),
    );
}

?>
