<?php
class Seat {
    public static function getRoomInfo($db, $maphong) {
        $maphong = (int)$maphong;
        return $db->select("
            SELECT p.*, r.tenrap 
            FROM phongchieu p 
            LEFT JOIN rapchieu r ON p.marap = r.marap 
            WHERE p.maphong = $maphong
        ");
    }

    public static function getAllByRoom($db, $maphong) {
        $maphong = (int)$maphong;
        return $db->select("SELECT * FROM ghengoi WHERE maphong = $maphong ORDER BY LEFT(tenghe, 1), CAST(SUBSTRING(tenghe, 2) AS UNSIGNED)");
    }

    public static function add($db, $maphong, $tenghe, $loaighe) {
        $maphong = (int)$maphong;
        $tenghe = $db->escape($tenghe);
        $loaighe = $db->escape($loaighe);
        
        $sql = "INSERT INTO ghengoi (tenghe, loaighe, maphong) VALUES ('$tenghe', '$loaighe', $maphong)";
        return $db->execute($sql);
    }

    public static function update($db, $maghe, $tenghe, $loaighe) {
        $maghe = (int)$maghe;
        $tenghe = $db->escape($tenghe);
        $loaighe = $db->escape($loaighe);
        
        $sql = "UPDATE ghengoi SET tenghe='$tenghe', loaighe='$loaighe' WHERE maghe=$maghe";
        return $db->execute($sql);
    }

    public static function delete($db, $maghe) {
        $maghe = (int)$maghe;
        
        // Kiểm tra xem ghế này đã được đặt vé chưa
        $check_ticket = $db->select("SELECT * FROM chitietve WHERE maghe = $maghe");
        if (!empty($check_ticket)) {
            return false;
        }
        
        return $db->execute("DELETE FROM ghengoi WHERE maghe=$maghe");
    }

    public static function autoGenerate($db, $maphong, $rows, $cols, $loaighe) {
        $maphong = (int)$maphong;
        $rows = (int)$rows;
        $cols = (int)$cols;
        $loaighe = $db->escape($loaighe);

        // KIỂM TRA RÀNG BUỘC: Xem phòng này đã có ghế nào bị dính vé chưa
        $check_room_tickets = $db->select("
            SELECT c.maghe FROM chitietve c 
            JOIN ghengoi g ON c.maghe = g.maghe 
            WHERE g.maphong = $maphong LIMIT 1
        ");
        
        if (!empty($check_room_tickets)) {
            return 'has_tickets';
        }

        // Nếu phòng chưa có vé nào bán ra -> Cho phép xóa ghế cũ và tạo mới
        $db->execute("DELETE FROM ghengoi WHERE maphong=$maphong");
        
        $success = true;
        for ($i = 1; $i <= $rows; $i++) {
            $row_letter = chr(64 + $i);
            for ($j = 1; $j <= $cols; $j++) {
                $tenghe = $row_letter . $j;
                $sql = "INSERT INTO ghengoi (tenghe, loaighe, maphong) VALUES ('$tenghe', '$loaighe', $maphong)";
                if (!$db->execute($sql)) $success = false;
            }
        }
        
        if ($success) {
            $total = $rows * $cols;
            $db->execute("UPDATE phongchieu SET tongghe=$total WHERE maphong=$maphong");
            return $total;
        }
        return false;
    }
    public static function updateRowType($db, $maphong, $row_letter, $loaighe) {
        $maphong = (int)$maphong;
        $loaighe = $db->escape($loaighe);
        
        if (empty($row_letter)) return false;

        // Nếu row_letter là mã định danh nội bộ "_numeric_", cập nhật các ghế chỉ có số
        if ($row_letter === '_numeric_') {
            $sql = "UPDATE ghengoi SET loaighe = '$loaighe' WHERE maphong = $maphong AND tenghe REGEXP '^[0-9]+$'";
        } else {
            $row_letter_esc = $db->escape($row_letter);
            $sql = "UPDATE ghengoi SET loaighe = '$loaighe' WHERE maphong = $maphong AND tenghe REGEXP '^{$row_letter_esc}[0-9]+$'";
        }
        return $db->execute($sql);
    }
}
