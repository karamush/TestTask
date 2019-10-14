<?php
/**
 * Класс для работы с разными переводами.
 * Переводы хранятся в ini-файлах, но секции не учитываются,
 * то есть, просто ключ=значение.
 * Чтобы перевод считался валидным и учитывался при поиске,
 * он должен содержать параметр lang со значением этого перевода (ru, en, etc...).
 * Можно включить автосохранение языка в куки при явном указании языка через GET,
 * тогда достаточно будет одного GET-запроса для установления языка, а дальше он будет браться из кукисов.
 *
 * User: karamush
 * Date: 014 14.10.19
 * Time: 17:16
 */

class LangManager {
    protected $langs_path = './langs/';             // путь, где будут искаться переводы
    protected $filename_format = '{LANG}.ini';      // формат файлов переводов
    protected $default_lang    = 'en';              // основной язык
    protected $langs = [];                          // массив с переводами будет тут
    protected $current_lang = null;                 // текущее название языка
    protected $save_lang_to_cookies = true;         // автоматическое сохранение языка в куки, если он установлен через GET

    /**
     * Установить формат файлов переводов (будет использоваться для поиска)
     * @param string $filename_format
     */
    public function setFilenameFormat(string $filename_format) {
        $this->filename_format = $filename_format;
    }

    /**
     * Получить формат файлов переводов
     * @return string|null
     */
    public function getFilenameFormat() {
        return $this->filename_format;
    }

    function __construct($langs_path = null, $filename_format = null) {
        if ($langs_path)
            $this->langs_path = $langs_path;
        if ($filename_format)
            $this->filename_format = $filename_format;
    }

    /**
     * Получить результирующее имя файла перевода
     * @param $lang_name
     * @return mixed
     */
    protected function getFormattedFilename($lang_name) {
        return str_replace('{LANG}', $lang_name, $this->filename_format);
    }

    /**
     * Проверяет, валидный ли файл перевода.
     * @param $filename     -- путь к файлу перевода
     * @param array $lang   -- уже распарсенный этот же файл
     * @return bool
     */
    protected function isValidLangFile($filename, array $lang) {
        return isset($lang['lang']) && basename($filename) == $this->getFormattedFilename($lang['lang']);
    }

    /**
     * Включить или отключить сохранение языка в куках, если он будет задан явно через GET
     * @param bool $enable
     */
    function setAutoSaveLangToCookies($enable = true) {
        $this->save_lang_to_cookies = $enable;
    }

    /**
     * Получить список доступных переводов
     * @return array|bool
     */
    function loadLanguages() {
        $files = glob($this->langs_path . '*.ini');
        if (!$files || !is_array($files) || !count($files)) {
            return false;
        }
        foreach ($files as $file) {
            $lang = parse_ini_file($file);
            if ($this->isValidLangFile($file, $lang)) {
                $this->langs[$lang['lang']] = $lang;
            }
        }
        return count($this->langs);
    }

    /**
     * Получить список доступных переводов
     * @return array
     */
    function getAvailableLangs() {
        return array_keys($this->langs);
    }

    /**
     * Существует ли указанный перевод
     * @param $lang
     * @return bool
     */
    protected function langExists($lang) {
        return isset($this->langs[$lang]);
    }

    /**
     * Получить строки указанного перевода
     * @param $lang
     * @return bool|mixed
     */
    function getLang($lang) {
        if ($this->langExists($lang))
            return $this->langs[$lang];
        return [];
    }

    /**
     * Установить текущий используемый язык для получения сообщений
     * @param $lang
     * @return mixed
     */
    function setCurrentLanguageName($lang) {
        if (!$this->langExists($lang))
            return false;
        return $this->current_lang = $lang;
    }


    /**
     * Получить имя текущего используемого языка
     * @return string|null
     */
    function getCurrentLanguageName() {
        if (!$this->current_lang)
            return $this->default_lang;
        return $this->current_lang;
    }

    /**
     * Получить текущий язык
     * @return array|false
     */
    function getCurrentLanguage() {
        return $this->getLang($this->getCurrentLanguageName());
    }

    /**
     * Получить список языков пользователя по приоритетам.
     * Высший приоритет у вручную заданного языка, затем $_GET-параметр, сессия, куки,
     * HTTP_ACCEPT_LANGUAGE и default_lang.
     * @return array
     */
    function getUserLangs() {
        $langs = [];

        if ($this->current_lang != null) {
            $langs[] = $this->current_lang;
        }

        if (isset($_GET['lang']) && is_string($_GET['lang'])) {
            $langs[] = $_GET['lang'];
            if ($this->save_lang_to_cookies) {
                setcookie('lang', $_GET['lang'], time() * 2);
            }
        }

        if (isset($_SESSION['lang']) && is_string($_SESSION['lang'])) {
            $langs[] = $_SESSION['lang'];
        }

        if (isset($_COOKIE['lang']) && is_string($_COOKIE['lang'])) {
            $langs[] = $_COOKIE['lang'];
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $h_accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $langs[] = strtolower(substr($h_accept, 0, strpos($h_accept, '-')));
        }
        // дефолтный язык
        $langs[] = $this->default_lang;
        $langs = array_unique($langs);
        $langs2 = [];
        foreach ($langs as $key => $value) {
            // фильтрация списка доступных языков (вдруг корявый параметр передан?!)
            if (preg_match('/^[0-9A-Za-z]*$/', $value) === 1)
                $langs2[$key] = $value;
        }
        return $langs2;
    }

    /**
     * Автоматически установить текущий язык, определив его из
     * списка языка пользователя.
     * @return string|null
     */
    function autoSetCurrentLanguage() {
        $user_langs = $this->getUserLangs();
        $this->setCurrentLanguageName(isset($user_langs[0]) ? $user_langs[0] : $this->default_lang);
        return $this->getCurrentLanguageName();
    }

    /**
     * Получить перевод указанной строки.
     * Можно указать язык.
     * Если не будет найден язык или не будет такой строки, вернётся $default_value
     * @param $str
     * @param null $default_value
     * @param null $lang
     * @return null
     */
    function getString($str, $default_value = null, $lang = null) {
        if ($lang)
            $this->setCurrentLanguageName($lang);
        if (!$lang = $this->getLang($this->getCurrentLanguageName()))
            return $default_value;
        return isset($lang[$str]) ? $lang[$str] : $default_value;
    }

    /**
     * Получить совмещённый перевод.
     * Сначала будет загружен дефолтный язык,
     * а потом строки из текущего указанного языка заменят его.
     * Необходимо это для работы отсутствующих в новом переводе строк.
     * @return array
     */
    function getMergedLang() {
        // на случай, если текущий язык совпал с дефолтным
        if ($this->getCurrentLanguageName() == $this->default_lang) {
            return $this->getLang($this->getCurrentLanguageName());
        }

        return array_merge($this->getLang($this->default_lang), $this->getLang($this->getCurrentLanguageName()));
    }

}