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

## Validateur des données du tableau
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

## Sauvegarder les donnée de l'utilisateur dans la BD
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
    if ($pass != $cPass) {
        throw new UserException("Incorrect password! password must be the same!");
    }
    if (getUserByUsername($pdo, $uName)) {
        throw new UserException("Sorry! Username already exist. Choose another one");
    }
    if (getUserByEmail($pdo, $uName)) {
        throw new UserException("Sorry! Email already exist. Choose another one");
    }
    ## Insertion des données
    $prep = $pdo->prepare("INSERT INTO users(public_id, firstname, lastname, email, username, password) VALUES(?,?,?,?,?,?)");
    $prep->execute([sha1(($uName . time() . $email . rand(999, 999999))), $fName, $lName, $email, $uName, password_hash($pass, PASSWORD_DEFAULT)]);
}

function getUserByPublicID(PDO $pdo, $public_ID)
{
}

function getUserByUsername(PDO $pdo, $username)
{
}

function getUserByEmail(PDO $pdo, $email)
{
}

function getAllUsers(PDO $pdo)
{
}

function existUser(PDO $pdo, $user)
{
    userFieldValidator($user);
}

function loginUser(PDO $pdo, $user)
{
    userFieldValidator($user);
}

function updateUser(PDO $pdo, $user, $public_ID)
{
    userFieldValidator($user);
}

function removeUser(PDO $pdo, $public_ID)
{
}