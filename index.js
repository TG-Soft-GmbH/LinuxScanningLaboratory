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

    let $scannersel = $('select[name=scanner]');
    $scannersel.on('change', function () {
        if ($scannersel.val() == '_search') {
            $('#actionzone').hide();
            $('#activeScanner').text('');
            $scannersel.find('[value="_search"]').text('Searching for more scanners... Please wait...').prop('disabled', true);
            $scannersel.load('?escl_discover');
        } else {
            $scannersel.find('[disabled]').remove();
            $('#activeScanner').text($scannersel.val());
            $('#actionzone').show();
        }
    });
    $scannersel.load('?escl_discover');

    $('#actionzone button').on('click', function () {
        $('#actionzone button').prop('disabled', true);
        $(this).html($(this).html() + '<spinner />');

        let eid = new Date().toISOString().replaceAll('T', '_').replaceAll(':', '-').replaceAll('Z', '');
        let $exhibit = $('[template].exhibit').clone().removeAttr('template').insertAfter('[template].exhibit').hide().slideDown();
        $exhibit.find('.eid').text(eid);
        $exhibit.find('.scanner').text($('#activeScanner').text() + ' (' + $scannersel.find('option:selected').text() + ')');
        $exhibit.find('.cmd').html($(this).text() + '<spinner />');
        const $terminal = $exhibit.find('.terminal');
        const $pdf = $exhibit.find('.pdf');
        $terminal.attr('src', `?cmd=${$(this).attr('cmd')}&out=${eid}/log.txt&dev=${ encodeURIComponent($exhibit.find('.scanner').text()) }`);
        $.ajax({
            url: `?out=${eid}/capabilities.xml&url=` + $('#activeScanner').text().replaceAll('escl:', '') + '/ScannerCapabilities',
            dataType: 'text',
            success: function (data) {
                $exhibit.find('details spinner').remove();
                const xml = formatXml(data);
                if (xml.includes('</parsererror>')) {
                    $exhibit.find('summary').append(' INVALID.');
                    if (!data) data = 'NO DATA.'
                    $exhibit.find('pre').text(data);
                } else {
                    $exhibit.find('summary').append(' Ready.');
                    $exhibit.find('pre').text(xml);
                }
            }
        });
        function resizeTerminal() {
            $terminal.stop().animate({ height: $terminal[0].contentWindow.document.body.scrollHeight }, 100);
        }
        const interval = setInterval(resizeTerminal, 50);
        window.addEventListener("message", function (e) {
            if (e.data === "execstream_done") {
                setTimeout(function () {
                    clearInterval(interval);
                    resizeTerminal();
                    $('spinner').remove();
                    $('#actionzone button').prop('disabled', false);
                }, 350);
            }
        });
        loadPDF($exhibit.find('.pdf')[0], 'DoubleBorderSheet-A4-v1.0.pdf');
        //loadPDF($exhibit.find('.pdf')[0], 'BorderSheet-A4-v1.0.pdf');
        //loadPDF($exhibit.find('.pdf')[0], 'inktester.pdf');
        //loadPDF($exhibit.find('.pdf')[0], 'pdfjs/web/compressed.tracemonkey-pldi-09.pdf');
    });
});
