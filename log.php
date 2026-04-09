<?php

$log = $_GET['log'] ?? '';
if ($log && $path) {
    $finalize = str_ends_with($log, $EOF_TAG);
    if ($finalize) {
        $log = substr($log, 0, -strlen($EOF_TAG));
    }
    if ($log) {
        file_put_contents($path . '/log.txt', $log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    if ($finalize) {
        $eid = basename($path);
        copy_dir_777($path, $datastore_copy . '/' . $eid);
        $srcdir = dirname($path);
        `echo "$srcdir" > /var/www/html/linuxscanlab/scandata/tmp; echo "$eid" >> /var/www/html/linuxscanlab/scandata/tmp`;
        //`cd "$srcdir" && zip -r "$eid.zip" "$eid"; xxrm -rf "$path"`;
    }
    exit;
}
