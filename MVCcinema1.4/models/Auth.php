<?php
class Auth {
    public static function login($db, $tendn, $matkhau) {
        $tendn = $db->escape($tendn);
        $matkhau = (int)$matkhau;
        
        $users = $db->select("SELECT * FROM nguoidung WHERE tendn = '$tendn' AND matkhau = $matkhau");
        return !empty($users) ? $users[0] : null;
    }

    public static function usernameExists($db, $tendn) {
        $tendn = $db->escape($tendn);
        $existing = $db->select("SELECT tendn FROM nguoidung WHERE tendn = '$tendn'");
        return !empty($existing);
    }

    public static function register($db, $tendn, $matkhau, $ghichu) {
        $tendn = $db->escape($tendn);
        $matkhau = (int)$matkhau;
        $ghichu = $db->escape($ghichu);
        
        $sql = "INSERT INTO nguoidung (tendn, matkhau, quyen, ghichu) VALUES ('$tendn', $matkhau, 'user', '$ghichu')";
        return $db->execute($sql);
    }
}
