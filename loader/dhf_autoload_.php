<?php
/**
 * @author Daniel Js Hdz  <daniel.hernandez.job@gmail.com>
 * This autoloader loads classes based on the namespace PSR-4
 * This autoloader was tested on windows and linux.
 */
class AutoloaderDHF
{
    public static function startsWith($haystack, $needle)
    {       
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {        
        return (substr($haystack, -strlen($needle)) === $needle); #negative index
    }
    /**
     * Function which resolves and includes the real path of a given namespace 
     *
     * @param string $class
     * @return void
     */
    public static function loader($class)
    {
        $vendorDir = dirname(dirname(__FILE__)); //The full path and filename of the file with symlinks resolved. If used inside an include, the name of the included file is returned.
        $baseDir = dirname($vendorDir); #Given a string containing the path of a file or directory, this function will return the parent directory's path that is levels up from the current directory.         
        $array = include "./../autoload_namespaces.php";
        foreach ($array as $namespace => $dir) {
            $notRight='';
            if(DIRECTORY_SEPARATOR=="/"){
                $notRight="\\";
            }else if(DIRECTORY_SEPARATOR=="\\"){
                $notRight="/";
            }else{
                #echo DIRECTORY_SEPARATOR;
                throw new Exception("Error identifiying the separator");
            }

            $dir=str_replace($notRight,DIRECTORY_SEPARATOR,$dir);            
            if(!self::startsWith($dir,DIRECTORY_SEPARATOR)&& !self::endsWith($vendorDir,DIRECTORY_SEPARATOR)){
                $dir = $vendorDir .DIRECTORY_SEPARATOR .$dir;
            }else{
                $dir = $vendorDir . $dir;
            }
            if (strrpos($class, '\\') !== false) {
                if (strrpos($class, $namespace) !== false) { 
                    
                    $resolved = str_replace($namespace, $dir . DIRECTORY_SEPARATOR, $class);
                    $resolved = $resolved . '.php';
                     # since    namespace is given in a format with '\\' , I have to ensure the proper directory format. If I omitted this, the autoloader  only works on windows, not  in linux.           
                    $resolved=  str_replace($notRight,DIRECTORY_SEPARATOR,$resolved);                    
                    require_once $resolved;
                    return;
                }
                if (false) {
                   # $parts = explode("\\", $class);
                }
            }
        }
        #if continues until here then the namespace is not registered, the root namespace must be the root folder of code
        #  echo $class;
        $resolved = $vendorDir . DIRECTORY_SEPARATOR . $classNew = str_replace("\\", DIRECTORY_SEPARATOR, $class) . '.php';
        require $resolved;
    }
}

spl_autoload_register('AutoloaderDHF::loader');
