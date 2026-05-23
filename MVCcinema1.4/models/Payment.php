<?php
class Payment {
    public static function createVietQROrder($db, $tendn, $tongtien, $masuat, $selected_seats) {
        $tendn = $db->escape($tendn);
        $tongtien = (int)$tongtien;
        $masuat = (int)$masuat;

        $sql = "INSERT INTO hoadon (tendn, tongtien, phuongthuctt, trangthai) VALUES ('$tendn', $tongtien, 'Chuyển khoản VietQR', 'Đã thanh toán')";
        if (!$db->execute($sql)) {
            return false;
        }

        $mahd = $db->getInsertId();

        // Lấy giá vé của suất chiếu
        $showtime_info = $db->select("SELECT p.giave FROM suatchieu s JOIN phim p ON s.maphim = p.maphim WHERE s.masuat = $masuat");
        $base_giave = !empty($showtime_info) ? $showtime_info[0]['giave'] : 0;

        foreach ($selected_seats as $maghe) {
            $maghe = (int)$maghe;
            $seat_info = $db->select("SELECT loaighe FROM ghengoi WHERE maghe = $maghe");
            $giave = $base_giave;
            if (!empty($seat_info) && $seat_info[0]['loaighe'] == 'VIP') {
                $giave += 40000;
            }
            $db->execute("INSERT INTO chitietve (mahd, masuat, maghe, giave) VALUES ($mahd, $masuat, $maghe, $giave)");
        }

        return $mahd;
    }

    public static function getSettings($db) {
        $cauhinh_data = $db->select("SELECT * FROM cauhinh");
        $settings = [];
        foreach ($cauhinh_data as $row) {
            $settings[$row['tukhoa']] = $row['giatri'];
        }
        return $settings;
    }
}
