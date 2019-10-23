<?php
/**
 * Точка входа в приложение.
 * User: karamush
 * Date: 014 14.10.19
 * Time: 12:38
 */

session_start();

require __DIR__ . '/../bootstrap/app.php';

/* Mini auto router */
$routes_dir = __DIR__ . '/../src/routes/';
// получение URI
$uri = ltrim(Utils::getArrayValue($_SERVER, 'REQUEST_URI'), '/');
// определение пути (с отсечением GET-параметров)
$path = trim((strpos($uri, '?')) ? strtolower(substr($uri, 0, strpos($uri, '?'))) : $uri);
// нужен только первый элемент пути, это и будет файлом маршрута
$route = explode('/', $path)[0];
// если ничего не указано или левые символы встретились, считаем, что это index!
if ($uri == '' || $path == '' || !preg_match('/^[a-zA-Z0-9_-]*$/', $route)) {
    $route = 'index';
}
$route_file = $routes_dir . $route . '.php';

/* некоторые глобальные переменные для шаблонизатора */
getTwig()->addGlobal('ROUTE', $route);
getTwig()->addGlobal('PATH', $path);

// если есть такой файл маршрута, то подключить его и исполнить
if (file_exists($route_file)) {
    require $route_file;
} else { // если маршрута такого нет, то выдать 404
    header("HTTP/1.0 404 Not Found");
    render('404');
}