<?php
include("includes/header.php");
if (isset($_SESSION["user_public_id"]) && getUserByPublicID($pdo, $_SESSION["user_public_id"])) {
    header("Location: /profil.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $user = loginUser($pdo, $_POST);
        $_SESSION['msg']['type'] = "success";
        $_SESSION['msg']['content'] = "Welcome back #<b>" . $user['username'] . "</b>";
        $_SESSION["user_public_id"] = $user['public_id'];
        header('Location: /index.php');
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