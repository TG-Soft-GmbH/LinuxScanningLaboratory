<?php
set_time_limit(600);
ob_implicit_flush(true);
ob_end_flush();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$self = dirname($_SERVER['SCRIPT_NAME']) . '/';
$ts = time();
$EOF_TAG = 'EOF';

$path = $_GET['path'] ?? '';
if ($path) {
    $path = $datastore . '/' . $instance . '-' . $path;
    if (!is_dir($path)) {
        @mkdir($path, 0777, true);
        chmod($path, 0777);
    }
}

$out = $_GET['out'] ?? '';
if ($out) {
    $out = $path . '/' . $out;
}

function copy_dir_777($src, $dst)
{
    if (!is_dir(dirname($dst))) throw new Exception('copy_dir: Target Directory "' . dirname($dst) .  '" is not writeable');
    $dir = opendir($src);
    if (is_dir($dst) || is_file($dst)) throw new Exception('copy_dir: Target Directory "' . dirname($dst) .  '" already exists');
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_dir_777($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    $permissions = 0777;
    chmod($dst, $permissions);
    $iterator = new RecursiveDirectoryIterator($dst, RecursiveDirectoryIterator::SKIP_DOTS);
    foreach ($iterator as $item) {
        chmod($item, $permissions);
    }
}
