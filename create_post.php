<?php
include("includes/header.php");
if (!isset($_SESSION["user_public_id"])) {
    header("Location: /index.php");
    exit();
}
$categories = getAllCategories($pdo);
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        savePost($pdo, $_SESSION["user_public_id"], $_POST, $_FILES);
        $msg_type = "success";
        $msg_content = "<b>New Post created!</b>";
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
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Login to Blog!</h3>
        <div class="group">
            <label for="formTitle">Post Title</label>
            <input type="text" name="title" id="formTitle">
        </div>
        <div class="group">
            <label for="formContent">Post Content <span id="contLen">0</span></label>
            <textarea name="content" id="formContent" cols="30" rows="10" minlength="200"></textarea>
        </div>
        <div class="group">
            <label for="formCat">Choose Post Category</label>
            <select name="category[]" id="formCat" multiple="true">
                <?php if (!$categories): ?>
                    <option value="">--</option>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>">
                            <?= $category['title'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="group">
            <label for="formFile" class="form-label">Upload Post Cover</label>
            <input class="form-control" type="file" id="formFile" name="upfile">
        </div>
        <div class="group">
            <button type="submit">Login</button>
            <a href="register.php">Join Us!</a>
        </div>
    </form>
</section>
<script>
    let textArea =document.getElementById('formContent');
    let contLen =document.getElementById('contLen');

    textArea.addEventListener('input', function () {
        contLen.innerText = textArea.value.length; 
    })
</script>
<?php
include("includes/footer.php");
?>