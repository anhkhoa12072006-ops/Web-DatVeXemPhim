<?php
class Setting {
    public static function getAll($db) {
        $cauhinh_data = $db->select("SELECT * FROM cauhinh");
        $settings = [];
        if (!empty($cauhinh_data)) {
            foreach ($cauhinh_data as $row) {
                $settings[$row['tukhoa']] = $row['giatri'];
            }
        }
        return $settings;
    }

    public static function update($db, $vietqr_bank, $vietqr_account, $vietqr_name) {
        $vietqr_bank = $db->escape($vietqr_bank);
        $vietqr_account = $db->escape($vietqr_account);
        $vietqr_name = $db->escape($vietqr_name);

        $configs = [
            'vietqr_bank' => ['value' => $vietqr_bank, 'desc' => 'Mã Ngân hàng (VietQR)'],
            'vietqr_account' => ['value' => $vietqr_account, 'desc' => 'Số tài khoản nhận tiền'],
            'vietqr_name' => ['value' => $vietqr_name, 'desc' => 'Tên chủ tài khoản ngân hàng']
        ];

        foreach ($configs as $key => $data) {
            $existing = $db->select("SELECT * FROM cauhinh WHERE tukhoa = '$key'");
            if (empty($existing)) {
                $db->execute("INSERT INTO cauhinh (tukhoa, giatri, mota) VALUES ('$key', '{$data['value']}', '{$data['desc']}')");
            } else {
                $db->execute("UPDATE cauhinh SET giatri = '{$data['value']}' WHERE tukhoa = '$key'");
            }
        }
        return true;
    }
}
