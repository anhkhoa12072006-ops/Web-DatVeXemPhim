<?php
class User {
    public static function getAll($db, $role_filter = '', $search = '') {
        $sql = "SELECT n.*,
                (SELECT COUNT(*) FROM hoadon h WHERE h.tendn = n.tendn) as sodon,
                (SELECT SUM(tongtien) FROM hoadon h WHERE h.tendn = n.tendn AND trangthai = 'Đã thanh toán') as tongtien
                FROM nguoidung n
                WHERE 1=1";

        if ($role_filter) {
            $sql .= " AND n.quyen = '" . $db->escape($role_filter) . "'";
        }
        if ($search) {
            $search_escaped = $db->escape($search);
            $sql .= " AND (n.tendn LIKE '%$search_escaped%' OR n.ghichu LIKE '%$search_escaped%')";
        }
        $sql .= " ORDER BY n.tendn";

        return $db->select($sql);
    }

    public static function add($db, $tendn, $matkhau, $quyen, $ghichu) {
        $tendn = $db->escape($tendn);
        $quyen = $db->escape($quyen);
        $ghichu = $db->escape($ghichu);
        $matkhau = (int)$matkhau;

        $existing = $db->select("SELECT * FROM nguoidung WHERE tendn = '$tendn'");
        if (!empty($existing)) {
            return false; // User exists
        }

        $sql = "INSERT INTO nguoidung (tendn, matkhau, quyen, ghichu) VALUES ('$tendn', $matkhau, '$quyen', '$ghichu')";
        return $db->execute($sql);
    }

    public static function update($db, $tendn_old, $quyen, $ghichu, $current_user) {
        $tendn_old = $db->escape($tendn_old);
        $quyen = $db->escape($quyen);
        $ghichu = $db->escape($ghichu);

        // Prevent self demotion
        if ($tendn_old == $current_user && $quyen == 'user') {
            return false;
        }

        $sql = "UPDATE nguoidung SET quyen='$quyen', ghichu='$ghichu' WHERE tendn='$tendn_old'";
        return $db->execute($sql);
    }

    public static function toggleStatus($db, $tendn) {
        $tendn = $db->escape($tendn);
        $user_info = $db->select("SELECT quyen, trangthai FROM nguoidung WHERE tendn='$tendn'");
        
        if ($user_info && $user_info[0]['quyen'] == 'admin') {
            return 'admin_error'; // Cannot disable admin
        }
        
        if ($user_info) {
            $new_status = ($user_info[0]['trangthai'] == 'Bị khóa') ? 'Hoạt động' : 'Bị khóa';
            if ($db->execute("UPDATE nguoidung SET trangthai='$new_status' WHERE tendn='$tendn'")) {
                return $new_status;
            }
        }
        return false;
    }

    public static function resetPassword($db, $tendn, $new_password) {
        $tendn = $db->escape($tendn);
        $new_password = (int)$new_password;
        return $db->execute("UPDATE nguoidung SET matkhau=$new_password WHERE tendn='$tendn'");
    }
}
