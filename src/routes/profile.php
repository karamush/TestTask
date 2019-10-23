<?php
/**
 * User: karamush
 * Date: 023 23.10.19
 * Time: 20:46
 */

if (!Utils::isAuth()) { // если не авторизованы, то перейти на страницу авторизации
    Utils::addWarning(__('auth_need'));
    Utils::e_redirect('/signin');
}

if (Utils::isPost()) {
    switch (Utils::POST('act')) {
        case 'change_avatar': onUserChangeAvatar(); break;
    }

    exit(0);
}

render('profile');


/* handlers! */

function onUserChangeAvatar() {
    try {
        $avatar_url = saveAvatarImageFromDataURI(Utils::POST('avatar_data'));
        echo $avatar_url;
        getDB()->user_avatar_update(Utils::getUserID(), $avatar_url);
    } catch (Exception $e) {
        return; // в случае ошибки ничего не возвращать! но лучше бы сделать нормальную обратную связь...
    }
}