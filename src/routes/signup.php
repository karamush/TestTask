<?php
/**
 * User: karamush
 * Date: 017 17.10.19
 * Time: 10:38
 */

// попытка регистрации, да?
if (Utils::isPost()) {

    switch (Utils::POST('act')) { // смотрим, что за действие там нужно клиенту?

        case 'check_email': { // проверка существования email
                echo GetDB()->user_email_exists(Utils::POST('email')) ? '1' : '0';
            }
            break;

        case 'register': {
                register();
            }
            break;
    }

    exit(0);
}

// дошли до этого места, значит, это просто GET, значит, вывести страницу
render('sign_up');

/* handlers */

function register() {
    Utils::print_arr($_POST);

//    Utils::CheckRecaptcha(Utils::getRecaptchaResponse(), GetCfgValue('recaptcha_secret_key'));

    $img_dir = __DIR__ . '/../../public/res/avatars/';

    try {
        $si = new \claviska\SimpleImage();
        // приём изображения с помощью SimpleImage, а заодно проверка на валидность изображения!
        $si->fromDataUri(Utils::POST('user_avatar')); // картинку получить из dataURI от canvas-а кропнутого

        // создание директории для аватарок, если её нет
        if (!is_dir($img_dir)) {
            mkdir($img_dir, 0777, true);
        }

        $max_width = (int)GetCfgValue('max_image_width', 1024);
        if (GetCfgValue('auto_resize_image') && $si->getWidth() > $max_width) {
            $si->resize($max_width); // пропорциональное изменение размера изображения до указанной ширины
        }

        // сформировать имя файла -- md5 от текущего времени + расширение файла из MimeType
        $img_file = $img_dir . md5(time()) . '.' . explode('/', $si->getMimeType())[1];
        $si->toFile($img_file); // сохранение изображения в файл
    } catch (Exception $e) {
        // TODO: ERROR!!!!
    }
}