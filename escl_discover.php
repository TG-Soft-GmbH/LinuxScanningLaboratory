<?php
if (isset($_GET['escl_discover'])) {
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
