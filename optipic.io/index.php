<?php

/**
 * OptiPic.io integration script
 * 
 * @author optipic.io
 * @copyright (c) 2018, https://optipic.io
 */

// fix not-installed timezone
if(!ini_get('date.timezone') || !date_default_timezone_get()) {
    date_default_timezone_set('UTC');
}
 
$errorController = new OptiPicErrorSupervisor();

@set_time_limit(0);
@ini_set('memory_limit', '512M');

// Версия модуля
define("_OPTIPIC_MODULE_VERSION_", "1.26");

class OptiPicIndex {
    
    function __construct() {
        $this->startTime = time();
        
        //$this->skiped = array();
        
        $this->list = array();
        $this->listUnReadable = array();
        $this->status = 'indexing';
        
        $this->rootPath = self::getRootPath(); // путь к директории с изображениями
        
        // Определяем типы файлов, которые нужно индексировать
        $this->extensions = array();
        $extWiteList = array('png', 'jpg', 'jpeg'); // разрешенный список типов файлов
        if(isset($_POST["cfg_filetypes"])) {
            $this->extensions = explode(",", trim($_POST["cfg_filetypes"]));
        }
        if(!$this->extensions || !is_array($this->extensions)) {
            $this->extensions = array();
        }
        $this->extensions = array_intersect($extWiteList, $this->extensions);
        
        $this->currentPath = $this->rootPath;
        
        // Задан ли последний проиндексированный файл?
        $this->byStep = (isset($_POST["by_step"]))? true: false;
        $this->lastProcessed = (isset($_POST["last_processed_file"])) ?trim($_POST["last_processed_file"]): "";
        if($this->lastProcessed)
        {
            $this->alreadySkippedLastProcessedFile = false;
        }
        else
        {
            $this->alreadySkippedLastProcessedFile = true;
        }
        
        // Лимит на 1 шаг индексации
        $this->indexStepLimitSec = (isset($_POST["index_step_sec"]))? intval($_POST["index_step_sec"]): 0;
        if($this->indexStepLimitSec<0 || $this->indexStepLimitSec>100)
        {
            $this->indexStepLimitSec = 5; // лимит по-умолчанию 5
        }
        
        $this->igorelist = $this->getIgnorelist();
        $this->indexOnlyPaths = $this->getIndexOnlyPaths();
        
        $this->breakIndexing = false;
    }
    
    /**
     * Получить инфу - был ли файл ранее уже сжат через OptiPic
     */
    public static function getCompressedInfoByExif($filePathFull) {
        global $optipicCfg;
        
        $data = file_get_contents($filePathFull);
        preg_match_all('/Optimized by OptiPic.io at (?<date>.*) from (?<size_original>.*) to (?<size_compressed>.*) bytes/', $data, $matches);
        unset($data);
        
        if(isset($matches['date'][0]) && isset($matches['size_original'][0]) && isset($matches['size_compressed'][0])
        && $matches['date'][0] && $matches['size_original'][0] && $matches['size_compressed'][0]) {
            return array(
                'compressed' => 1,
                'datetime_compressed' => date('Y-m-d H:i:s', strtotime($matches['date'][0])),
                'size_original' => $matches['size_original'][0],
                'size_compressed' => $matches['size_compressed'][0],
            );
        }
        
        return false;
    }
    
