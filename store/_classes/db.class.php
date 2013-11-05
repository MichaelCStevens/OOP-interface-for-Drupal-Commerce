<?php

class DB {

    private static $objInstance;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    /*
     * Returns DB instance or create initial connection
     * @param
     * @return $objInstance;
     */

    public static function getInstance() {
        if (!self::$objInstance) {
            self::$objInstance = new PDO('mysql:dbname=db_name;localhost', 'username', 'password');
            self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$objInstance;
    }

    /*
     * Passes on any static calls to this class onto the singleton PDO instance
     * @param $chrMethod, $arrArguments
     * @return $mix
     */

    final public static function __callStatic($chrMethod, $arrArguments) {
        $objInstance = self::getInstance();

        return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);
    }

}

?>