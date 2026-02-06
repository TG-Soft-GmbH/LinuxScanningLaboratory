<?php
function execstream($cmd)
{
    echo '<!doctype html><html><body><pre>';
    passthru('bash -c "echo; ' . $cmd . '" 2>&1; echo Execution finished. Exit code: $?');
    echo '</pre><script>parent.postMessage("execstream_done", "*");</script></body></html>';
}

