<?php
/**
 * Настройки по-умолчанию.
 * Базовые настройки можно определить здесь, а потом переопределять лишь нужные параметры
 * в файлах config_dev.php и config_prod.php
 *
 * User: karamush
 * Date: 014 14.10.19
 * Time: 14:20
 */

return [
    'project_title' => 'app name', // будет отображаться в Title и брэнде
    'db' => [
        'host' => 'localhost',
        'user' => '',
        'pass' => '',
        'db' => '',
        'charset' => 'utf8mb4'
    ],

    // google reRECAPTCHA. Создать ключ тут: https://www.google.com/recaptcha/admin
    'use_recaptcha' => false,
    'recaptcha_site_key' => '',
    'recaptcha_secret_key' => '',

    // настройки блокировки неудачных попыток авторизации
    'use_auth_block' => true,        // использовать ли вообще блокировку неудачных попыток входа
    'auth_check_after'      => 3,    // после стольких неудачных попыток будет показана reCAPTCHA (если включена)
    'auth_block_after'      => 5,    // после стольких неудачных попыток будет заблокирован на указанное далее время
    'auth_block_time'       => 30    // время блокировки входа для текущего IP (в минутах)

];