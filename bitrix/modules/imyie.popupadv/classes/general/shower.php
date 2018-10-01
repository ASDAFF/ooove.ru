<?
// RegisterModuleDependences("main", "OnProlog", "imyie.popupadv", "CIMYIEPPADVShower", "HandlerOnProlog");

class CIMYIEPPADVShower
{
	function HandlerOnProlog()
	{
		global $APPLICATION;
		if(strpos($APPLICATION->GetCurDir(), "bitrix/admin/")<1 && CIMYIEPPADVShower::CheckNeedShow())
		{
			$addjquery = COption::GetOptionString("imyie.popupadv", "addjquery", "N");
			if($addjquery=="Y")
			{
				CJSCore::Init(array("jquery"));
			}
			$APPLICATION->AddHeadString( CIMYIEPPADVShower::GetStyles() );
			$APPLICATION->AddHeadString( CIMYIEPPADVShower::GetScripts() );
			$APPLICATION->AddBufferContent(array("CIMYIEPPADVShower", "ScriptAppendToDOM2")); 
			CIMYIEPPADVShower::SetDontShow();
		}
	}
	
	function ScriptAppendToDOM2()
	{
		global $APPLICATION;
		$show_close = COption::GetOptionString("imyie.popupadv", "show_close");
		$data = CIMYIEPPADVShower::GetData();
		$context = '<div class="imyie-popupadv-overlay"></div><div class="imyie-popupadv"><div class="imyie-popupadv-content"><div class="imyie-popupadv-content-inner">'.$data.'</div></div>';
		if($show_close=="Y")
			$context.= '<div class="imyie-popupadv-close"></div>';
		$context.= '</div>';
		return $context;
	}
	
	function GetData()
	{
		$res = CIMYIEPPADVPopupAdv::GetRandomElement();
		if($data = $res->Fetch())
		{
			$data_type = COption::GetOptionInt("imyie.popupadv", "data_type");
			switch($data_type){
				case 1:
					return CIMYIEPPADVShower::_GetDataOne($data);
					break;
				case 2:
					return CIMYIEPPADVShower::_GetDataTwo($data);
					break;
				case 3:
					return CIMYIEPPADVShower::_GetDataThree($data);
					break;
			}
		} else {
			return false;
		}
	}
	
	function _GetDataOne($CONTENT)
	{
		$cont = unserialize($CONTENT["CONTENT"]);
		$return = '';
		if($cont["url"]!="")
			$return.= '<a href="'.$cont["url"].'"';
		if($cont["blank"]=="Y")
			$return.= ' target="blank"';
		if($cont["url"]!="")
			$return.= '>';
		$return.= '<img src="'.$cont["content_banner"].'" border="0" alt="" />';
		if($cont["url"]!="")
			$return.= '</a>';
		
		return $return;
	}
	
	function _GetDataTwo($CONTENT)
	{
		$cont = unserialize($CONTENT["CONTENT"]);
		$return = '';
		$return.= $cont["CONTENT"];
		
		return $return;
	}
	
	function _GetDataThree($CONTENT)
	{
		$cont = unserialize($CONTENT["CONTENT"]);
		$path = $_SERVER['DOCUMENT_ROOT'].$cont["CONTENT"];
		if( file_exists($path) )
		{
			ob_start();
			include($path);
			$html = ob_get_contents();
			ob_end_clean();
			$return = '';
			$return.= $html;
			return $return;
		} else {
			return "";
		}
	}
	
	function SetDontShow()
	{
		$variant = COption::GetOptionString("imyie.popupadv", "sesscook", "session");
		switch ($variant) {
			case "session":
				$needShowKey = CIMYIEPPADVShower::_SetDontShowSession();
			break;
			case "cookie":
				$needShowKey = CIMYIEPPADVShower::_SetDontShowCookie();
			break;
		}
	}
	
	function _SetDontShowSession()
	{
		global $APPLICATION;
		$_SESSION["IMYIE_POPUPADV_DONT_SHOW"] = time();
	}
	
	function _SetDontShowCookie()
	{
		global $APPLICATION;
		$APPLICATION->set_cookie("IMYIE_POPUPADV_DONT_SHOW", time(), time()+60*60*24*31*12);
	}

	function CheckNeedShow()
	{
		$needShowKey = true;
		$variant = COption::GetOptionString("imyie.popupadv", "sesscook", "session");
		switch ($variant) {
			case "session":
				$needShowKey = CIMYIEPPADVShower::_CheckNeedShowBySession();
			break;
			case "cookie":
				$needShowKey = CIMYIEPPADVShower::_CheckNeedShowByCookie();
			break;
			case "none":
				$needShowKey = false;
			break;
		}
		return $needShowKey;
	}
	
