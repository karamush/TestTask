<?php
/**
 * Маршрут для выхода и уничтожения сессии
 * User: karamush
 * Date: 023 23.10.19
 * Time: 17:49
 */

if (!Utils::isAuth()) { // если не авторизованы, то перейти на страницу авторизации
//    Utils::addWarning(__('auth_need'));
    Utils::e_redirect('/signin');
}

// только POST нужен, чтоб нельзя было сделать выход, просто перейдя по ссылке на этот маршрут
if (Utils::isPost()) {
    if (Utils::POST('act') == 'logout') {
        session_destroy();
        session_start();

        Utils::addInfo(__('logout_success'));
        Utils::e_redirect('/');
    }
}

render('logout');