<?php

namespace forms\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


abstract class Log
{

    private static $file = '';
    public  static $switch = true;
    protected static $_filename = 'system';
    protected static $_directoryName = 'var/logs';
    public static  $fileExtension = '.log';

    public static function setFileExtension($name){
        $name = str_replace('\.', '', $name);
        self::$fileExtension = '.' . $name;
    }

    public static function setDirectoryName($path){
        $ds = DIRECTORY_SEPARATOR;
        $name = str_replace(array("/","\\"),$ds,$path);
        self::$_directoryName = $name;
    }

    public static function getDirectoryName(){
        $ds = DIRECTORY_SEPARATOR;
        $path = str_replace(array("/","\\"),$ds,self::$_directoryName);
        return $path;
    }

    public static function setFileName($name){
        self::$_filename = $name;
    }


    public static function getFileName()
    {
        $date = self::getTimestamp("Ymd");
        $directoryPath = self::getDirectoryName();

        $filePath = $directoryPath . DIRECTORY_SEPARATOR . self::$_filename . '_' . $date . self::$fileExtension;
        try{
            if (!file_exists($directoryPath)) {
                $oldmask = umask(0);
                mkdir($directoryPath, 0777, true);
                umask($oldmask);
            }

            if (file_exists($filePath)) {
                self::$file = $filePath;
            } else {
                $handle = fopen($filePath, 'w');
                chmod($filePath, 0777);
                fclose($handle);
                self::$file = $filePath;
            }
        } catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
        return self::$file;
    }


    public static function getContent($options = array())
    {
        $fileCountent = file_get_contents(self::getFileName());
        $fileCountent = nl2br($fileCountent);
        return $fileCountent;
    }

    public static function clear($options = array())
    {
        $filename = self::getFileName();
        $f = @fopen($filename, "r+");
        if ($f !== false) {
            ftruncate($f, 0);
            fclose($f);
        }
    }

    public static function trash($directory = '')
    {
        if ($directory === '') {
            $directory = static::getDirectoryName();
        }
            if (is_dir( $directory ) && class_exists('\DirectoryIterator')) {

                $now = strtotime('today UTC');
                try {
                    $files = new \DirectoryIterator( $directory );
                    foreach ($files as $fileInfo) {

                        if (!$fileInfo->isDot()) {
                            $fileTime = filemtime( $fileInfo->getPathname() );
                            if ($fileTime <= $now) {
                                try {
                                    unlink( $fileInfo->getPathname() );
                                } catch (\Exception $e) {
                                    Helper::display($e->getMessage());
                                }
                            }
                        }

                    }
                    return true;
                } catch (\Exception $e ) {
                    Helper::display( $e->getMessage() );
                }

            }

        return false;
    }

    public static function getFiles($path, $string = '', $limitFrom = '', $limitTo = ''){
        $files = array();

        $fileCount = 0;

        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry !== '.' && $entry !== '..') {
                    $files[] = $path . DIRECTORY_SEPARATOR . $entry;
                }
                $fileCount++;
            }
            closedir($handle);
        }

        return $files;
    }

    public static function write($message, $option = FILE_APPEND)
    {
        $file = self::getFileName();
        $date = self::getTimestamp();
        $message = '['.$date.'] ' . print_r($message, true) . PHP_EOL;
        if (self::$switch){
            file_put_contents($file,  $message, $option);
        }
    }

    private static function getTimestamp($format = 'Y-m-d G:i:s.u')
    {
        date_default_timezone_set('UTC');

        list($usec, $sec) = explode(' ', microtime());
        $usec = substr($usec, 2, 6);
        $datetime_now = date('Y-m-d H:i:s\.', $sec).$usec;
        $date = new \DateTime($datetime_now, new \DateTimeZone( 'UTC' ));
        return $date->format($format);
    }

}