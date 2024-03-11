<?php
## Echapper les caractères
function escape($string)
{
    return strip_tags(trim($string));
}

####    USER FUNCTIONS    ####
class UserException extends Exception
{
}
class PostException extends Exception
{
}
class CategoryException extends Exception
{
}
class CommentException extends Exception
{
}

## Validateur des données du tableau user
function userFieldValidator($user): void
{
    ## Les champs requis
    $required_field = ["firstname", "lastname", "email", "username", "password"];
    ## Vérifie que la variable $user est bien un tableau
    if (!is_array($user)) {
        throw new UserException("User data type is not valid! Require an Array," . gettype($user) . " given.");
    }
    ## Vérifie si les champs requis sont dans le tableau $user
    foreach ($required_field as $field) {
        if (!array_key_exists($field, $user) || empty(trim($user[$field]))) {
            throw new UserException("'" . $field . "' field is required!");
        }
    }
    ## Vérifie si les champs requis dans le tableau $user ont une valeur d'au moins 2 caractères
    foreach ($required_field as $field) {
        if (!empty(trim($user[$field])) && strlen(trim($user[$field])) < 2) {
            throw new UserException("'" . $field . "' field must contains at least 2 characteres!");
        }
    }
}

### INSERTION DE USER DANS LA BD
function saveUser(PDO $pdo, $user)
{
    ## Valider les données de $user
    userFieldValidator($user);
    ## Récupération et vérification des valeurs
    $fName = escape($user['firstname']);
    $lName = escape($user['lastname']);
    $email = filter_var(escape($user['email']), FILTER_SANITIZE_EMAIL);
    $uName = escape($user['username']);
    $pass = escape($user['password']);
    $cPass = escape($user['cpassword']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new UserException("Invalid Email: " . $email);
    }
    if (strlen($uName) < 5) {
        throw new UserException("Username must contains at least 5 characteres!");
    }
    if (strlen($pass) < 6) {
        throw new UserException("Password must contains at least 6 characteres!");
    }
    ## Verifie si le 'username' n'existe pas déja!
    if (getUserByUsername($pdo, $uName)) {
        throw new UserException("Sorry! Username already exist. Choose another one");
    }
    ## Verifie si le 'email' n'existe pas déja!
    if (getUserByEmail($pdo, $email)) {
        throw new UserException("Sorry! Email already exist. Choose another one");
    }
    ## Verifie si les mots de passe correspondent
    if ($pass != $cPass) {
        throw new UserException("Incorrect password! password must be the same!");
    }
    ## Insertion des données
    $prep = $pdo->prepare("INSERT INTO users(public_id, firstname, lastname, email, username, password) VALUES(?,?,?,?,?,?)");
    $prep->execute([sha1(($uName . time() . $email . rand(999, 999999))), $fName, $lName, $email, $uName, password_hash($pass, PASSWORD_DEFAULT)]);
}

function getUserByPublicID(PDO $pdo, $public_ID)
{
    ## Récupération de l'user par son public_ID
    $stmt = $pdo->prepare("SELECT public_id, firstname, lastname, email, username, created_at FROM users WHERE public_id = ?");
    $stmt->execute([$public_ID]);
    $user = $stmt->fetch();
    return $user;
}

