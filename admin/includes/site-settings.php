<?php
/**
 * Membaca pengaturan website dari tabel pengaturan yang SUDAH ADA.
 * Tidak membuat/mengubah tabel database.
 * Mendukung tabel single-row maupun key/value dengan nama tabel/kolom umum.
 */
function fikes_setting_value(array $row, array $aliases, string $default = ''): string
{
    foreach ($aliases as $key) {
        if (array_key_exists($key, $row) && trim((string)$row[$key]) !== '') {
            return trim((string)$row[$key]);
        }
    }
    return $default;
}

function fikes_find_settings_table(PDO $pdo): ?array
{
    $preferred = [
        'pengaturan_website', 'pengaturan_web', 'pengaturan',
        'website_settings', 'site_settings', 'settings',
        'konfigurasi_website', 'konfigurasi'
    ];

    $tables = $pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    $tables = array_values(array_unique(array_map('strval', $tables)));
    $ordered = [];
    foreach ($preferred as $name) if (in_array($name, $tables, true)) $ordered[] = $name;
    foreach ($tables as $name) if (!in_array($name, $ordered, true)) $ordered[] = $name;

    $best = null;
    $bestScore = 0;
    foreach ($ordered as $table) {
        $st = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION");
        $st->execute([$table]);
        $cols = $st->fetchAll(PDO::FETCH_COLUMN);
        if (!$cols) continue;
        $lc = array_map('strtolower', $cols);
        $score = 0;
        foreach (['nama_website','nama_website','site_name','website_name','email','telepon','alamat','instagram','facebook','youtube','logo','google_maps','maps','copyright','deskripsi'] as $c) {
            if (in_array($c, $lc, true)) $score += 2;
        }
        foreach (['key','setting_key','nama','kunci'] as $c) if (in_array($c,$lc,true)) $score += 1;
        foreach (['value','setting_value','nilai','isi'] as $c) if (in_array($c,$lc,true)) $score += 1;
        if (in_array($table, $preferred, true)) $score += 5;
        if ($score > $bestScore) { $bestScore = $score; $best = ['table'=>$table,'columns'=>$cols]; }
    }
    return $bestScore >= 3 ? $best : null;
}

function fikes_load_site_settings(PDO $pdo): array
{
    $found = fikes_find_settings_table($pdo);
    if (!$found) return [];
    $table = str_replace('`','', $found['table']);
    $columns = array_map('strtolower', $found['columns']);

    $quoted = '`' . str_replace('`','``',$table) . '`';
    $rows = $pdo->query("SELECT * FROM $quoted LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$rows) return [];
    $row = array_change_key_case($rows, CASE_LOWER);

    // Jika format key/value, ubah menjadi associative setting.
    $keyCol = null; $valueCol = null;
    foreach (['key','setting_key','nama','kunci','kode'] as $c) if (in_array($c,$columns,true)) { $keyCol=$c; break; }
    foreach (['value','setting_value','nilai','isi','konten'] as $c) if (in_array($c,$columns,true)) { $valueCol=$c; break; }
    if ($keyCol && $valueCol && !isset($row['nama_website']) && !isset($row['site_name'])) {
        $all = $pdo->query("SELECT * FROM $quoted")->fetchAll(PDO::FETCH_ASSOC);
        $out=[];
        foreach ($all as $r) {
            $r=array_change_key_case($r,CASE_LOWER);
            $k=trim((string)($r[$keyCol]??''));
            if ($k!=='') $out[$k]=(string)($r[$valueCol]??'');
        }
        return $out;
    }
    return $row;
}

function fikes_site_settings(PDO $pdo): array
{
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = fikes_load_site_settings($pdo);
    return $cache;
}
