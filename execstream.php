<?php

if (isset($_GET['cmd']) && $_GET['cmd']) {
    echo '<!doctype html><html><head><style>*{margin:0;padding:0;overflow:hidden;white-space:pre-wrap;overflow-wrap:break-word;word-break:break-word;}body{padding:6px;}</style></head><body><pre>';
    $cmd = $_GET['cmd'];
    $split = explode(' ', $cmd, 2);
    $cmdfile = 'cmd/' . $split[0] . '.sh';
    if (is_file($cmdfile)) {
        $cmd = $cmdfile . ' ' . ($split[1] ?? '');
    }
    if($out && isset($_GET['dev']) && $_GET['dev']) {
        file_put_contents($out, 'Device: ' . $_GET['dev'] . PHP_EOL . PHP_EOL);
    }
    passthru('{ echo "Executing:"; echo "' . $cmd . '"; echo; } 2>&1 | tee -a ' . $out . ' 2>&1');
    passthru('bash -c \'' . $cmd . '; rc=$?; echo; echo "Execution finished. Exit code: $rc";\' 2>&1 | tee -a ' . $out . ' 2>&1');
    echo '</pre><script>parent.postMessage("execstream_done", "*");</script></body></html>';
    flush();
    if($out) {
        $exhibit = dirname($out);
        copy_dir_777($exhibit, $datastore_copy . '/' . basename($exhibit));
    }
    exit;
}
