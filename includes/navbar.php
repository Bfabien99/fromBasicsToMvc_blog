<header>
    <nav>
        <ul>
            <li><a href="index.php">home</a></li>
            <li><a href="category.php">Category</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>

        <?php if(!empty($user)): ?>
            <!-- CONNECTED -->
            <ul>
                <li><a href="my_posts.php">My Posts</a></li>
                <li><a href="create_post.php">create post</a></li>
                <li><a href="profil.php">profile</a></li>
                <li><a href="logout.php">logout</a></li>
            </ul>
            <!-- END CONNECTED -->
        <?php else: ?>
            <ul>
                <li><a href="login.php">login</a></li>
                <li><a href="register.php">register</a></li>
            </ul>
        <?php endif ?>
    </nav>
</header>