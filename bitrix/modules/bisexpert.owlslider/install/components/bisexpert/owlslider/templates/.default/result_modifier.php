<?//some final manipulations ?>

<?if ($arResult['ITEMS']):?>

    <?
    $currentTime = time();
    ?>

    <?foreach($arResult['ITEMS'] as $key => $item):?>
        <?
        if ($item['DETAIL_PICTURE']) {
            $arResult['ITEMS'][$key]['PICTURE_ID'] = $item['DETAIL_PICTURE'];
        } elseif ($item['PREVIEW_PICTURE']) {
            $arResult['ITEMS'][$key]['PICTURE_ID'] = $item['PREVIEW_PICTURE'];
        } elseif($item['IMAGE_ID']) {
            $arResult['ITEMS'][$key]['PICTURE_ID'] = $item['IMAGE_ID'];
        }
        elseif($item['AD_TYPE'] == 'html') {
            //nothing yet
        } else {
            unset($arResult['ITEMS'][$key]);
            continue;
        }

        $arrPictureSizes = array();

        if (intval($arParams['WIDTH_RESIZE']) > 0) {
            $arrPictureSizes['width'] = intval($arParams['WIDTH_RESIZE']);
        }

        if (intval($arParams['HEIGHT_RESIZE']) > 0) {
            $arrPictureSizes['height'] = intval($arParams['HEIGHT_RESIZE']);
        }

        $pictureResizeType = BX_RESIZE_IMAGE_EXACT;
        if ($arParams['IS_PROPORTIONAL']=='Y') {
            $pictureResizeType = BX_RESIZE_IMAGE_PROPORTIONAL_ALT;
        }

        $arResult['ITEMS'][$key]['PICTURE_RESIZED'] = CFile::ResizeImageGet($arResult['ITEMS'][$key]['PICTURE_ID'], $arrPictureSizes, $pictureResizeType );

        // IBLOCK Items:
        if ($arParams['MAIN_TYPE']=='iblock') {
            $itemUrl = '';
            $itemUrlPropLink = 'PROPERTY_'.$arParams['LINK_URL_PROPERTY_ID'];

            if ($item[$itemUrlPropLink]) {
                $arResult['ITEMS'][$key]['ITEM_URL'] = $item[$itemUrlPropLink];
            }

            $itemText = '';
            $itemTextPropLink = 'PROPERTY_'.$arParams['TEXT_PROPERTY_ID'];

            if ($item[$itemTextPropLink]) {
                $arResult['ITEMS'][$key]['ITEM_TEXT'] = $item[$itemTextPropLink];
            } else {
                $arResult['ITEMS'][$key]['ITEM_TEXT'] = $item['NAME'];
            }
        }

        // ADVERT Items:
        if ($arParams['MAIN_TYPE']=='advert') {
            if ($item['DATE_SHOW_FROM'] != '') {
                if (strtotime($item['DATE_SHOW_FROM']) > $currentTime) {
                    unset($arResult['ITEMS'][$key]);
                    continue;
                }
            } elseif ($item['DATE_SHOW_TO'] != '') {
                if (strtotime($item['DATE_SHOW_TO']) <= $currentTime) {
                    unset($arResult['ITEMS'][$key]);
                    continue;
                }
            }

            $arResult['ITEMS'][$key]['ITEM_URL'] = $item['URL'];
            $arResult['ITEMS'][$key]['ITEM_TEXT'] = ($item['IMAGE_ALT'] != '') ? $item['IMAGE_ALT'] : $item['NAME'];
        }

        $arResult['ITEMS'][$key]['ITEM_URL_TARGET'] = ($item['URL_TARGET'] == '') ? '_self' : $item['URL_TARGET'];
        ?>

    <?endforeach;?>
<?endif;?>

