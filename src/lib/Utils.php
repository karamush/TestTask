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
     * Это GET-запрос?
     * @return bool
     */
    static function isGet() {
        return self::RequestMethod()($_SERVER, 'REQUEST_METHOD') === 'GET';
    }

    /**
     * Это POST-запрос?
     * @return bool
     */
    static function isPost() {
        return self::RequestMethod() === 'POST';
    }

    /**
     * Получить REQUEST_METHOD запроса
     * @return mixed|null
     */
    static function RequestMethod() {
        return self::getArrayValue($_SERVER, 'REQUEST_METHOD');
    }

    /**
     * Получить IP клиента с учётом Cloudflare и всяких прокси (непрозрачных)
     * @return mixed|null
     */
    static function GetIP() {
        // сначала cloudflare, если он есть
        if ($cf_ip = self::getArrayValue($_SERVER, 'HTTP_CF_CONNECTING_IP')) {
            return $cf_ip;
        }

        // теперь клиенты за прокси, а если нет, то просто REMOTE_ADDR
        // WARNING! Щас будет мозголомно, но зато кратко... :)
        return self::getArrayValue($_SERVER, 'HTTP_CLIENT_IP',
                self::getArrayValue($_SERVER, 'HTTP_X_FORWARDED_FOR',
                 self::getArrayValue($_SERVER, 'REMOTE_ADDR')));
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

    /**
     * Получить "g-recaptcha-response" от пользователя
     * @return string
     */
    static function getRecaptchaResponse() {
        return self::POST('g-recaptcha-response');
    }

    /**
     * Проверить рекаптчу
     * @param $user_response -- "g-recaptcha-response", что приходит от пользователя
     * @param $secret_key    -- секретный ключ recaptcha
     * @return \ReCaptcha\Response
     */
    static function CheckRecaptcha($user_response, $secret_key) {
        $r = new \ReCaptcha\ReCaptcha($secret_key);
        return $r->verify($user_response, self::GetIP());
    }

}