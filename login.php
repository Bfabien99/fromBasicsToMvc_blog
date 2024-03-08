<?php 
include("includes/header.php");
if($_SERVER['REQUEST_METHOD'] === "POST"){
    try {
        $user = loginUser($pdo, $_POST);
        $_SESSION['msg']['type'] = "success";
        $_SESSION['msg']['content'] = "Welcome back #<b>".$user['username']."</b>";
        $_SESSION["user_public_id"] = $user['public_id'];
        header('Location: /index.php');
        exit();
    }
    catch (Exception $th) {
        $msg_type = "error";
        $msg_content = $th->getMessage();
        $_SESSION['msg'] = false;
    }
}
?>
<section>
    <form action="" method="post">
        <?php if(!empty($msg_type)):?>
            <p class="<?= $msg_type; ?>"><?= $msg_content; ?></p>
        <?php endif;?>
        <h3>Login to Blog!</h3>
        <div class="group">
            <label for="formUsername">Username or Email</label>
            <input type="text" name="username" id="formUsername">
        </div>
        <div class="group">
            <label for="formPassword">Password</label>
            <input type="text" name="password" id="formPassword">
        </div>
        <div class="group">
            <button type="submit">Login</button>
            <a href="register.php">Join Us!</a>
        </div>
    </form>
</section>
<?php 
include("includes/footer.php");
?>