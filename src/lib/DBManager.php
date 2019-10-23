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
     * Обновить время и IP последнего запроса указанного пользователя
     * @param $user_id
     * @param $time
     * @param $ip
     * @return bool
     */
    public function user_update_last_time_ip($user_id, $time, $ip) {
        return !!$this->query('UPDATE users SET last_time=?i, last_ip=?s WHERE id=?i', $time, $ip, $user_id);
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

    public function user_avatar_update($user_id, $avatar_url) {
        return !!$this->query('UPDATE users SET avatar=?s WHERE id=?i', $avatar_url, $user_id);
    }

    /* ip bans! ^^ */

    /**
     * Получить статус бана указанного IP (время и количество ошибок).
     * @param $ip
     * @return array
     */
    public function ip_ban_status($ip) {
        return $this->getRow('SELECT * FROM ip_bans WHERE ip=?s', $ip);
    }

    /**
     * Увеличить на единицу количество ошибок для указанного IP,
     * сохранив также время последней ошибки.
     * @param $ip
     * @param $last_time
     * @return bool
     */
    public function ip_inc_err($ip, $last_time) {
        return !!$this->query('INSERT INTO ip_bans (ip, err_count, ban_time) VALUES(?s, 1, ?i) 
                                ON DUPLICATE KEY UPDATE err_count = err_count + 1', $ip, $last_time);
    }

    /**
     * Снять блокировку по IP
     * @param $ip
     * @return FALSE|resource
     */
    public function ip_unban($ip) {
        return $this->query('DELETE FROM ip_bans WHERE ip=?s', $ip);
    }

}