<?php if (isset($_GET['msg'])) { ?>
    <div class="info">
        <span style="float:right;"><a href="#" onClick="$(this).closest('div').slideUp(500, function() { $(this).remove(); }); return false;">Close Message</a></span>
        <?= $_GET['msg'] ?>
    </div>
    <script>
        history.replaceState({}, '', '<?= $self ?>');
    </script>
<?php } ?>