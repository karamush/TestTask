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

// теперь можно проинициализировать шаблонизатор
require __DIR__ . '/twig.php';
twigInit($cfg->devMode());

/* добавление некоторые глобальных переменных в шаблонизатор, в том числе настройки и строки перевода */
getTwig()->addGlobal('SERVER', $_SERVER);
getTwig()->addGlobal('REMOTE_ADDR', Utils::GetIP());
getTwig()->addGlobal('cfg', $cfg->getAll());
getTwig()->addGlobal('lang', $lang->getMergedLang()); // строки будут доступны так: {{ LANG.str_name }}
getTwig()->addGlobal('langs', $lang->getAvailableLangs()); // список доступных переводов (только названия)
getTwig()->addGlobal('current_lang', $lang->getCurrentLanguageName()); // название текущего перевода
if ($cfg->get('use_recaptcha', false))
    getTwig()->addGlobal('RECAPTCHA_BLOCK', "<div class=\"g-recaptcha\" data-sitekey=\"{$cfg->get('recaptcha_site_key')}\"></div>");