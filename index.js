let activeScanner = '';
$(function () {
    function manageVersion(localdata) {
        $('.version').text('Version ' + localdata.version);
        $.getJSON(`https://raw.githubusercontent.com/TG-Soft-GmbH/LinuxScanningLaboratory/refs/heads/main/release.json?${ts}`, function (remotedata) {
            if (localdata.version != remotedata.version) {
                $('.upgrade').html(` - <a href="?upgrade">Upgrade to Version ${remotedata.version}</a>`);
                $('.upgrade').find('a').on('click', function () {
                    $('.upgradeoverlay').show();
                    setTimeout(() => location.reload(true), 45000);
                });
            }
        });
    }
    $.getJSON(`release.json?${ts}`).done(manageVersion).fail(manageVersion);

    let interval = null;
    window.addEventListener("message", function (e) {
        if (e.data === "execstream_done") {
            if (interval) clearInterval(interval);
            $("#execresult").text($("#execiframe").contents().find("pre").text());
        }
    });
    interval = setInterval(function () {
        $("#execresult").text($("#execiframe").contents().find("pre").text());
    }, 250);

    let $scannersel = $('select[name=scanner]');
    $scannersel.on('change', function () {
        if ($scannersel.val() == '_search') {
            $('#actionzone').hide();
            $scannersel.load('?escl_discover');
            $('#activeScanner').text('');
        } else {
            $('#activeScanner').text($scannersel.val());
            $('#actionzone').show();
        }
    });
    $scannersel.load('?escl_discover');
});
