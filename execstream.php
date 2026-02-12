<?php

if (isset($_GET['cmd']) && $_GET['cmd']) {
    echo '<!doctype html><html><head><style>*{margin:0;padding:0;overflow:hidden;white-space:pre-wrap;overflow-wrap:break-word;word-break:break-word;}body{padding:6px;}</style></head><body><pre>';
    $info = $_GET['info'] ?? '';
    $dev = $_GET['dev'] ?? '';
    if($out && $info) {
        file_put_contents($out, 'Device: ' . $info . PHP_EOL . PHP_EOL);
    }
    $cmd = $_GET['cmd'];
    $split = explode(' ', $cmd, 2);
    $cmdfile = 'cmd/' . $split[0] . '.sh';
    if (is_file($cmdfile)) {
        $cmd = $cmdfile . ' ' . $path . ' ' . $dev;
    }
    passthru('{ echo "Command:"; echo "' . $cmd . '"; echo; } 2>&1 | tee -a ' . $out . ' 2>&1');
    if (is_file($cmdfile)) {
        passthru('{ echo "Executing: ' . $cmdfile . '"; cat "' . $cmdfile . '"; echo; echo; } 2>&1 | tee -a ' . $out . ' 2>&1');
    }
    passthru('bash -c \'' . $cmd . '; rc=$?; echo; echo "Execution finished. Exit code: $rc"; echo;\' 2>&1 | tee -a ' . $out . ' 2>&1');
    echo '</pre><script>parent.postMessage("execstream_done", "*");</script></body></html>';
    flush();
    exit;
}
