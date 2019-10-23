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
     * @param $secret_key -- секретный ключ recaptcha
     * @return bool
     */
    static function CheckRecaptcha($user_response, $secret_key) {
        $r = new \ReCaptcha\ReCaptcha($secret_key);
        return $r->verify($user_response, self::GetIP())->isSuccess();
    }

    /* flash-сообщения! :) */

    /**
     * Добавить информационное сообщение
     * @param $text
     * @param string $caption
     */
    static function addInfo($text, $caption = '') {
        $_SESSION['infos'][] = ["text" => $text, "caption" => $caption, 'time' => time()];
    }

    static function addError($text, $caption = '') {
        $_SESSION['errors'][] = ["text" => $text, "caption" => $caption, 'time' => time()];
    }

    static function addWarning($text, $caption = '') {
        $_SESSION['warnings'][] = ['text' => $text, 'caption' => $caption, 'time' => time()];
    }

    static function addSuccess($text, $caption = '') {
        $_SESSION['successes'][] = ['text' => $text, 'caption' => $caption, 'time' => time()];
    }

    /**
     * Проверка наличия сообщений-предупреждений
     * @return int
     */
    static function warningsExists() {
        return count(@$_SESSION['warnings']);
    }

    static function errorsExists() {
        return count(@$_SESSION['errors']);
    }

    static function infosExists() {
        return count(@$_SESSION['infos']);
    }

    static function successesExists() {
        return count(@$_SESSION['successes']);
    }

    /**
     * Получить список сообщений успеха и очистить сразу этот список (по-умолчанию).
     * А можно и не очищать!
     * @param bool $remove_successes
     * @return mixed
     */
    static function getSuccesses($remove_successes = true) {
        $successes = @$_SESSION['successes'];
        if ($remove_successes) {
            $_SESSION['successes'] = [];
        }
        return $successes;
    }

    static function getInfos($remove_infos = true) {
        $infos = @$_SESSION['infos'];
        if ($remove_infos) {
            $_SESSION['infos'] = [];
        }
        return $infos;
    }

    static function getErrors($remove_errors = true) {
        $errors = @$_SESSION['errors'];
        if ($remove_errors)
            $_SESSION['errors'] = [];
        return $errors;
    }

    static function getWarnings($remove_warnings = true) {
        $errors = @$_SESSION['warnings'];
        if ($remove_warnings)
            $_SESSION['warnings'] = [];
        return $errors;
    }

    /* редиректы всякие  */

    /**
     * Перенаправить на указанный URL
     * @param $to_url
     */
    static function redirect($to_url) {
        header('location: ' . $to_url);
    }

    /**
     * перенаправить и завершить работу текущего скрипта
     * @param $to_url
     */
    static function e_redirect($to_url) {
        self::redirect($to_url);
        exit;
    }

    /* авторизационные функции всякие */

    /**
     * Авторизован ли пользователь? (проверяет наличие пользователя в сессии)
     * @return bool
     */
    static function isAuth() {
        return self::getArrayValue($_SESSION, 'user', null) != null;
    }

    /**
     * Получить id текущего залогиненого пользователя
     * @return int|null
     */
    static function getUserID() {
        return self::isAuth() ? self::getArrayValue($_SESSION, 'user')['id'] : null;
    }

}