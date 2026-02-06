<?php
if (isset($_GET['upgrade'])) {
    $dir = __DIR__;
    $upgradescript = "$dir/.upgrade.sh";
    if (!file_exists($upgradescript)) exit;
    $user = trim(shell_exec('stat -c %U ' . escapeshellarg($upgradescript)));
    $res = `sudo -u $user $upgradescript`;
    if (isset($_GET['raw'])) {
        header('Content-Type: text/plain');
        print($res);
    } else {
        header("Location: $self?post_upgrade&$ts");
    }
    exit;
}
if (isset($_GET['post_upgrade'])) {
    $_GET['msg'] = 'Upgraded to <span class="version"></span>.';
}
