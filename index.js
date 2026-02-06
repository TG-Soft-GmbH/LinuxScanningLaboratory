let activeScanner = '';
$(function () {

    let interval = null;
    window.addEventListener("message", function (e) {
        if (e.data === "execstream_done") {
            $("#execresult").text($("#execiframe").contents().find("pre").text());
            if (interval) clearInterval(interval);
        }
    });
    interval = setInterval(function () {
        $("#execresult").text($("#execiframe").contents().find("pre").text());
    }, 250);
    
    $.getJSON(`release.json?${ts}`, function (localdata) {
        if (!localdata) return;
        $('.version').text('Version ' + localdata.version);
        $.getJSON(`https://raw.githubusercontent.com/TG-Soft-GmbH/LinuxScanningLaboratory/refs/heads/main/release.json?${ts}`, function (remotedata) {
            if (!remotedata) return;
            if (localdata.version != remotedata.version) {
                $('.upgrade').html(` - <a href="?upgrade" style="color:darkorange;">Upgrade to Version ${remotedata.version}</a>`);
            }
        });
    });
    let $scannersel = $('select[name=scanner]');
    $scannersel.on('change', function () {
        if ($scannersel.val() == '_search') {
            $scannersel.load('?escl_discover');
            $('#activeScanner').text('');
        } else {
            $('#activeScanner').text($scannersel.val());
        }
    });
    $scannersel.load('?escl_discover');
});
