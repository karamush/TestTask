<?php
/**
 * Класс содержит вспомогательные методы для работы приложения.
 * User: karamush
 * Date: 014 14.10.19
 * Time: 14:04
 */

class Utils {

    /**
     * Получить значение указанного GET-параметра или $default_value в случае отсутствия GET-параметра
     * @param $param_name
     * @param string $default_value
     * @return string
     */
    static function GET($param_name, $default_value = "") {
        return self::getArrayValue($_GET, $param_name, $default_value);
    }

    /**
     * Получить значение указанного POST-параметра или $default_value
     * @param $param_name
     * @param string $default_value
     * @return string
     */
    static function POST($param_name, $default_value = "") {
        return self::getArrayValue($_POST, $param_name, $default_value);
    }

    /**
     * Pretty print array
     * @param $array
     */
    static function print_arr($array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /**
     * Безопасно получить значение ключа массива (с проверкой на сущестование этого ключа),
     * иначе вернуть $default_value
     * @param array $arr
     * @param $key
     * @param null $default_value
     * @return mixed|null
     */
    static function getArrayValue(array $arr, $key, $default_value = null) {
        return isset($arr[$key]) ? $arr[$key] : $default_value;
    }

}