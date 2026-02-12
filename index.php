<?php
require 'localconf.php';
require 'base.php';
require 'upgrade.php';
require 'execstream.php';
require 'log.php';
require 'escl_discover.php';
require 'proxy.php';

if (isset($_GET['_phpinfo'])) {
    phpinfo();
    exit;
}
?>
<html>

<head>
    <meta charset="UTF-8" />
    <title>LinuxScanningLaboratory</title>
    <script src="jquery-4.0.0.min.js"></script>
    <script>
        const ts = Date.now();
        const dataprefix = '<?= $datastore . '/' . $instance . '-' ?>';
        const EOF_TAG = '<?= $EOF_TAG ?>';
    </script>
    <link href="index.css?<?= $ts ?>" rel="stylesheet">
</head>

<body>
    <div class="upgradeoverlay"><span class="loader"></span></div>
    <span style="float:right;"><span class="version"></span><span class="upgrade"></span></span>
    <h2>TG-Soft / graphax LinuxScanningLaboratory</h2>
    <?php require 'msg.php' ?>
    <div class="menu">
        <p>Local eSCL capable Devices: <select name="scanner">
                <option value="_search" disabled selected>Searching, please wait...</option>
            </select></p>
        <p id="actionzone" style="display:none;">
            Selected scanner: <span id="activeScanner"></span>
            <button cmd="flat">Scan from Flatbed</button>
            <button cmd="adf">Scan from ADF (Duplex)</button>
            <!-- <button cmd="tst">Test</button> -->
        </p>
    </div>
    <div template class="exhibit">
        <span style="float:right; margin-top: 16px;"><a href="#" onClick="$(this).closest('div').slideUp(500, function() { $(this).remove(); }); return false;">Remove</a></span>
        <table>
            <tr>
                <td>Exhibit</td>
                <td class="eid"></td>
            </tr>
            <tr>
                <td>Scanner</td>
                <td class="scanner"></td>
            </tr>
            <tr>
                <td>Command</td>
                <td class="cmd"></td>
            </tr>
        </table>
        <details>
            <summary>Device Capabilities: XML Manifest
                <spinner />
            </summary>
            <pre>Loading, please be patient... <spinner /><</pre>
        </details>
        <div class="output">
            <iframe class="terminal" src=""></iframe>
            <div class="pdf-scroll">
                <div class="pdf">Waiting for PDF...</div>
            </div>
            </table>
        </div>
        <script src="pdfjs/build/pdf.mjs" type="module"></script>
        <script src="pdf.js?<?= $ts ?>"></script>
        <script src="utils.js?<?= $ts ?>"></script>
        <script src="index.js?<?= $ts ?>"></script>
</body>

</html>