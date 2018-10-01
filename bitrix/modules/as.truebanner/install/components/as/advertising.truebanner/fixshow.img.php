<?php
/*
 *	Фиксирует показ баннера $_REQUEST["BANNER_ID"]
 *	и отдаёт в ответ однопиксельный .gif файл
 */
Header("Content-type:  image/gif"); 
Header("Expires: Wed, 11 Nov 1998 11:11:11 GMT"); 
Header("Cache-Control: no-cache"); 
Header("Cache-Control: must-revalidate"); 
printf ("%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c",71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!CModule::IncludeModule("advertising")) { return; }

CAdvBanner::FixShow(array('ID'=>$_REQUEST["BANNER_ID"], 'FIX_SHOW'=>'Y'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");