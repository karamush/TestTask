<?php
/**
 * User: karamush
 * Date: 015 15.10.19
 * Time: 12:51
 */

$__twig = null;

function __getTplDir() {
    return __DIR__ . '/../resources/templates/';
}

/**
 * Проинициализировать шаблонизатор
 * @param bool $debug_mode
 * @return \Twig\Environment
 */
function twigInit($debug_mode = false) {
    global $__twig;
    $loader = new \Twig\Loader\FilesystemLoader(__getTplDir());
    $__twig = new \Twig\Environment($loader, [
        'cache' => $debug_mode ? false : __DIR__ . '/../data/cache',
        'debug' => $debug_mode,
        'auto_reload' => $debug_mode
    ]);
    return $__twig;
}

/**
 * Получить объект шаблонизатора
 * @return \Twig\Environment
 */
function getTwig() {
    global $__twig;
    if (!$__twig)
        twigInit();
    return $__twig;
}

/**
 * Отрендерить указанный шаблон (по имени), передав переменные
 * @param $template_name
 * @param array $variables
 * @param bool $result_return
 * @return bool|string
 */
function render($template_name = 'index', array $variables = [], $result_return = false) {
    try {
        // вывод flash-сообщений, если таковые имеются!
        if (Utils::infosExists()) {
            getTwig()->addGlobal('infos', Utils::getInfos());
        }
        if (Utils::errorsExists()) {
            getTwig()->addGlobal('errors', Utils::getErrors());
        }
        if (Utils::warningsExists()) {
            getTwig()->addGlobal('warnings', Utils::getWarnings());
        }
        if (Utils::successesExists()) {
            getTwig()->addGlobal('successes', Utils::getSuccesses());
        }

        if (!file_exists(__getTplDir() . $template_name . '.twig'))
            $template_name = '404';

        $content = getTwig()->render($template_name . '.twig', $variables);
        if ($result_return)
            return $content;
        else {
            echo $content;
            return true;
        }
    } catch (Exception $e) {
        echo '<pre>';
        echo $e;
        echo '</pre>';
        return false;
    }
}