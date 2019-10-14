<?php
/**
 * Точка входа в приложение.
 * User: karamush
 * Date: 014 14.10.19
 * Time: 12:38
 */

require __DIR__ . '/../vendor/autoload.php';

// инициализация менеджера настроек
$cfg = new ConfigManager();
$cfg->loadFromDir(__DIR__ . '/../config/', true); // true -- DEV-настройки

// менеджер
$lang = new LangManager(__DIR__ . '/../resources/langs/');
$lang->loadLanguages();
$lang->autoSetCurrentLanguage();

Utils::print_arr($cfg->getAll());