<?php
/**
 * Не очень хотелось создавать данный файл, но пришлось...
 * User: karamush
 * Date: 024 24.10.19
 * Time: 00:15
 */

/* функции-хелперы */

/**
 * @return DBManager
 */
function getDB() {
    global $db;
    return $db;
}

/**
 * Получить менеджер переводов
 * @return LangManager|string
 */
function getLangManager() {
    global $lang;
    return $lang;
}

/**
 * Получить значение строки, взятое из текущего перевода
 * @param $str_name
 * @return null
 */
function getLangStr($str_name) {
    return getLangManager()->getString($str_name);
}

/**
 * Alias for GetLangStr()
 * @param $str_name
 * @return null
 */
function langStr($str_name) {
    return getLangStr($str_name);
}

/**
 * Alias for GetLangStr()
 * @param $str_name
 * @return null
 */
function __($str_name) {
    return getLangStr($str_name);
}

/**
 * Получить менеджер настроек
 * @return ConfigManager
 */
function getCfgManager() {
    global $cfg;
    return $cfg;
}

/**
 * Получить значение указанного параметра
 * @param $param
 * @param null $default
 * @return mixed|null
 */
function getCfgValue($param, $default = null) {
    return getCfgManager()->get($param, $default);
}

/**
 * Функция принимает изображение из dataURI, проверяет его валидность,
 * сохраняет под уникальным файлом и возвращает готовую к использованию
 * относительную ссылку.
 * @param $data_uri
 * @return bool|string
 * @throws Exception
 */
function saveAvatarImageFromDataURI($data_uri) {
// Раз дошли до сюды, то теперь непосредственно сохранение изображения и сохранение пользователя в базу!
    $img_dir = __DIR__ . '/../public/res/avatars/';
    $si = new \claviska\SimpleImage();
    // приём изображения с помощью SimpleImage, а заодно проверка на валидность изображения!
    $si->fromDataUri($data_uri); // картинку получить из dataURI от canvas-а кропнутого

    // создание директории для аватарок, если её нет
    if (!is_dir($img_dir)) {
        mkdir($img_dir, 0777, true);
    }

    // если слишком большое изображение пришло и если включено автоизменение размера
    $max_width = (int)getCfgValue('max_image_width', 1024);
    if (getCfgValue('auto_resize_image') && $si->getWidth() > $max_width) {
        $si->resize($max_width); // пропорциональное изменение размера изображения до указанной ширины
    }

    // сформировать имя файла -- md5 от текущего времени + расширение файла из MimeType
    $img_file = md5(time()) . '.' . explode('/', $si->getMimeType())[1];
    $si->toFile($img_dir . $img_file); // сохранение изображения в файл

    return '/res/avatars/' . $img_file; // формирование части URL аватарки (путь относительный)
}