    /**
     * Рекурсивно находим все картинки и генерируем массив с информацией о них
     */
    public function findFiles($dir=null) {
        
        // Если список разрешенных типов файлов пустой - выходим из поиска файлов
        if(!$this->extensions || !is_array($this->extensions) || !count($this->extensions)) {
            return false;
        }
        
        // Если индексация прервана, выходим из рекурсии поиска
        if($this->breakIndexing) {
            //$this->sliped[] = "! ".$dir;
            return false;
        }
    
        if(!$dir) {
            $dir = $this->rootPath;
        }
        elseif($this->isFileInIndexScope($dir)===false) {
            return false;
        }
   
        //$result = array(); 

        // Если нет доступа на чтение папки - пропускаем ее (но запоминаем)
        if(is_readable($dir)===false) {
            $this->listUnReadable[] = $this->getLocalPath($dir).'/';
            return false;
        }
        
        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) {
            $skipThisFile = false;
            
            $filePathFull = $dir . DIRECTORY_SEPARATOR . $value;
            $filePath = $this->getLocalPath($filePathFull);
            
            // проверяем - ведет ли ссылка в тот же document_root
            // такие цикличные ссылки игнорируем - они не попадут в индекс
            $isLoop = @is_link($filePathFull) && strpos(@realpath($filePathFull), $this->rootPath)===0;
            
            if (@file_exists($filePathFull) && !in_array($value,array(".","..")) && !$isLoop) {
                if (is_dir($filePathFull)) {
                
                    ////////////////////////////////////////
                    if($this->lastProcessed) {
                        // проверить, если текущий урл НЕ содержится в последнем файле
                        // при этом текущий урл < последнего обработанного
                        // то пропускаем эту папку
                        $left = strnatcasecmp($filePath, $this->lastProcessed);
                        if(is_dir($filePathFull) && substr_count($this->lastProcessed, '/')>1 && $left==-1 && stripos($this->lastProcessed, $filePath)!==0) {
                            
                            //var_dump("skip");
                            //var_dump($filePath);
                            
                            //echo "last proc: ".$this->lastProcessed."\n";
                            //echo "skip ".$filePath."\n";
                            
                            //$skipThisFile===false;
                            
                            //$this->skiped[] = $filePath;
                            
                            /*if(substr_count($filePath, '/')>1) {
                                return false;
                            }
                            else {
                                $skipThisFile = true;
                            }*/
                        }
                        else {
                            $this->findFiles($filePathFull);
                        }
                        /*else {
                            return true;
                        }*/
                    }
                    else {
                        $this->findFiles($filePathFull);
                    }
                    ////////////////////////////////////////
                    
                    //if($this->findFiles($filePathFull)===false) {
                        //return false;
                        
                    //}
                } 
                else {
                    // Уже дошли до пропущенного файла
                    if ($this->byStep && !$this->alreadySkippedLastProcessedFile && $filePath == $this->lastProcessed) {
                        $this->alreadySkippedLastProcessedFile = true;
                        //echo("skipped: ".$dataForAPI["path"]."\n");
                        //optipicLog("skipped: ".$dataForAPI["path"]);
                        //continue; // его тоже пропускаем
                        $skipThisFile = true;
                    }
                    
                    // Еще не дошли до пропущенного файла
                    if ($this->alreadySkippedLastProcessedFile === false) {
                        //optipicLog("skipped: ".$dataForAPI["path"]);
                        //echo("skipped: ".$dataForAPI["path"]."\n");
                        //continue;
                        $skipThisFile = true;
                    }
                    
                    // Пропускаем файл, если он не должен индексироваться согласно настройкам сайта
                    if ($this->isFileInIndexScope($filePathFull)===false) {
                        //optipicLog("ignore: ".$dataForAPI["path"]);
                        //continue;
                        $skipThisFile = true;
                    }
                    
                    // Если нет доступа на чтение файла - пропускаем его (но запоминаем)
                    if(is_readable($filePathFull)===false) {
                        $this->listUnReadable[] = $filePath;
                        $skipThisFile = true;
                    }
                    
                    if($skipThisFile===false) {
                        //echo "indexing ".$filePath."\n";
                        
                        // Начинаем формировать данные о файле для передачи на индексацию через API
                        $dataForAPI = array(
                            "path" => $filePath,
                            "path_md5" => md5($filePath),
                        );

                        //echo($dataForAPI["path"]."\n");
                        
                        

                        $dataForAPI["size_original"] = filesize($filePathFull);
                        $dataForAPI["mtime"] = date("Y-m-d H:i:s", filemtime($filePathFull));

                        //$dataForAPI["compressed"] = 0;
                        $dataForAPI["size_compressed"] = 0;
                        $dataForAPI["orig_exists"] = 0;
                        
                        //-------------------------------
                        // Проверяем есть ли EXIF-метка о сжатии от OptiPic
                        $hasCompressedHistory = OptiPicIndex::getCompressedInfoByExif($filePathFull);
                        if(is_array($hasCompressedHistory) && $hasCompressedHistory['compressed']) {
                            $dataForAPI["compressed"] = 1;
                            $dataForAPI["size_original"] = $hasCompressedHistory["size_original"];
                            $dataForAPI["size_compressed"] = $hasCompressedHistory["size_compressed"];
                            $dataForAPI["datetime_compressed"] = $hasCompressedHistory["datetime_compressed"];
                        }
                        //-------------------------------
                        
                        elseif (file_exists($filePathFull . ".optipic-orig")) {
                            //$dataForAPI["compressed"] = 1;
                            $dataForAPI["size_compressed"] = $dataForAPI["size_original"]; // считаем что файл уже сжат, если есть orig-файл рядом
                            $dataForAPI["size_original"] = filesize($filePathFull . ".optipic-orig");
                            $dataForAPI["datetime_compressed"] = date("Y-m-d H:i:s", filemtime($filePathFull . ".optipic-orig"));
                            $dataForAPI["orig_exists"] = 1;
                        }
                        
                        //sleep(1);
                        
                        $this->list[] = $dataForAPI;
                    }
                    
                    // завершаем, если пошаговость и лимит на шаг истек
                    $this->stopIfTimelimit($filePath);
                //}
                } 
                $this->stopIfTimelimit($filePath);
            } 
            //$this->stopIfTimelimit($filePath);
        }
        //$this->stopIfTimelimit($filePath);
       
