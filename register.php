<?php 
include("includes/header.php");
if($_SERVER['REQUEST_METHOD'] === "POST"){
    try {
        saveUser($pdo, $_POST);
    }
    catch (Exception $th) {
        $msg_type = "danger";
        $msg_content = $th->getMessage();
        $_SESSION['msg'] = false;
    }
}
?>
<section>
    <form action="" method="post">
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