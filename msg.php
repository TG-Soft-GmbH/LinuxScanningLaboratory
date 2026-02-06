<?php if (isset($_GET['msg'])) { ?>
    <div class="info">
        <span style="float:right;"><a href="#" onClick="$(this).parent().parent().remove(); return false;">Close Message</a></span>
        <?= $_GET['msg'] ?>
    </div>
    <script>
        history.replaceState({}, '', '<?= $self ?>');
    </script>
<?php } ?>