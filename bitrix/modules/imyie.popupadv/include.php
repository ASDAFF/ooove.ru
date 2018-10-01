<?
global $DB, $DBType, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"imyie.popupadv",
	array(
		"CIMYIEPPADVUtils" => "classes/general/utils.php",
		"CIMYIEPPADVShower" => "classes/general/shower.php",
		"CIMYIEPPADVPopupAdv" => "classes/".$DBType."/banners.php",
	)
);
?>