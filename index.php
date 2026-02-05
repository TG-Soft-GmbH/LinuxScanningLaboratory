<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$self = dirname($_SERVER['SCRIPT_NAME']) . '/';
$ts = time();

if (isset($_GET['_phpinfo'])) {
    phpinfo();
    exit;
}
if (isset($_GET['upgrade'])) {
    $dir = __DIR__;
    $upgradescript = "$dir/.upgrade.sh";
    if(!file_exists($upgradescript)) exit;
    $user = trim(shell_exec('stat -c %U ' . escapeshellarg($upgradescript)));
    $res = `sudo -u $user $upgradescript`;
    if (isset($_GET['raw'])) {
        header('Content-Type: text/plain');
        print($res);
    } else {
        header("Location: $self?post_upgrade");
    }
    exit;
}
if(isset($_GET['post_upgrade'])) {
    $_GET['msg'] = 'Upgraded to <span class="version"></span>.';
}
if (isset($_GET['find_scanners'])) {
    $pre = [];
    $scanners = [];
    $lines = [];
    exec('avahi-browse -rt _uscan._tcp', $lines);
    $expect = 'device';
    $name = '';
    $ip = '';
    $port = '';
    $proto = '';
    $path = '';
    foreach ($lines as $line) {
        //$pre[] = "LINE: $line\n";
        switch ($expect) {
            case 'device':
                if (preg_match('/^= \w+ IPv4 ([\w \-\[\]]+?)\s+_uscan/', $line, $matches)) {
                    $name = $matches[1];
                    $pre[] = "NAME: $name\n";
                    $ip = '';
                    $expect = 'ip';
                }
                break;
            case 'ip':
                if (preg_match('/^\s+address = \[([\d\.:]+)\]+$/', $line, $matches)) {
                    $ip = $matches[1];
                    $pre[] = "IP: $ip\n";
                    $port = '';
                    $proto = '';
                    $expect = 'port';
                }
                break;
            case 'port':
                if (preg_match('/^\s+port = \[([\d\.]+)\]+$/', $line, $matches)) {
                    $port = $matches[1];
                    $proto = ($port == 443 || $port == 8082) ? 'https' : 'http';
                    $pre[] = "PORT: $port\n";
                    $pre[] = "PROTO: $proto\n";
                    $path = '';
                    $expect = 'path';
                }
                break;
            case 'path':
                if (preg_match('/^\s+txt = .*"rs=(\w+?)"/', $line, $matches)) {
                    $path = $matches[1];
                    $pre[] = "PATH: $path\n";

                    $scanners["$name [$ip]"] = "escl:$proto://$ip:$port/$path";

                    $device = '';
                    $expect = 'device';
                }
                break;
        }
    }
    if (empty($scanners)) {
        echo '<option value="" disabled selected>No scanners found, please search again...</option>' . "\n";
    } else {
        echo '<option value="" disabled selected>Please select...</option>' . "\n";
        foreach ($scanners as $name => $scanpath) {
            echo "<option value=\"$scanpath\">$name</option>\n";
        }
    }
    echo '<option value="_search">Search for more scanners...</option>' . "\n";
    exit;
}

?>
<html>

<head>
    <meta charset="UTF-8" />
    <script src="jquery-4.0.0.min.js"></script>
    <title>LinuxScanningLaboratory</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            padding: 25px;
            font-family: 'Roboto', sans-serif;
            font-size: 1.2em;
            font-style: normal;
        }

        input,
        select {
            font-size: 1em;
        }

        h2,
        p {
            margin-bottom: 18px;
        }

        p>span {
            color: darkgreen;
        }

        .info {
            background-color: lightskyblue;
            border: 2px solid darkcyan;
            padding: 22px;
        }
    </style>
    <script>
        const ts = <?= $ts ?>;
    </script>
</head>

<body>
    <span style="float:right;"><span class="version"></span><span class="upgrade"></span></span>
    <h2>TG-Soft / graphax LinuxScanningLaboratory</h2>
    <?php if(isset($_GET['msg'])) { ?>
        <p class="info">
            <span style="float:right;"><a href="#" onClick="$(this).parent().parent().remove()">Close Message</a></span>
            <?= $_GET['msg'] ?>
        </p>
        <script>
            history.replaceState( {} , '', '<?= $self ?>');
        </script>
    <?php } ?>
    <p>eSCL capable devices: <select name="scanner">
            <option value="_search" disabled selected>Searching, please wait...</option>
        </select>
    <p>Active scanner: <span id="activeScanner"></span>
        <!-- <pre><?= print_r($scanners) ?></pre> -->
        <script>
            let activeScanner = '';
            $(function() {
                $.getJSON(`release.json?${ts}`, function(localdata) {
                    if (!localdata) return;
                    $('.version').text('Version ' + localdata.version);
                    $.getJSON(`https://raw.githubusercontent.com/TG-Soft-GmbH/LinuxScanningLaboratory/refs/heads/main/release.json?${ts}`, function(remotedata) {
                        if (!remotedata) return;
                        if(localdata.version != remotedata.version) {
                            $('.upgrade').html(` - <a href="?upgrade" style="color:darkorange;">Upgrade to Version ${remotedata.version}</a>`);
                        }
                    });
                });
                let $scannersel = $('select[name=scanner]');
                $scannersel.on('change', function() {
                    if ($scannersel.val() == '_search') {
                        $scannersel.load('?find_scanners');
                        $('#activeScanner').text('');
                    } else {
                        $('#activeScanner').text($scannersel.val());
                    }
                });
                $scannersel.load('?find_scanners');
            });
        </script>
</body>

</html>
