<?php
include("includes/header.php");
if (!isset($_SESSION["user_public_id"])) {
    header("Location: /index.php");
    exit();
}
$posts = getAllPostByUserPublicID($pdo, $_SESSION["user_public_id"]);
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
    <?php if (empty($posts)): ?>
        <div class="empty">
            <h4>No post found</h4>
        </div>
    <?php else: ?>
        <div class="postBox">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <div class="imgContainer" style="width:300px; height:300px">
                        <img src="uploads/<?= $post['cover_pic'] ?>" alt="<?= $post['slug'] ?>" style="width:100%;height:100%">
                    </div>
                    <div class="content">
                        <h3 class="postTitle">
                            <?= ucwords($post['title']) ?>
                        </h3>
                        <div class="postContent">
                            <p>
                                <?= substr($post['content'], 0, 60) ?>(...)
                            </p>
                            <div class="catDat">
                                <?php foreach (getCategoryByPostID($pdo, $post['id']) as $cat): ?>
                                    <em>#
                                        <?= $cat['title'] ?>
                                    </em>
                                <?php endforeach; ?>
                                <p>
                                    <?= $post['created_at'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <a href="user_show_post.php?slug=<?= $post['slug'] ?>" class="btn btnMore">show more</a>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif; ?>
</section>
<?php
include("includes/footer.php");
?>