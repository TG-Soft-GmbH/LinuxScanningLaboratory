<?php
    if(isset($_GET['find_scanners'])) {
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
            switch($expect) {
                case 'device':
                    if(preg_match('/^= \w+ IPv\d ([\w \-\[\]]+?)\s+_uscan/', $line, $matches)) {
                        $name = $matches[1];
                        $pre[] = "NAME: $name\n";
                        $ip = '';
                        $expect = 'ip';
                    }
                    break;
                case 'ip':
                    if(preg_match('/^\s+address = \[([\d\.:]+)\]+$/', $line, $matches)) {
                        $ip = $matches[1];
                        $pre[] = "IP: $ip\n";
                        $port = '';
                        $proto = '';
                        $expect = 'port';
                    }
                    break;
                case 'port':
                    if(preg_match('/^\s+port = \[([\d\.]+)\]+$/', $line, $matches)) {
                        $port = $matches[1];
                        $proto = ($port == 443 || $port == 8082) ? 'https' : 'http';
                        $pre[] = "PORT: $port\n";
                        $pre[] = "PROTO: $proto\n";
                        $path = '';
                        $expect = 'path';
                    }
                    break;
                case 'path':
                    if(preg_match('/^\s+txt = .*"rs=(\w+?)"/', $line, $matches)) {
                        $path = $matches[1];
                        $pre[] = "PATH: $path\n";

                        $scanners["$name [$ip]"] = "escl:$proto://$ip:$port/$path";

                        $device = '';
                        $expect = 'device';
                    }
                    break;
            }
        }
        if(empty($scanners)) {
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
        <title>ScanTest</title>
        <style>
            * {
                margin: 0;
                padding: 0;
            }
            body {
                width: 100vw;
                height: 100vh;
                padding: 25px;
                font-family: 'Roboto', sans-serif;
                font-size: 1.2em;
                font-style: normal;
            }
            input, select {
                font-size: 1em;
            }
            h2, p {
                margin-bottom: 18px;
            }
            span {
                color: darkgreen;
            }
        </style>
    </head>
    <body>
        <h2>TG-Soft / graphax Linux Scanning Test Environment</h2>
        <p>eSCL capable devices: <select name="scanner">
            <option value="_search" disabled selected>Searching, please wait...</option>
        </select>
        <p>Active scanner: <span id="activeScanner"></span>
        <!-- <pre><?= print_r($scanners) ?></pre> -->
        <script>
            let activeScanner = '';
            $(function() {
                let $scannersel = $('select[name=scanner]');
                $scannersel.on('change', function() {
                    if($scannersel.val() == '_search') {
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
