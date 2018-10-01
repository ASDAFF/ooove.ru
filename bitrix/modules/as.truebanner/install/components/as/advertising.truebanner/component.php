<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) { die(); }

//Params
$arParams["TYPE"] = (isset($arParams["TYPE"]) ? trim($arParams["TYPE"]) : "");

if($arParams["NOINDEX"] <> "Y")
	$arParams["NOINDEX"] = "N";

if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
	$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
else
	$arParams["CACHE_TIME"] = 0;

//Result
$arResult = Array(
	"BANNER" => "",
	"BANNER_PROPERTIES" => Array(),
);

$obCache = new CPHPCache;
$cache_id = SITE_ID."|advertising.truebanner|".serialize($arParams)."|".$USER->GetGroups();
$cache_path = "/".SITE_ID.$this->GetRelativePath();

if ($obCache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path)){
	if(!CModule::IncludeModule("advertising")) { return; }

	$arBanner = CAdvBanner::GetRandom($arParams["TYPE"]);
	$strReturn = CAdvBanner::GetHTML($arBanner, ($arParams["NOINDEX"] == "Y"));
    
	// Дописываем js-скрипт вставки "картинки" для фиксации показов
	// Вместо CAdvBanner::FixShow($arBanner);
	if (strlen($strReturn)>0) {
		$curdir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
		$strReturn .= '<noindex id="banner_'.$arBanner['ID'].'_img"></noindex>
						<script>
							var place = document.getElementById("banner_'.$arBanner['ID'].'_img");
							var img = document.createElement("img");
								img.src = "'.$curdir.'/fixshow.img.php?BANNER_ID='.$arBanner['ID'].'&rnd='.time().'-'.rand(0,999999).'-"+Math.random();
							    place.appendChild(img);
						</script>
		';
	}
	
	$arResult["BANNER"] = $strReturn;
	$arResult["BANNER_PROPERTIES"] = $arBanner;

	$this->IncludeComponentTemplate();

	$templateCachedData = $this->GetTemplateCachedData();

	$obCache->EndDataCache(
		Array(
			"arResult" => $arResult,
			"templateCachedData" => $templateCachedData
		)
	);
} else {
	$arVars = $obCache->GetVars();
	$arResult = $arVars["arResult"];
	$this->SetTemplateCachedData($arVars["templateCachedData"]);
}

if ($USER->IsAuthorized() && $APPLICATION->GetShowIncludeAreas()){
	if(($arIcons = CAdvBanner::GetEditIcons($arResult["BANNER_PROPERTIES"], $arParams["TYPE"])) !== false)
		$this->AddIncludeAreaIcons($arIcons);
}
?>