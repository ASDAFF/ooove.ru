<?
// ################################################
// Company: NicLab
// Site: https://www.psdtobitrix.ru
// Copyright (c) 2013-2018 NicLab
// ################################################
namespace Ptb\Canonical;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Context;
use Bitrix\Main\Page;
use Bitrix\Main\Web\Uri;

/**
 * Class Handlers
 *
 * @author Nic-lab n.revin
 */
class Handlers
{

    private static $moduleName = 'ptb.canonical';

    public static function setCanonical()
    {
        $moduleName = self::$moduleName;
        
        if (! (defined('ADMIN_SECTION') && ADMIN_SECTION == true) && Loader::includeModule($moduleName)) {
            
            global $APPLICATION, $CACHE_MANAGER;
            
            $isManySite = Option::get($moduleName, 'ptb_canonical_for_sites', 'N') == 'Y';
            $isActive = Option::get($moduleName, 'ptb_canonical_active', 'N', $isManySite ? SITE_ID : '-') == 'Y';
            $b404 = Option::get($moduleName, 'ptb_canonical_404', 'N', $isManySite ? SITE_ID : '-') == 'Y';
            
            if ($b404 && defined("ERROR_404") && ERROR_404 == true) {
                $isActive = false;
            }
            
            if ($isActive) {
                
                $isSetDefault = Option::get($moduleName, 'ptb_canonical_default', 'N', $isManySite ? SITE_ID : '-') == 'Y';
                $isSetQuery = Option::get($moduleName, 'ptb_canonical_query', 'N', $isManySite ? SITE_ID : '-') == 'N';
                $cacheTime = Option::get($moduleName, 'ptb_canonical_cache_time', '86400', $isManySite ? SITE_ID : '-');
                $isUseServerUri = Option::get($moduleName, 'ptb_server_request_uri', 'N', $isManySite ? SITE_ID : '-') == 'Y';
                $isUseGetParams = Option::get($moduleName, 'ptb_get_params', 'N', $isManySite ? SITE_ID : '-') == 'Y';
                
                $curPage = $isUseGetParams ? $APPLICATION->GetCurPageParam("", array(
                    'clear_cache',
                    'clear_cache_session',
                    'back_url_admin',
                    'back_url',
                    'backurl',
                    'login',
                    'logout',
                    'compress'
                )) : $APPLICATION->GetCurPage();
                if ($isUseServerUri) {
                    $curPage = $isUseServerUri ? $_SERVER['REQUEST_URI'] : str_replace("?" . $_SERVER['QUERY_STRING'], "", $_SERVER['REQUEST_URI']);
                }
                
                $obCache = Cache::createInstance();
                $cacheDir = '/' . SITE_ID . '/' . $moduleName . '/list/';
                
                $arFilter = array(
                    'ACTIVE' => 'Y',
                    'USE_REGEXP' => array(
                        false,
                        'N'
                    ),
                    '=SITE_ID' => SITE_ID
                );
                
                if ($isSetQuery) {
                    $arFilter['=PAGE'] = $curPage;
                }
                
                if ($obCache->initCache($cacheTime, md5($moduleName . SITE_ID, serialize($arFilter)), $cacheDir)) {
                    $arItems = $obCache->getVars();
                } elseif ($obCache->startDataCache()) {
                    
                    $CACHE_MANAGER->StartTagCache($cacheDir);
                    
                    $rs = ListTable::getList(array(
                        'filter' => $arFilter,
                        'select' => array(
                            'CANONICAL',
                            'PAGE'
                        )
                    ));
                    
                    $arItems = array();
                    
                    while ($arItem = $rs->fetch()) {
                        $arItems[$arItem['PAGE']] = $arItem['CANONICAL'];
                    }
                    
                    $CACHE_MANAGER->RegisterTag("ptb_canonical");
                    $CACHE_MANAGER->EndTagCache();
                    $obCache->endDataCache($arItems);
                }
                
                $curCanonical = trim($arItems[$curPage]);
                
                if (! $curCanonical) {
                    $curCanonical = self::setCanonicalByRegExp($curPage, $isManySite, $cacheTime);
                }
                
                if ((! $curCanonical || is_null($curCanonical)) && $isSetDefault) {
                    $curCanonical = $curPage;
                }
                
                if ($curCanonical) {
                    self::addHeadString($curCanonical);
                }
            }
        }
    }

    protected static function getDeleteParams()
    {
        $isManySite = Option::get(static::$moduleName, 'ptb_canonical_for_sites', 'N') == 'Y';
        $deleteGet = Option::get(static::$moduleName, 'ptb_get_delete_params', '', $isManySite ? SITE_ID : '-');
        $deleteGet = trim($deleteGet);
        $arDeleteGet = (array) explode("\n", str_replace("\r", "", $deleteGet));
        return $arDeleteGet;
    }

    protected static function addHeadString($curCanonical)
    {
        $host = Context::getCurrent()->getRequest()->getHttpHost();
        $hostWithProtocol = 'http' . (Context::getCurrent()->getRequest()->isHttps() ? 's' : '') . '://' . $host;
        
        if (strpos($curCanonical, 'http://') === false && strpos($curCanonical, 'https://') === false) {
            $curCanonical = $hostWithProtocol . $curCanonical;
        }
        
        $uri = new Uri($curCanonical);
        if ($arDeleteGet = static::getDeleteParams()) {
            $uri->deleteParams($arDeleteGet);
        }
        
        Page\Asset::getInstance()->addString('<link rel="canonical" href="' . $uri->getUri() . '" />', true);
    }

    protected static function setCanonicalByRegExp($curPage, $isManySite, $cacheTime)
    {
        global $CACHE_MANAGER;
        $moduleName = self::$moduleName;
        
        $arFilter = array(
            'ACTIVE' => 'Y',
            'USE_REGEXP' => 'Y',
            '=SITE_ID' => SITE_ID
        );
        
        $obCache = Cache::createInstance();
        $cacheDir = '/' . SITE_ID . '/' . $moduleName . '/listregexp/';
        
        if ($obCache->initCache($cacheTime, md5($moduleName . SITE_ID, serialize($arFilter)), $cacheDir)) {
            $arItems = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $CACHE_MANAGER->StartTagCache($cacheDir);
            
            $rs = ListTable::getList(array(
                'filter' => $arFilter,
                'order' => array(
                    'SORT' => 'ASC',
                    'ID' => 'DESC'
                ),
                'select' => array(
                    'CANONICAL',
                    'PAGE'
                )
            ));
            
            $arItems = array();
            
            while ($arItem = $rs->fetch()) {
                $arItems[$arItem['PAGE']] = $arItem['CANONICAL'];
            }
            
            $CACHE_MANAGER->RegisterTag("ptb_canonical");
            $CACHE_MANAGER->EndTagCache();
            $obCache->endDataCache($arItems);
        }
        
        foreach ($arItems as $regexp => $canonical) {
            $pattern = "@" . $regexp . "@is";
            if (preg_match($pattern, $curPage, $match)) {
                foreach ($match as $i => $val) {
                    if ($i == 0) {
                        continue;
                    }
                    
                    $replace['$' . $i] = $val;
                }
                
                $canonical = str_replace(array_keys($replace), $replace, $canonical);
                
                return $canonical;
            }
        }
        
        return null;
    }
}
?>