	function _CheckNeedShowBySession()
	{
		global $APPLICATION;
		$show = false;
		$timeNow = time();
		$timeinterval = COption::GetOptionString("imyie.popupadv", "timeinterval", "3600");
		$timeFromSess = $_SESSION["IMYIE_POPUPADV_DONT_SHOW"];
		if(($timeFromSess + $timeinterval)<$timeNow)
			$show = true;
		return $show;
	}
	
	function _CheckNeedShowByCookie()
	{
		global $APPLICATION;
		$show = false;
		$timeNow = time();
		$timeinterval = COption::GetOptionString("imyie.popupadv", "timeinterval", "3600");
		$timeFromCook = $APPLICATION->get_cookie("IMYIE_POPUPADV_DONT_SHOW");
		if(($timeFromCook + $timeinterval)<$timeNow)
			$show = true;
		return $show;
	}
	
	function GetScripts()
	{
		$show_close = COption::GetOptionString("imyie.popupadv", "show_close");
		$close_overlay = COption::GetOptionString("imyie.popupadv", "close_overlay");
		$scripts = '<script>
var TimeID = 0;
function IMYIEPopupAdvShow()
{
	$(\'.imyie-popupadv-overlay\').show();
	//$(\'.imyie-popupadv\').show();
	$(\'.imyie-popupadv\').animate({
		opacity:\'toggle\'
	}, 100, "linear", function() {
		// console.log( "all done" );
	});
}
function IMYIEPopupAdvHide()
{
	//$(\'.imyie-popupadv\').hide();
	$(\'.imyie-popupadv\').animate({
		opacity:\'toggle\'
	}, 250, "linear", function() {
		// console.log( "all done" );
		$(\'.imyie-popupadv-overlay\').hide();
	});
}
$(document).ready(function(){
	TimeID = setInterval(function(){
		var pop_window = $(\'.imyie-popupadv\');
		if(pop_window.width()>0)
		{
			pop_window.css(\'left\', ($(window).width()-pop_window.width())/2+ \'px\');
			pop_window.css(\'top\', ($(window).height()-pop_window.height())/2+ \'px\');
			IMYIEPopupAdvShow();
			clearInterval(TimeID);
		}
	},500);';
	if($show_close=="Y")
	{
		$scripts.= '$(\'.imyie-popupadv-close\').bind(\'click\', function(){
			IMYIEPopupAdvHide();
		});';
	}
	if($close_overlay=="Y")
	{
		$scripts.= '$(\'.imyie-popupadv-overlay\').bind(\'click\', function(){
			IMYIEPopupAdvHide();
		});';
	}
$scripts.= '});
</script>';
		return $scripts;
	}
	
	function GetStyles()
	{
		$show_close = COption::GetOptionString("imyie.popupadv", "show_close");
		$close_overlay = COption::GetOptionString("imyie.popupadv", "close_overlay");
		$img_close_path = COption::GetOptionString("imyie.popupadv", "img_close_path");
		$img_close_width = COption::GetOptionInt("imyie.popupadv", "img_close_width");
		$img_close_height = COption::GetOptionInt("imyie.popupadv", "img_close_height");
		$img_overlay_path = COption::GetOptionString("imyie.popupadv", "img_overlay_path");
		$styles = '<style type="text/css">
.imyie-popupadv-overlay{
	display:block;
	width:auto;
	height:auto;
	position:fixed;
	top:0px;
	left:0px;
	right:0px;
	bottom:0px;';
	if($close_overlay=="Y")
		$styles.= 'cursor:pointer;';
	$styles.= 'overflow:auto;
	background:url(\''.$img_overlay_path.'\') 0 0 repeat;
	z-index:1510;
}
.imyie-popupadv{
	display:none;
	width:auto;
	height:auto;
	position:fixed;
	top:400px;
	left:700px;
	z-index:1520;
}';
if($show_close=="Y")
{
$styles.= '.imyie-popupadv-close{
	position:absolute;
	top:-19px;
	right:-19px;
	width:'.$img_close_width.'px;
	height:'.$img_close_height.'px;
	cursor:pointer;
	background:url(\''.$img_close_path.'\') 0 0 no-repeat;
	z-index:1530;
}';
}
$styles.= '</style>';
		return $styles;
	}
}
?>