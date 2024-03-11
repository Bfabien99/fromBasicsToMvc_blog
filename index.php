<?php 
include("includes/header.php");
?>
<section>
<?php if(!empty($msg_type)):?>
    <div class="msgBox">
        <hr>
        <p class="<?= $msg_type; ?>"><?= $msg_content; ?></p>
        <hr>
    </div>
<?php endif;?>
</section>
<?php 
include("includes/footer.php");
?>