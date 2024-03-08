<?php 
include("includes/header.php");
?>
<section>
    <form action="" method="post">
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