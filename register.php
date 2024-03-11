<?php
include("includes/header.php");
if (isset($_SESSION["user_public_id"]) && getUserByPublicID($pdo, $_SESSION["user_public_id"])) {
    header("Location: /profil.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        saveUser($pdo, $_POST);
        $_SESSION['msg']['type'] = "success";
        $_SESSION['msg']['content'] = "Saved successfully, let's login now!";
        header('Location: /login.php');
        exit();
    } catch (Exception $th) {
        $msg_type = "error";
        $msg_content = $th->getMessage();
        $_SESSION['msg'] = false;
    }
}
?>
<section>
    <?php if (!empty($msg_type)): ?>
        <div class="msgBox">
            <hr>
            <p class="<?= $msg_type; ?>">
                <?= $msg_content; ?>
            </p>
            <hr>
        </div>
    <?php endif; ?>
    <form action="" method="post">
        <h3>Register to Blog!</h3>
        <div class="group">
            <label for="formFirstname">Firstname</label>
            <input type="text" name="firstname" id="formFirstname">
        </div>
        <div class="group">
            <label for="formLastname">Lastname</label>
            <input type="text" name="lastname" id="formLastname">
        </div>
        <div class="group">
            <label for="formEmail">Email</label>
            <input type="email" name="email" id="formEmail">
        </div>
        <div class="group">
            <label for="formUsername">Username</label>
            <input type="text" name="username" id="formUsername">
        </div>
        <div class="group">
            <label for="formPassword">Password</label>
            <input type="password" name="password" id="formPassword">
        </div>
        <div class="group">
            <label for="formCPassword">Confirm Password</label>
            <input type="text" name="cpassword" id="formCPassword">
        </div>
        <div class="group">
            <button type="submit">SignUp</button>
            <a href="login.php">Connect!</a>
        </div>
    </form>
</section>
<?php
include("includes/footer.php");
?>