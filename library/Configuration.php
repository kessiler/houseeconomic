<?php
/**
 * Created by JetBrains PhpStorm.
 * User: KESSILER
 * Date: 07/04/13
 * Time: 22:47
 * To change this template use File | Settings | File Templates.
 */

namespace StoredLibrary;


class Configuration
{

    public static function get($ini)
    {
        $ini_array = parse_ini_file($ini, true);
        return $ini_array;
    }
}