        //return $result; 
    } 
    
    /**
     * Останавливаем поиск файлов и выдаем результат в браузер
     */
    public function stopIfTimelimit($filePath) {
        $timePass = time() - $this->startTime;
        //var_dump($timePass);
        if ($this->byStep && ($timePass > $this->indexStepLimitSec)) {
            //return $path;
            //echo "break\n";
            $this->breakIndexing = true;
            $this->lastFile = $filePath;
            
            echo $this->getResultData();
            exit;
            
            /*$statusIndexing["files"] = $indexingFiles;
            $statusIndexing['last_file'] = $path;
            
            echo optipicGetSerializedGzip($statusIndexing);
            exit;
            break;*/
        }
    }
    
    /**
     * Делаем локальный путь из полного пути - вырезаем root_path
     */
    public function getLocalPath($path) {
        if(strpos($path, $this->rootPath)===0) {
            $path = str_replace($this->rootPath, "", $path);
        }
        
        return $path;
    }
    
    /**
     * Делаем полный путь из локального пути - добавляем root_path
     */
    public function getFullPath($localPath) {
        if(strpos($localPath, $this->rootPath)===0) {
            return $localPath;
        }
        
        return $this->rootPath.$localPath;
    }
    
    /**
     * Получить массив игнорируемых путей
     */
    public function getIgnorelist() {
        
        // если есть закешированное значение, отдаем его
        if(isset($this->igorelist)) return $this->igorelist;
        
        // Если нет закешированного, собираем игнорлист
        $ignorePaths = (isset($_POST["cfg_index_ignorelist"]))? (string) $_POST["cfg_index_ignorelist"]: "";
        $ignorePaths = ($ignorePaths)? explode("\n", $ignorePaths): array();
        
        if($ignorePaths){
            foreach($ignorePaths as $iK=>$iV) {
                $ignorePaths[$iK] = trim($iV);
            }
        }
        
        // кешируем
        $this->igorelist = $ignorePaths;
        
        return $ignorePaths;
    }
    
    
    /**
     * Возвращает массив путей "индексировать только"
     */
    function getIndexOnlyPaths() {
        
        // если есть закешированное значение, отдаем его
        if(isset($this->indexOnlyPaths)) return $this->indexOnlyPaths;
        
        // Если нет закешированного, собираем значение
        $strIndexOnlyPaths = (isset($_POST["cfg_index_whitelist"]))? (string) $_POST["cfg_index_whitelist"]: "";
        $indexOnlyPaths = ($strIndexOnlyPaths)? explode("\n", $strIndexOnlyPaths): array();
        if($indexOnlyPaths){
            foreach($indexOnlyPaths as $iK=>$iV) {
                $indexOnlyPaths[$iK] = trim($iV);
            }
        }
        
        // кешируем
        $this->indexOnlyPaths = $indexOnlyPaths;
        
        return $this->indexOnlyPaths;
    }

    
    /*public function isDirInIndexScope($path) {
        
    }*/
    
    /**
     * Попадает ли файл в индекс модуля?
     * Проверяется попадание файла в игнорлист и в "индексируем только"
     */
    public function isFileInIndexScope($path) {
        
        // Если нет доступа на чтение папки - пропускаем ее (но запоминаем)
        if(@file_exists($path)===false) {
            $this->listUnReadable[] = $this->getLocalPath($path);
            return false;
        }
        
        // Сначала провеяем расширение файла
        $fileExt = pathinfo($path, PATHINFO_EXTENSION);
        $fileExt = strtolower($fileExt); // добавляем регистронезависимость расширений файла (JPG --> jpg и т.п.)
        if(is_dir($path)==false && in_array($fileExt, $this->extensions)===false) {
            return false;
        }
        
        // Делаем локальный путь из полного пути - вырезаем DOCUMENT_ROOT
        $path = $this->getLocalPath($path);
        
        $ignorePaths = $this->getIgnorelist();
        $indexOnlyPaths = $this->getIndexOnlyPaths();
        
        // пропускаем папки, которые в игноре
        $ignoreThisPath = false;
        foreach($ignorePaths as $ignorePath) {
            if($ignorePath && strpos($path, $ignorePath)===0) {
                $ignoreThisPath = true;
            }
        }
        if($ignoreThisPath) {
            return false;
        }

        if($indexOnlyPaths){
            $indexThisPath = false;

            foreach($indexOnlyPaths as $indexPath) {
                /*
                Нужно проверять оба strpos()! - или/или

                Примеры:
                whitelist - /upload/
                path - /upload/bla/
                тут нужно проверить на strpos(path, whitelist)===0
                иначе внутрь /upload/bla/ не войдем

                whitelist - /upload/bla/
                path - /upload/
                тут нужно проверить на strpos(whitelist, path)===0
                иначе внутрь /upload/ не войдем
                */
                if($indexPath && (
                    strpos($path, $indexPath)===0 
                    || strpos($indexPath, $path)===0)
                ) {
                    $indexThisPath = true;
                }
            }

            if(!$indexThisPath) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Возвращает сериализованный массив с проиндексированными изобрежениями
     */
    public function getResultData() {
        $data = array();
        
        if($this->breakIndexing) {
            $data["status"] = "indexing";
            $data["files"] = $this->list;
            $data['last_file'] = $this->lastFile;
        }
        else {
            //"status" => "indexing done", 
            $data["status"] = "indexing done";
            $data["files"] = $this->list;
        }
        
        $data["list_unreadable"] = $this->listUnReadable;
        
        //$data['skiped'] = $this->skiped;
        
        $serializedData = serialize($data);
        //$serializedData = $data;
        
        return $serializedData;
    }
    
    /**
     * Возвращает корневую директорию, в которой находится сайт
     */
    public static function getRootPath() {
        global $optipicCfg;
        
        require_once './config.php';
        
        return (isset($optipicCfg["root_path"]))? $optipicCfg["root_path"]: $_SERVER['DOCUMENT_ROOT'];
    }
    
    /**
     * Безопасное удаление файла
     */
    public static function unlink($filepath) {
        if(file_exists($filepath) && is_writable($filepath)) {
            @unlink($filepath);
        }
    }
    
    
    public static function createTmpDir($prefix='') {
        $tempfile=tempnam(sys_get_temp_dir(), $prefix);
        // you might want to reconsider this line when using this snippet.
        // it "could" clash with an existing directory and this line will
        // try to delete the existing one. Handle with caution.
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        if (is_dir($tempfile)) { return $tempfile; }
        
        return false;
    }
    
    
    public static function getCRC($content) {
        return hash("crc32b", $content);
    }
}


class OptiPicErrorSupervisor
{
	public function __construct()
	{
		// регистрация ошибок
		set_error_handler(array($this, 'OtherErrorCatcher'));
		
		// перехват критических ошибок
		register_shutdown_function(array($this, 'FatalErrorCatcher'));
		
		// создание буфера вывода
		ob_start();
	}
	
	public function OtherErrorCatcher($errno, $errstr, $errfile, $errline) {
        
        // не обрабатываем ошибку, когда функции вызываются с игнором ошибок через @do_smth()
        if(!error_reporting()) {
            return true;
        }
        
        echo optipicGetSerializedGzip(array(
            "error" => "error_php",
            "error_text" => "[".$this->FriendlyErrorType($errno)."] ".$errstr." in file $errfile on line $errline",
        ));
        exit;
        
		// контроль ошибок:
		// - записать в лог
		return false;
	}
	
	public function FatalErrorCatcher() {
		$error = error_get_last();
		if (isset($error))
			if($error['type'] == E_ERROR
				|| $error['type'] == E_PARSE
				|| $error['type'] == E_COMPILE_ERROR
				|| $error['type'] == E_CORE_ERROR)
			{
				ob_end_clean();	// сбросить буфер, завершить работу буфера
			
                $this->OtherErrorCatcher($error['type'], $error['message'], $error['file'], $error['line']);
            
				// контроль критических ошибок:
				// - записать в лог
				// - вернуть заголовок 500
				// - вернуть после заголовка данные для пользователя
			}
			else {
				ob_end_flush();	// вывод буфера, завершить работу буфера
			}
		else {
			ob_end_flush();	// вывод буфера, завершить работу буфера
		}
	}
    
    public function FriendlyErrorType($type) { 
        switch($type) { 
            case E_ERROR: // 1 // 
                return 'E_ERROR'; 
            case E_WARNING: // 2 // 
                return 'E_WARNING'; 
            case E_PARSE: // 4 // 
                return 'E_PARSE'; 
            case E_NOTICE: // 8 // 
                return 'E_NOTICE'; 
            case E_CORE_ERROR: // 16 // 
                return 'E_CORE_ERROR'; 
            case E_CORE_WARNING: // 32 // 
                return 'E_CORE_WARNING'; 
            case E_COMPILE_ERROR: // 64 // 
                return 'E_COMPILE_ERROR'; 
            case E_COMPILE_WARNING: // 128 // 
                return 'E_COMPILE_WARNING'; 
            case E_USER_ERROR: // 256 // 
                return 'E_USER_ERROR'; 
            case E_USER_WARNING: // 512 // 
                return 'E_USER_WARNING'; 
            case E_USER_NOTICE: // 1024 // 
                return 'E_USER_NOTICE'; 
            case E_STRICT: // 2048 // 
                return 'E_STRICT'; 
            case E_RECOVERABLE_ERROR: // 4096 // 
                return 'E_RECOVERABLE_ERROR'; 
            case E_DEPRECATED: // 8192 // 
                return 'E_DEPRECATED'; 
            case E_USER_DEPRECATED: // 16384 // 
                return 'E_USER_DEPRECATED'; 
        } 
        return "UNKNOWN_ERROR_TYPE"; 
    } 
}


function optipicLog($data)
{ 
    file_put_contents("./log.txt", "[".date("d.m.Y H:i:s")."] ".var_export($data, true)."\n", FILE_APPEND);
}

function optipicGetSerializedGzip($data)
{
    $serializedData = serialize($data);
    
    return $serializedData;
}

// Подключаем конфиг
require_once './config.php';

if(isset($_GET["cmd"]) && $_GET["cmd"]=="getversion")
{
    $_optipicVersion = "";
    if(defined("_OPTIPIC_MODULE_VERSION_"))
    {
        $_optipicVersion = _OPTIPIC_MODULE_VERSION_;
    }
    echo optipicGetSerializedGzip(array(
        "version" => $_optipicVersion,
    ));
    exit;
}

// Проверяем корректность конфига
if(!isset($optipicCfg["api_url"]) || !$optipicCfg["api_url"] || !isset($optipicCfg["secretkey"]) || !$optipicCfg["secretkey"])
{
    require_once './help.php';
    exit;
}

// Если переданный секретный ключ не совпадает - выдаем ошибку
if(isset($optipicCfg["secretkey"]) && isset($_REQUEST["secretkey"]) && $optipicCfg["secretkey"] && $_REQUEST["secretkey"]!==$optipicCfg["secretkey"])
{
    echo optipicGetSerializedGzip(array(
        "error" => "wrong_api_key",
    ));
    exit;
}

// Если секретный ключ не передан или не задан - показываем только help
if(!isset($optipicCfg["secretkey"]) || !isset($_REQUEST["secretkey"]) || !$optipicCfg["secretkey"] || !isset($_GET["cmd"]))
{
    require_once './help.php';
    exit;
}

// ИНДЕКСАЦИЯ НАЧАЛО 
// ----------------------------------------
if($_REQUEST["cmd"]=="doreindex")
{
    $indexator = new OptiPicIndex();
    $indexator->findFiles();
    echo $indexator->getResultData();
}
// ИНДЕКСАЦИЯ КОНЕЦ 
// ----------------------------------------

// СЖАТИЕ НАЧАЛО 
// ----------------------------------------
if($_REQUEST["cmd"]=="docompress")
{
    // Если CURL не установлен - возвращаем соответствующую ошибку
    if(!function_exists('curl_init') || !is_callable('curl_init'))
    {
        echo optipicGetSerializedGzip(array(
            "error" => "curl", 
        ));
        exit;
    }
    
    $listFiles = $_POST["files"];
    
    $postParams = array();
    
    $compressedFiles = array();
    
    if(isset($_POST["quality"]))
    {
        $quality = (int) $_POST["quality"];
        $postParams["quality"] = $quality;
    }
    if(isset($_POST["maxwidth"]) && $_POST["maxwidth"])
    {
        $maxwidth = (int) $_POST["maxwidth"];
        $postParams["maxwidth"] = $maxwidth;
    }
    if(isset($_POST["maxheight"]) && $_POST["maxheight"])
    {
        $maxheight = (int) $_POST["maxheight"];
        $postParams["maxheight"] = $maxheight;
    }
    $save_originals = false;
    if(isset($_POST["save_originals"]) && $_POST["save_originals"])
    {
        $save_originals = true;
    }
    
    $postParams["secretkey"] = $optipicCfg['secretkey'];
    
    $rootPath = OptiPicIndex::getRootPath();
    
    foreach($listFiles as $file)
    {
        $fullpath = $rootPath.$file;
        
        // Файла не существует - говорим об этом сервису (чтобы он удалил файл из индекса)
        if(!file_exists($fullpath))
        {
            $compressedFiles[] = array(
                "path" => $file,
                "compressed" => 0,
                "not_found" => 1,
            );
            continue;
        }
        
        // Если директория файла или сам файл недоступен для записи - выдаем ошибку записи
        if(!is_writable(dirname($fullpath)) || !is_writable($fullpath))
        {
            $compressedFiles[] = array(
                "path" => $file,
                "compressed" => 0,
                "write_error" => 1,
            );
            continue;
        }
        
        // Прикрепляем файл
        if (function_exists('curl_file_create')) { // php 5.5+
            $cFile = curl_file_create($fullpath);
        } else { // 
            $cFile = '@' . realpath($fullpath);
        }
        $postParams["file"] = $cFile; 
        
        // Указываем внутренний путь к файлу от корня root_path (это нужно для сохранения оригиналов в облеке optipic.io)
        $postParams['filepath'] = $file;
        
        //$imgData = file_get_contents($fullpath);
        //var_dump($optipicCfg["api_url"]."compress?".http_build_query($getParams));        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $optipicCfg["api_url"]."compress");
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, $optipicCfg["api_login"] . ":" . $optipicCfg["api_pass"]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!ini_get("open_basedir"))
        {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        @curl_setopt($ch, CURLOPT_POSTREDIR, 3); // чтобы POST-данные передавались и при редиректе
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // таймаут - 60 секунд

        $optiImgData = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        if($info["http_code"]==200)
        {
            //var_dump($_SERVER['DOCUMENT_ROOT'].$file);
            $copiedOrig = null;
            // Оригинал еще не сохранен
            if($save_originals && !file_exists($fullpath.".optipic-orig"))
            {
                $copiedOrig = @copy($fullpath, $fullpath.".optipic-orig");
                @chmod($fullpath.".optipic-orig", fileperms($fullpath)); // ставим chmod у ориг-а такой же как у исходного
                @chown($fullpath.".optipic-orig", fileowner($fullpath)); // ставим владельца таким же, как у исходного файла
                @chgrp($fullpath.".optipic-orig", filegroup($fullpath)); // ставим группу такой же, как у исходного файла
            }
            
            // Не удалось сохранить оригинал - ничего больше с этим файлом не делаем
            if($copiedOrig===false)
            {
                $compressedFiles[] = array(
                    "path" => $file,
                    "compressed" => 0,
                    "write_error" => 1,
                );
                continue;
            }
            
            // сохраняем результат сжатия во временный файл
            $tmpCompressedFile = $fullpath.".tmp"; // tempnam(sys_get_temp_dir(), 'optipic_');
            file_put_contents($tmpCompressedFile, $optiImgData);
            
            // сравниваем исходный размер картинки со сжатым
            $compressedSize = filesize($tmpCompressedFile); // сжатый размер
            $origSize = filesize($fullpath); // исходный размер
            
            // сжатая картинка меньше, чем исходная - сохраняем сжатую версию
            if($compressedSize>0 && $compressedSize<$origSize)
            {
                $perms = fileperms($fullpath); // запоминаем исходный chmod
                $saved = file_put_contents($fullpath, $optiImgData);
                @chmod($fullpath, $perms); // ставим chmod у сжатого такой же как у исходного
                @chown($fullpath, fileowner($fullpath)); // ставим владельца таким же, как у исходного файла
                @chgrp($fullpath, filegroup($fullpath)); // ставим группу такой же, как у исходного файла
            }
            // исходная картинка меньше сжатой - оставляем исходную
            else
            {
                OptiPicIndex::unlink($tmpCompressedFile); // удаляем временный файл
                
                $compressedFiles[] = array(
                    "path" => $file,
                    "compressed" => 1,
                    "size" => $origSize,
                    "mtime" => filemtime($fullpath),
                );
                continue;
            }
            
            OptiPicIndex::unlink($tmpCompressedFile); // удаляем временный файл
            
            // успешно сохранен сжатый файл
            if($saved!==false)
            {
                // чистим кеш информации по файлу - иначе php выдает старый mtime и size
                if(version_compare(PHP_VERSION, '5.3.0') >= 0) {
                    clearstatcache(true, $fullpath); // передавать параметры можно лишь начиная с 5.3.0
                }
                else {
                    clearstatcache();
                }
                
                $newsize = filesize($fullpath);
                $newmtime = filemtime($fullpath);
                
                $compressedFiles[] = array(
                    "path" => $file,
                    "compressed" => 1,
                    "size" => $newsize,
                    "mtime" => $newmtime,
                );
            }
            // сжатый файл не удалось сохранить - фиксируем ошибку записи файла
            else
            {
                $compressedFiles[] = array(
                    "path" => $file,
                    "compressed" => 0,
                    "write_error" => 1,
                );
                
            }
        }
        else {
            //optipicLog($info);
            //optipicLog(curl_error($ch));
            
            $compressedFiles[] = array(
                "path" => $file,
                "compressed" => 0,
                "write_error" => 1,
            );
            continue;
        }
        
        OptiPicIndex::unlink($tmpCompressedFile); // удаляем временный файл
    }
    
    echo optipicGetSerializedGzip(array(
        "status" => "compressing done", 
        "files" => $compressedFiles
    ));
}
// СЖАТИЕ КОНЕЦ 
// ----------------------------------------



// ВОЗВРАТ ОРИГИНАЛОВ НАЧАЛО 
// ----------------------------------------
if($_REQUEST["cmd"]=="doreturnorigs")
 {
    $start = time();
    
    $indexer = new OptiPicIndex();

    $directoryIterator = new RecursiveDirectoryIterator(OptiPicIndex::getRootPath(), RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
    $iteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::LEAVES_ONLY);

    $extensions = array('optipic-orig'); // показывать расширения
    
    $timeLimit = (isset($_REQUEST['timelimit']))? intval($_REQUEST['timelimit']): 5;

    foreach ($iteratorIterator as $file)
    {
        $fileExt = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if (in_array($fileExt, $extensions))
        {
            //$i++;
            $newPath = str_replace('.optipic-orig', '', $file->getPathname());
            $renamed = rename($file->getPathname(), $newPath);
            //sleep(1);

            if ($renamed)
            {
                $path = str_replace(OptiPicIndex::getRootPath(), "", $newPath);
                
                // чистим кеш информации по файлу - иначе php выдает старый mtime и size
                if(version_compare(PHP_VERSION, '5.3.0') >= 0) {
                    clearstatcache(true, $newPath); // передавать параметры можно лишь начиная с 5.3.0
                }
                else {
                    clearstatcache();
                }

                //$pathMD5 = $DB->ForSql(md5($path));
                $newSize = filesize($newPath);
                $newMtime = filemtime($newPath);
            }

            //$timePass = time() - $start;
            // $timePass>1
            //if ($byStep && $i > 10) {
            //    return str_replace($_SERVER["DOCUMENT_ROOT"], "", $file->getPathname());
            //}
        }
        
        if((time() - $start) >= $timeLimit) {
            echo optipicGetSerializedGzip(array(
                "status" => "returning",
                "lastfile" => $indexer->getLocalPath($file->getPathname())
            ));
            exit;
        }
    }
    
    echo optipicGetSerializedGzip(array(
        "status" => "done", 
    ));
}
// ВОЗВРАТ ОРИГИНАЛОВ КОНЕЦ 
// ----------------------------------------


// УДАЛЕНИЕ ОРИГИНАЛОВ НАЧАЛО 
// ----------------------------------------
if($_REQUEST["cmd"]=="dodeleteorigs")
 {
    $indexer = new OptiPicIndex();
     
    $start = time();

    $directoryIterator = new RecursiveDirectoryIterator(OptiPicIndex::getRootPath(), RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
    $iteratorIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::LEAVES_ONLY);
    
    $timeLimit = (isset($_REQUEST['timelimit']))? intval($_REQUEST['timelimit']): 5;

    $extensions = array('optipic-orig'); // показывать расширения

    foreach ($iteratorIterator as $file)
    {
        $fileExt = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if (in_array($fileExt, $extensions))
        {
            //$i++;
            OptiPicIndex::unlink($file->getPathname());
            //sleep(1);
        }
        
        if((time() - $start) >= $timeLimit) {
            echo optipicGetSerializedGzip(array(
                "status" => "deleting",
                "lastfile" => $indexer->getLocalPath($file->getPathname())
            ));
            exit;
        }
    }
    
    echo optipicGetSerializedGzip(array(
        "status" => "done", 
    ));
}
// УДАЛЕНИЕ ОРИГИНАЛОВ КОНЕЦ 
// ----------------------------------------



// UPDATER (start)
// ----------------------------------------
if($_REQUEST["cmd"]=="update") {
    // If CURL does not installed - return the error
    if(!function_exists('curl_init') || !is_callable('curl_init'))
    {
        echo optipicGetSerializedGzip(array(
            "error" => "curl", 
        ));
        exit;
    }
    
    // list of updated files
    $updated = array();
    // list of errors
    $updateErrors = array();
    
    // get last stable version via API
    $ch = curl_init($optipicCfg["api_url"].'update/get-last-version');
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    $lastVersion = curl_exec($ch);
    $lastVersion = json_decode($lastVersion, true);
    $lastVersion = $lastVersion['version'];
    curl_close ($ch);
    
    $files = array(
        'help.php',
        'index.php',
    );
    
    // list of file`s CRC
    $filesCRC = array();
    
    // If the current version less than last stable (via API) - do update
    if(version_compare(_OPTIPIC_MODULE_VERSION_, $lastVersion, '<')) {
        
        $tmpDir = OptiPicIndex::createTmpDir('optipic_update_');
        if(!$tmpDir) { 
            echo optipicGetSerializedGzip(array(
                'status' => 'error',
                'text' => 'Cant create TMP dir',
            ));
            exit;
        }
        
        foreach($files as $file) {
            $done = false;
            
            $tmpFilePath = $tmpDir.'/'.$file;
            
            $getParams = array(
                'filename' => $file,
            );
            
            $ch = curl_init($optipicCfg["api_url"].'update/get-file-content?'.http_build_query($getParams));
            curl_setopt($ch, CURLOPT_HEADER, 0);  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            $content = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
        
            $downloadErrors = false;
        
            if($info["http_code"]==200 && $content) {
                $updatingFilePath = dirname(__FILE__).'/'.$file;
                if(file_exists($updatingFilePath)) {
                    
                    // Get CRC from API
                    $ch = curl_init($optipicCfg["api_url"].'update/get-file-crc?'.http_build_query($getParams));
                    curl_setopt($ch, CURLOPT_HEADER, 0);  
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
                    $crc32b = curl_exec($ch);
                    curl_close($ch);
                    
                    // save last version of file to tmp dir
                    @file_put_contents($tmpFilePath, $content);
                    // copy current (prev) version of file to tmp dir
                    $copy = @copy($updatingFilePath, $tmpFilePath.'.bak');
                    if(!$copy) {
                        $downloadErrors = true;
                        $updateErrors[] = "Error copy bak-file to tmp $tmpFilePath.bak";
                        break;
                    }
                    
                    if(!$content) {
                        $downloadErrors = true;
                        $updateErrors[] = "Error save new version to tmp $tmpFilePath";
                        break;
                    }
                    
                    if($crc32b != OptiPicIndex::getCRC(file_get_contents($tmpFilePath))) {
                        $downloadErrors = true;
                        $updateErrors[] = "Error compare CRC for $file";
                        break;
                    }
                    
                    $filesCRC[$file] = $crc32b;
                    
                }
            }
        }
        
        // If have no errors - do update
        if($downloadErrors==false) {
            foreach($files as $file) {
                $updatingFilePath = dirname(__FILE__).'/'.$file;
                $tmpFilePath = $tmpDir.'/'.$file;
                
                $crcOld = OptiPicIndex::getCRC(file_get_contents($updatingFilePath));
                $crcNew = OptiPicIndex::getCRC(file_get_contents($tmpFilePath));
                
                // update - if file changed
                if($crcOld!=$crcNew && is_writable($updatingFilePath)) {
                    // illusion update
                    if(isset($_REQUEST['only_check'])) {
                        $updated[] = $file;
                    }
                    // real update
                    else {
                        $done = @rename($tmpFilePath, $updatingFilePath);
                        if($done) {
                            $updated[] = $file;
                        }
                    }
                    
                    // @todo permission error handler
                }
            }
        }
        // If have errors - does not update
        //else {
        //}
        
        // Remove all tmp files and dir
        foreach($files as $file) {
            $tmpFilePath = $tmpDir.'/'.$file;
            OptiPicIndex::unlink($tmpFilePath);
            OptiPicIndex::unlink($tmpFilePath.'.bak');
        }
        @rmdir($tmpDir);
        //var_dump($updated);
        
        
    }
    else {
        echo optipicGetSerializedGzip(array(
            'status' => 'nothing_to_update',
        ));
        exit;
    }
    
    if(!count($updateErrors)) {
        echo optipicGetSerializedGzip(array(
            'status' => 'success',
            'updated' => $updated,
        ));
    }
    else {
        echo optipicGetSerializedGzip(array(
            //'status' => 'nothing_to_update',
            'status' => 'error',
            'errors' => $updateErrors,
        ));
    }
    
    
}
// UPDATER (end)
// ----------------------------------------

?>