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

$auth_allowed = true;
$need_recaptcha = false;

// если в настройках включена блокировка при неудачных попытках входа...
if (getCfgValue('use_auth_block', true)) {
    $ip_ban_status = getDB()->ip_ban_status(Utils::GetIP());

    // снять блокировку, если прошло достаточное количество времени
    if ($ip_ban_status && (((int)$ip_ban_status['ban_time'] + (int)getCfgValue('auth_block_time', 30) * 60) < time())) {
        getDB()->ip_unban(Utils::GetIP()); // разбаниваем этот IP если прошло достаточно времени!
        $ip_ban_status = null; // Об-null-ить статус блокировки текущего IP
    }

    // если ошибок больше, чем можно, значит запретить и оповестить клиента о запрете входа с этого IP
    if ($ip_ban_status['err_count'] >= getCfgValue('auth_block_after', 6)) {
        $auth_allowed = false; // всё, вход запрещён
        Utils::addWarning(__('signin_blocked')); // оповестим клиента об этом
    }

    // если уже несколько ошибок есть, то включить дополнительную проверку рекапчей (если она включена в настройках)
    if ($ip_ban_status && $auth_allowed && $ip_ban_status['err_count'] >= getCfgValue('auth_check_after', 3)) {
        $need_recaptcha = getCfgValue('use_recaptcha');
    }
}

/**/

/* попытка авторизации, хех! */

if ($auth_allowed && Utils::isPost()) {
    $email = Utils::POST('email');
    $pass = Utils::POST('password');

    if ($user = getDB()->user_get_by_email($email)) { // получить пользователя и сразу проверить его наличие
        $recaptcha_checked = true;
        if ($need_recaptcha && getCfgValue('use_recaptcha')) { // необходима дополнительная проверка рекапчей
            if (!Utils::CheckRecaptcha(Utils::getRecaptchaResponse(), GetCfgValue('recaptcha_secret_key'))) {
                Utils::addWarning(__('auth_recaptcha_error')); // оповестить об ошибке проверки рекапчи
                $recaptcha_checked = false;
            }
        };

        // если здесь, значит, с рекапчей всё хорошо, поэтому можно попробовать авторизоваться
        if ($recaptcha_checked) {
            if (password_verify($pass, $user['password'])) { // пароль успешно проверен, можно авторизовывать!
                unset($user['password']);                       // хеш пароля не нужен в сессии!
                getDB()->ip_unban(Utils::GetIP());              // очистить неудачные попытки входа (если они были, хехе)
                $_SESSION['user'] = $user;                      // сохранить юзера в сессию
                $_SESSION['login_time'] = time();               // сохранить время входа
                Utils::e_redirect('/profile');           // перейти сразу на страницу профиля
            } else {
                Utils::addWarning(__('signin_error')); // неправильный пароль, оповестить о неудачной попытке!
            }
        }
    } else {
        Utils::addWarning(__('signin_error')); // не найден юзер с таким email, но оповестить о неудачной попытке
    }

    // если есть предупреждения, значит, попытка авторизации неудачная.
    // увеличить счётчик ошибок авторизации!
    if (Utils::warningsExists()) {
        getDB()->ip_inc_err(Utils::GetIP(), time());
        // сохранить в сессию email
        $_SESSION['email'] = $email;
        Utils::e_redirect('/signin'); // перенаправить на страницу авторизации, чтоб проверки сработали как надо
    }
}

/* render */

render('sign_in', [
    'email' => Utils::POST('email', Utils::getArrayValue($_SESSION, 'email')),
    'need_recaptcha' => $need_recaptcha,
    'auth_allowed' => $auth_allowed,
]);



