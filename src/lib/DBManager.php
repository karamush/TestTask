<?php
/**
 * Класс для работы с базой данных.
 * Содержит все необходимые методы для реализации логики приложения.
 * User: karamush
 * Date: 014 14.10.19
 * Time: 13:46
 */

class DBManager extends SafeMySQL {

    /**
     * Существует ли в базе пользователь с указанным email-ом
     * @param $email
     * @return bool
     */
    public function user_email_exists($email) {
        return $this->getOne('SELECT COUNT(id) FROM users WHERE email=?s', $email) > 0;
    }

    /**
     * Получить пользователя по ID
     * @param $user_id
     * @return array|FALSE
     */
    public function user_get_by_id($user_id) {
        return $this->getRow('SELECT * FROM users WHERE id=?i', $user_id);
    }

    /**
     * Получить пользователя по email
     * @param $user_email
     * @return array|FALSE
     */
    public function user_get_by_email($user_email) {
        return $this->getRow('SELECT * FROM users WHERE email=?s', $user_email);
    }

    /**
     * Добавить нового пользователя!
     * @param $email
     * @param $hash_password
     * @param $name
     * @param $surname
     * @param $avatar
     * @param $reg_time
     * @param $reg_ip
     * @param null $info
     * @return bool|int
     */
    public function user_add($email, $hash_password, $name, $surname, $avatar, $reg_time, $reg_ip, $info = null) {
        $sql = $this->parse('INSERT INTO users(name, surname, email, password, avatar, reg_time, reg_ip, info) 
                                         VALUE(?s, ?s, ?s, ?s, ?s, ?i, ?s, ?s)',
            $name, $surname, $email, $hash_password, $avatar, $reg_time, $reg_ip, $info);
        if (!$this->query($sql)) return false;
        return $this->insertId();
    }



}