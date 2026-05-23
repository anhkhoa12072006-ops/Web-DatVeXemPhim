<?php
class Room {
    public static function getAll($db) {
        return $db->select("
            SELECT p.*, r.tenrap,
                   (SELECT COUNT(*) FROM ghengoi g WHERE g.maphong = p.maphong) as ghehientai,
                   (SELECT COUNT(*) FROM suatchieu s WHERE s.maphong = p.maphong) as suatchieu
            FROM phongchieu p
            LEFT JOIN rapchieu r ON p.marap = r.marap
            ORDER BY p.maphong
        ");
    }

    public static function add($db, $marap, $tenphong, $tongghe, $tinhtrang) {
        $marap = (int)$marap;
        $tenphong = $db->escape($tenphong);
        $tongghe = (int)$tongghe;
        $tinhtrang = $db->escape($tinhtrang);
        
        $sql = "INSERT INTO phongchieu (marap, tenphong, tongghe, tinhtrang) VALUES ($marap, '$tenphong', $tongghe, '$tinhtrang')";
        if ($db->execute($sql)) {
            $res = $db->select("SELECT MAX(maphong) as max_id FROM phongchieu");
            if (!empty($res)) {
                $new_maphong = $res[0]['max_id'];
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
                $count = 0;
                foreach ($rows as $r) {
                    for ($c = 1; $c <= 10; $c++) {
                        if ($count >= $tongghe) break 2;
                        $tenghe = $r . $c;
                        $db->execute("INSERT INTO ghengoi (maphong, tenghe, loaighe) VALUES ($new_maphong, '$tenghe', 'Thường')");
                        $count++;
                    }
                }
            }
            return true;
        }
        return false;
    }

    public static function update($db, $maphong, $marap, $tenphong, $tongghe, $tinhtrang) {
        $maphong = (int)$maphong;
        $marap = (int)$marap;
        $tenphong = $db->escape($tenphong);
        $tongghe = (int)$tongghe;
        $tinhtrang = $db->escape($tinhtrang);
        
        $sql = "UPDATE phongchieu SET marap=$marap, tenphong='$tenphong', tongghe=$tongghe, tinhtrang='$tinhtrang' WHERE maphong=$maphong";
        return $db->execute($sql);
    }

    public static function delete($db, $maphong) {
        $maphong = (int)$maphong;
        $check_tickets = $db->select("SELECT COUNT(*) as total FROM chitietve c JOIN suatchieu s ON c.masuat = s.masuat WHERE s.maphong = $maphong");
        if (!empty($check_tickets) && $check_tickets[0]['total'] > 0) {
            return false; // Cannot delete, has tickets
        }
        return $db->execute("DELETE FROM phongchieu WHERE maphong=$maphong");
    }
}
