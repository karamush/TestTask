<?php
/**
 * User: karamush
 * Date: 017 17.10.19
 * Time: 10:38
 */

if (Utils::isAuth()) { // уже авторизованы, перейти в профиль и написать гневное сообщение
    Utils::addWarning(__('signup_already_logged_in'));
    Utils::e_redirect('/profile');
}

// попытка регистрации, да?
if (Utils::isPost()) {

    switch (Utils::POST('act')) { // смотрим, что за действие там нужно клиенту?

        case 'check_email': { // проверка существования email
                echo getDB()->user_email_exists(Utils::POST('email')) ? '1' : '0';
            } break;

        case 'register': register(); break;
    }

    exit(0);
}

// дошли до этого места, значит, это просто GET, значит, вывести страницу
render('sign_up');


/* handlers */

function register() {
    $name = Utils::POST('name');
    $surname = Utils::POST('surname');
    $password = Utils::POST('password');
    $password_confirm = Utils::POST('password_confirm');
    $email = Utils::POST('email');
    $user_avatar = Utils::POST('user_avatar');

    if (mb_strlen($name) < 2) {
        Utils::addWarning(__('auth_firstname_dataerror'));
    }

    if (mb_strlen($surname) < 2) {
        Utils::addWarning(__('auth_lastname_dataerror'));
    }

    if (mb_strlen($password) < 8) {
        Utils::addWarning(__('auth_password_dataerror'));
    }

    if ($password != $password_confirm) {
        Utils::addWarning(__('auth_password_confirm_dataerror'));
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Utils::addWarning(__('auth_email_dataerror'));
    }

    if (getDB()->user_email_exists($email)) {
        Utils::addWarning(__('auth_email_exists'));
    }

    if (empty($user_avatar)) {
        Utils::addWarning(__('auth_user_avatar_error'));
    }

    if (getCfgValue('use_recaptcha')) {
        if (!Utils::CheckRecaptcha(Utils::getRecaptchaResponse(), GetCfgValue('recaptcha_secret_key'))) {
            Utils::addWarning(__('auth_recaptcha_error'));
        }
    }

    // если дошли до этого места с ошибками, то пользователю нужно снова ввести данные свои или исправить что-то!
    if (Utils::warningsExists()) {
        // отрендерить шаблон, а все ошибки автоматически там будут показаны.
        render('sign_up');
        return;
    }

    // Раз дошли до сюды, то теперь непосредственно сохранение изображения и сохранение пользователя в базу!
    try {
        $avatar_url = saveAvatarImageFromDataURI($user_avatar);
    } catch (Exception $e) {
        Utils::addWarning(sprintf(__('save_image_error'), $e->getMessage()));
        render('sign_up');
        return;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    /* Регистрация в базу! Ура! */
    if (getDB()->user_add($email, $password, $name, $surname, $avatar_url, time(), Utils::GetIP())) {
        Utils::addInfo(__('register_success'));
        Utils::e_redirect('/signin');
    } else {
        Utils::addWarning(__('register_error'));
        render('sign_up');
    }
}