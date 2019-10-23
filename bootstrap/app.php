<?php
/**
 * User: karamush
 * Date: 015 15.10.19
 * Time: 12:52
 */

require __DIR__ . '/../vendor/autoload.php';

// инициализация менеджера настроек
$cfg = new ConfigManager();
$cfg->loadFromDir(__DIR__ . '/../config/', true); // true -- DEV-настройки

// менеджер переводов
$lang = new LangManager(__DIR__ . '/../resources/langs/');
$lang->loadLanguages();
$lang->autoSetCurrentLanguage();

// инициализация базы
$db = new DBManager(GetCfgValue('db'));

// теперь можно проинициализировать шаблонизатор
require __DIR__ . '/twig.php';
twigInit($cfg->devMode());

/* добавление некоторые глобальных переменных в шаблонизатор, в том числе настройки и строки перевода */
$twig = getTwig();
$twig->addGlobal('SERVER', $_SERVER);
$twig->addGlobal('REMOTE_ADDR', Utils::GetIP());
$twig->addGlobal('cfg', $cfg->getAll());
$twig->addGlobal('lang', $lang->getMergedLang()); // строки будут доступны так: {{ LANG.str_name }}
$twig->addGlobal('langs', $lang->getAvailableLangs()); // список доступных переводов (только названия)
$twig->addGlobal('current_lang', $lang->getCurrentLanguageName()); // название текущего перевода
if ($cfg->get('use_recaptcha', false))
    $twig->addGlobal('RECAPTCHA_BLOCK', "<div class=\"g-recaptcha\" data-sitekey=\"{$cfg->get('recaptcha_site_key')}\"></div>");
$twig->addGlobal('GET', $_GET);         // сделать GET-параметры доступными в шаблонах
$twig->addGlobal('POST', $_POST);       // и POST-параметры тоже доступны будут в шаблонах


/* функции-хелперы */

/**
 * @return DBManager
 */
function GetDB() {
    global $db;
    return $db;
}

/**
 * Получить менеджер переводов
 * @return LangManager|string
 */
function GetLangManager() {
    global $lang;
    return $lang;
}

/**
 * Получить менеджер настроек
 * @return ConfigManager
 */
function GetCfgManager() {
    global $cfg;
    return $cfg;
}

/**
 * Получить значение указанного параметра
 * @param $param
 * @param null $default
 * @return mixed|null
 */
function GetCfgValue($param, $default = null) {
    return GetCfgManager()->get($param, $default);
}