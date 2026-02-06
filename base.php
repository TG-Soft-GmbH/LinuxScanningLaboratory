<?php
set_time_limit(600);
ob_implicit_flush(true);
ob_end_flush();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$self = dirname($_SERVER['SCRIPT_NAME']) . '/';
$ts = time();