function getUserByUsername(PDO $pdo, $username)
{
    ## Récupération de l'user par son username
    $stmt = $pdo->prepare("SELECT public_id, firstname, lastname, email, username, created_at FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    return $user;
}

function getUserByEmail(PDO $pdo, $email)
{
    ## Récupération de l'user par son email
    $stmt = $pdo->prepare("SELECT public_id, firstname, lastname, email, username, created_at FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    return $user;
}

function getAllUsers(PDO $pdo)
{
    $stmt = $pdo->query("SELECT public_id, firstname, lastname, email, username, created_at FROM users");
    $users = $stmt->fetchAll();
    return $users;
}

function existUser(PDO $pdo, $user)
{
    userFieldValidator($user);
}

### LOGIN DE USER
function loginUser(PDO $pdo, $user)
{
    ## Validation du tableau $user
    if (!is_array($user) || !array_key_exists("username", $user) || !array_key_exists("password", $user)) {
        throw new UserException("Required field missed!");
    }
    ## Récupération des données
    if (!empty($user["username"]) && !empty($user["password"])) {
        $uName = escape($user["username"]);
        $pass = escape($user["password"]);
    } else {
        throw new UserException("Fill all fields!");
    }
    ## Verifie si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT public_id, username, password FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$uName, $uName]);
    $user = $stmt->fetch();
    if (!empty($user)) {
        ## Verifie si le mot de passe est correct
        if (!password_verify($pass, $user["password"])) {
            throw new UserException("Username or Email or Password is incorrect!");
        }
    } else {
        throw new UserException("Username or Email or Password is incorrect!");
    }
    return ["public_id" => $user['public_id'], "username" => $user['username']];
}

function updateUser(PDO $pdo, $user, $public_ID)
{
    userFieldValidator($user);
}

function removeUser(PDO $pdo, $public_ID)
{
}

####    POSTS FUNCTIONS     ####
function createSlug($text)
{
    // Convertit le texte en minuscules
    $text = strtolower($text);
    // Remplace les caractères spéciaux par des tirets
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    // Supprime les tirets en double
    $text = preg_replace('/-{2,}/', '-', $text);
    // Supprime les tirets au début et à la fin
    $text = trim($text, '-');
    return $text;
}

## Validateur des données du tableau post
function postFieldValidator($post): void
{
    ## Les champs requis
    $required_field = ["title", "content", "category"];
    ## Vérifie que la variable $post est bien un tableau
    if (!is_array($post)) {
        throw new PostException("Post data type is not valid! Require an Array," . gettype($post) . " given.");
    }
    ## Vérifie si les champs requis sont dans le tableau $post
    foreach ($required_field as $field) {
        if ($field != "category" && (!array_key_exists($field, $post) || empty(trim($post[$field])))) {
            throw new PostException("'" . $field . "' field is required!");
        }
    }
    ## Vérifie si les champs requis dans le tableau $post ont une valeur d'au moins 2 caractères
    foreach ($required_field as $field) {
        if ($field != "category" && !empty(trim($post[$field])) && strlen(trim($post[$field])) < 2) {
            throw new PostException("'" . $field . "' field must contains at least 2 characteres!");
        }
    }
}

function savePicture($file)
{
    if (($file == null) || !is_array($file) || $file['upfile']['name'] == "") {
        return null;
    }

    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($file['upfile']['error']) ||
        is_array($file['upfile']['error'])
    ) {
        throw new RuntimeException('Upload a file.');
    }

    // Check $_FILES['upfile']['error'] value.
    switch ($file['upfile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // You should also check filesize here. 
    if ($file['upfile']['size'] > 1000000) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (
        false === $ext = array_search(
            $finfo->file($file['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),
            true
        )
    ) {
        throw new RuntimeException('Invalid file format.');
    }

    // You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    $unique_file_name = sha1_file($file['upfile']['tmp_name']);
    if (
        !move_uploaded_file(
            $_FILES['upfile']['tmp_name'],
            sprintf(
                'uploads/%s.%s',
                $unique_file_name,
                $ext
            )
        )
    ) {
        throw new RuntimeException('Failed to move uploaded file.');
    }
    return $unique_file_name;

}

function savePost(PDO $pdo, $public_id, $post, $file = null)
{
    postFieldValidator($post);
    if (!getUserByPublicID($pdo, $public_id)) {
        throw new UserException('Sorry, you are not granted to make a Post!');
    }

    $title = strtolower(escape($post['title']));
    $content = escape($post['content']);
    $category = $post['category'];

    if (strlen($title) < 10 || strlen($title) > 100) {
        throw new PostException('Post title length is not in the accorded range, between 10 - 100 characteres');
    }
    if (strlen($content) < 200 || strlen($content) > 1000) {
        throw new PostException('Post content length is not in the accorded range, between 200 - 1000 characteres');
    }
    if (isPostTitleExist($pdo, $title)) {
        throw new PostException('Post title already exist!');
    }
    foreach ($category as $cat) {
        if (!getCategoryByID($pdo, $cat)) {
            throw new CategoryException('Sorry, category not found!');
        }
    }

    $filename = savePicture($file);
    $slug = createSlug($title);

    $stmt = $pdo->prepare('INSERT INTO posts(slug, title, content, user_public_id, cover_pic) VALUES(?,?,?,?,?)');
    $stmt->execute([$slug, $title, $content, $public_id, $filename]);
    $id = $pdo->lastInsertId();
    foreach ($category as $cat) {
        linkPostToCategory($pdo, $id, $cat);
    }
}

function updatePost(PDO $pdo, $public_id, $post_id, $post)
{

}

function getAllPost(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM posts");
    $posts = $stmt->fetchAll();
    return $posts;
}

function getAllPostByUserPublicID(PDO $pdo, $public_id)
{

}

function getPostByID(PDO $pdo, $post_id)
{
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    return $post;
}

function getOnePostByUserPublicID(PDO $pdo, $public_id, $post_id)
{

}

function getPostBySlug(PDO $pdo, $slug)
{
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
    return $post;
}

function isPostTitleExist(PDO $pdo, $title)
{
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE title = ?");
    $stmt->execute([strtolower($title)]);
    $post = $stmt->fetch();
    return $post;
}

function deletePost(PDO $pdo, $public_id, $post_id)
{

}

####    CATEGORIES FUNCTIONS    ####
function categoryFieldValidator($category): void
{
    ## Les champs requis
    $required_field = ["title"];
    ## Vérifie que la variable $category est bien un tableau
    if (!is_array($category)) {
        throw new CategoryException("category data type is not valid! Require an Array," . gettype($category) . " given.");
    }
    ## Vérifie si les champs requis sont dans le tableau $category
    foreach ($required_field as $field) {
        if (!array_key_exists($field, $category) || empty(trim($category[$field]))) {
            throw new CategoryException("'" . $field . "' field is required!");
        }
    }
    ## Vérifie si les champs requis dans le tableau $post ont une valeur d'au moins 2 caractères
    foreach ($required_field as $field) {
        if (!empty(trim($category[$field])) && strlen(trim($category[$field])) < 2) {
            throw new CategoryException("'" . $field . "' field must contains at least 2 characteres!");
        }
    }
}

function savecategory(PDO $pdo, $category)
{
    categoryFieldValidator($category);
    $title = strtolower(escape($category["title"]));

    if (getCategoryByTitle($pdo, $title)) {
        throw new CategoryException("Category already exist!");
    }
    $stmt = $pdo->prepare("INSERT INTO categories(title) VALUES(?)");
    $stmt->execute([$title]);
}

function updatecategory(PDO $pdo, $category)
{

}

function deleteCategory(PDO $pdo, $category_id)
{

}

function getCategoryByID(PDO $pdo, $category_id)
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    return $category;
}

function getCategoryByTitle(PDO $pdo, $title)
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE title = ?");
    $stmt->execute([$title]);
    $category = $stmt->fetch();
    return $category;
}

function getAllCategories(PDO $pdo)
{
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
    return $categories;
}

function linkPostToCategory(PDO $pdo, $post_id, $category_id)
{
    if (!getPostByID($pdo, $post_id)) {
        throw new PostException("Post not found!");
    }
    if (!getCategoryByID($pdo, $category_id)) {
        throw new CategoryException("Category not found");
    }

    $stmt = $pdo->prepare('INSERT INTO post_cat(post_id, cat_id) VALUES(?,?)');
    $stmt->execute([$post_id, $category_id]);
}
####    COMMENTS FUNCTIONS      ####