<?
class CIMYIEPPADVPopupAdv
{
	function GetList($aSort=array(), $aFilter=array())
	{
		global $DB;
		
		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			$val = $DB->ForSql($val);
			if(strlen($val)<=0)
				continue;
			switch(strtoupper($key))
			{
				case "ID":
					$arFilter[] = "PB.ID='".$val."'";
					break;
				case "ACTIVE":
					$arFilter[] = "PB.ACTIVE='".($val=="Y"?"Y":"N")."'";
					break;
				case "NAME":
					$arFilter[] = "PB.NAME LIKE '%".$val."%'";
					break;
				case "PRIORITY":
					$arFilter[] = "PB.PRIORITY='".$val."'";
					break;
			}
		}

		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> "ASC"?"DESC":"ASC");
			switch(strtoupper($key))
			{
				case "ID":
					$arOrder[] = "PB.ID ".$ord;
					break;
				case "NAME":
					$arOrder[] = "PB.NAME ".$ord;
					break;
				case "PRIORITY":
					$arOrder[] = "PB.PRIORITY ".$ord;
					break;
			}
		}
		if(count($arOrder) == 0)
			$arOrder[] = "PB.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);

		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);

		$strSql = "
			SELECT
				PB.*
			FROM
				b_imyie_popupadv_banners PB
			".$sFilter.$sOrder;

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function GetByID($ID)
	{
		global $DB;
		
		return CIMYIEPPADVPopupAdv::GetList(array("ID"=>"SORT"),array("ID"=>$ID));
	}
	
	function Delete($ID)
	{
		global $DB;
		
		$ID = intval($ID);

		$DB->StartTransaction();

		$res = $DB->Query("DELETE FROM b_imyie_popupadv_banners WHERE ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if($res)
			$DB->Commit();
		else
			$DB->Rollback();

		return $res;
	}
	
	function Add($arFields)
	{
		global $DB;
		
		if(isset($arFields["ID"]))
			unset($arFields["ID"]);
			
		if(IntVal($arFields["PRIORITY"])>100)
			$arFields["PRIORITY"] = 100;
		if(IntVal($arFields["PRIORITY"])<1)
			$arFields["PRIORITY"] = 1;
		
		$arFields["ACTIVE"] = $arFields["ACTIVE"]=="Y"?"Y":"N";
		
		$ID = $DB->Add("b_imyie_popupadv_banners", $arFields);
		
		CIMYIEPPADVPopupAdv::SetMaxPriority();
		return $ID;
	}
	
	function Update($ID, $arFields)
	{
		global $DB;
		
		$ID = intval($ID);
		
		if(isset($arFields["ID"]))
			unset($arFields["ID"]);
			
		if(IntVal($arFields["PRIORITY"])>100)
			$arFields["PRIORITY"] = 100;
		if(IntVal($arFields["PRIORITY"])<1)
			$arFields["PRIORITY"] = 1;
		
		$arFields["ACTIVE"] = $arFields["ACTIVE"]=="Y"?"Y":"N";

		$strUpdate = $DB->PrepareUpdate("b_imyie_popupadv_banners", $arFields);
		if($strUpdate!="")
		{
			$strSql = "UPDATE b_imyie_popupadv_banners SET ".$strUpdate." WHERE ID=".$ID;
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			CIMYIEPPADVPopupAdv::SetMaxPriority();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	function GetRandomElement()
	{
		global $DB;
		
		$max = COption::GetOptionInt("imyie.popupadv","max_priority");
		$priority = rand(1, $max);
		
		$sFilter = " WHERE PB.ACTIVE LIKE 'Y' AND PB.PRIORITY>".($priority);
		
		$sOrder = " ORDER BY RAND()";
		
		$strSql = "
			SELECT
				PB.*
			FROM
				b_imyie_popupadv_banners PB
			".$sFilter.$sOrder." LIMIT 1";
		
		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function SetMaxPriority()
	{
		$res = CIMYIEPPADVPopupAdv::GetList(array("PRIORITY"=>"DESC"),array("ACTIVE"=>"Y"));
		if($arr = $res->Fetch())
		{
			COption::SetOptionInt("imyie.popupadv","max_priority",$arr["PRIORITY"]);
		}
	}
}
?>