<?php
require 'base.php';

require 'localconf.php';

require 'upgrade.php';
require 'execstream.php';
require 'escl_discover.php';

if (isset($_GET['_phpinfo'])) {
    phpinfo();
    exit;
}
if (isset($_GET['tst'])) {
    execstream('echo 0; sleep 1; echo 1; sleep 1; echo 2 >&2; sleep 1; echo 3; sleep 1; echo 4 >&2;');
    flush();
    exit;
}

?>
<html>

<head>
    <meta charset="UTF-8" />
    <title>LinuxScanningLaboratory</title>
    <script src="jquery-4.0.0.min.js"></script>
    <script>
        const ts = <?= $ts ?>;
    </script>
    <link href="index.css?<?= $ts ?>" rel="stylesheet">
</head>

<body>
    <span style="float:right;"><span class="version"></span><span class="upgrade"></span></span>
    <h2>TG-Soft / graphax LinuxScanningLaboratory</h2>
    <?php require 'msg.php' ?>
    <p>eSCL capable devices: <select name="scanner">
            <option value="_search" disabled selected>Searching, please wait...</option>
        </select>
    </p>
    <div id="actionzone" style="display:none;">
        <p>Selected scanner: <span id="activeScanner"></span></p>
    </div>
    <iframe id="execiframe" style="display:none;" src="?tst&<?= $ts ?>"></iframe>
    <pre id="execresult"></pre>
    <script src="index.js?<?= $ts ?>"></script>
</body>

</html>