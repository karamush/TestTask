<?php
/**
 * Переопределение настроек для Production-окружения
 * User: karamush
 * Date: 014 14.10.19
 * Time: 14:32
 */

return [
    'project_title' => 'Super Service',
    'db' => [
        'host' => 'localhost',
        'user' => '',
        'pass' => '',
        'db' => '',
        'charset' => 'utf8mb4'
    ],
    'use_recaptcha' => false,
    'recaptcha_site_key' => '',
    'recaptcha_secret_key' => ''
];