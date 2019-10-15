<?php
/**
 * Класс для считывания настроек с учётом различных сред запуска.
 * Основной файл настроек -- config_default.php
 * При наличие файла config_dev.php и его не игнорировании при загрузке настроек,
 * он будет загружен, а настройки, указанные в нём, переопределят настройки из config_default.
 * При наличие файла config_prod.php и если до этого не был загружен config_dev, он будет загружен,
 * а настройки, указанные в нём, переопределят настройки из config_default.
 *
 * User: karamush
 * Date: 014 14.10.19
 * Time: 14:21
 */

class ConfigManager {
    protected $configs = []; // тут будет храниться массив с настройками
    protected $dev_loaded = false; // устанавливается в true, если была произведена загрузка DEV-настроек

    /**
     * Загрузить настройки из указанного файла.
     * Если файл не будет найден или не вернёт массив, то return false
     * @param $file_path
     * @return bool|array
     */
    protected function loadFromFile($file_path) {
        if (!file_exists($file_path))
            return false;
        $result = require $file_path;
        if (!is_array($result))
            return false;
        return $result;
    }

    /**
     * Загрузить настройки из указанной папки.
     * Порядок загрузки такой:
     *  * config_default.php
     *  * config_dev.php(если $use_dev == true) || config_prod.php (если не загружен config_dev.php)
     * @param $directory         -- путь к папке с конфигами
     * @param bool $use_dev      -- использовать ли config_dev.php (можно использовать при dev-окружении)
     */
    function loadFromDir($directory, $use_dev = false) {
        // загрузка базовых настроек
        if ($cfg = $this->loadFromFile($directory . DIRECTORY_SEPARATOR . 'config_default.php')) {
            $this->configs = $cfg;
        }

        // попытка загрузить dev-настройки
        if ($use_dev && $cfg = $this->loadFromFile($directory . DIRECTORY_SEPARATOR . 'config_dev.php')) {
            $this->configs = array_merge($this->configs, $cfg);
            $this->dev_loaded = true;
        }

        // попытка загрузить prod-настройки
        if (!$this->dev_loaded && $cfg = $this->loadFromFile($directory . DIRECTORY_SEPARATOR . 'config_prod.php')) {
            $this->configs = array_merge($this->configs, $cfg);
        }
    }

    /**
     * Получить значение указанного параметра, иначе $default_value
     * @param $cfg_param_name
     * @param null $default_value
     * @return mixed|null
     */
    function get($cfg_param_name, $default_value = null) {
        return isset($this->configs[$cfg_param_name]) ? $this->configs[$cfg_param_name] : $default_value;
    }

    /**
     * Получить массив с названиями всех параметров из настроек
     * @return array
     */
    function getNames() {
        return array_keys($this->configs);
    }

    /**
     * Получить все настройки!
     * @return array
     */
    function getAll() {
        return $this->configs;
    }

    /**
     * Загружены ли DEV-настройки?
     * @return bool
     */
    function devMode() {
        return $this->dev_loaded;
    }

}