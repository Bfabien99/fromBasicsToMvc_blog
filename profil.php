<?php 
include("includes/header.php");
if(!isset($_SESSION["user_public_id"]) || !getUserByPublicID($pdo, $_SESSION["user_public_id"])){
    header("Location: /index.php");
    exit();
}
var_dump($user);
?>
<section>
    
</section>
<?php 
include("includes/footer.php");
?>