<?php

if (isset($_GET['url']) && $_GET['url']) {
    $data = file_get_contents($_GET['url']);
    if ($out) {
        file_put_contents($out, $data);
    }
    echo $data;
    exit;
}
