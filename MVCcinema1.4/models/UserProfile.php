<?php
class UserProfile {
    public static function getByUsername($db, $tendn) {
        $tendn = $db->escape($tendn);
        return $db->select("SELECT * FROM nguoidung WHERE tendn = '$tendn'");
    }

    public static function getStats($db, $tendn) {
        $tendn = $db->escape($tendn);
        $total_tickets = count($db->select("SELECT mahd FROM hoadon WHERE tendn = '$tendn'"));
        $total_spent_data = $db->select("SELECT SUM(tongtien) as total FROM hoadon WHERE tendn = '$tendn' AND trangthai = 'Đã thanh toán'");
        $total_spent = $total_spent_data[0]['total'] ?? 0;

        return [
            'total_tickets' => $total_tickets,
            'total_spent' => $total_spent
        ];
    }

    public static function updateProfile($db, $tendn, $ghichu) {
        $tendn = $db->escape($tendn);
        $ghichu = $db->escape($ghichu);
        return $db->execute("UPDATE nguoidung SET ghichu = '$ghichu' WHERE tendn = '$tendn'");
    }

    public static function changePassword($db, $tendn, $new_password) {
        $tendn = $db->escape($tendn);
        $new_password = (int)$new_password;
        return $db->execute("UPDATE nguoidung SET matkhau = $new_password WHERE tendn = '$tendn'");
    }
}
