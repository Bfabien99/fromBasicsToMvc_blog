<?php 
include("includes/header.php");
if(!isset($_SESSION["user_public_id"])){
    header("Location: /index.php");
    exit();
}
?>
<section>
    
</section>
<?php 
include("includes/footer.php");
?>