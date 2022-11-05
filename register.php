<?php
    require("connect.php");

    $errorFlag = false;
    $fieldsToValidate = ['username' => ['isEmpty' => false, 'name' => 'Username'],
                         'email' => ['isEmpty' => false, 'name' => 'Email'],
                         'password' => ['isEmpty' => false, 'name' => 'Password'],
                         'passwordConfirm' => ['isEmpty' => false, 'name' => 'Password Confirmation'], 
                        ];

    if(isset($_POST['button'])) {
        foreach($fieldsToValidate as $field => $info) {
            if(!isset($_POST[$field]) || (empty(trim($_POST[$field]))))
            {
                $errorFlag = true;
                $fieldsToValidate[$field]['isEmpty'] = true;
            }
        }
        if(!$errorFlag) {
            $additionalErrors = SubmitToDatabase($db);
        }
    }

    function SubmitToDatabase($db) {
        $errors = [];

        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $passwordConfirm = filter_input(INPUT_POST, "passwordConfirm", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

        $userAvailableQuery = "SELECT username FROM Users";
        $userAvailableStatement = $db->prepare($userAvailableQuery);
        $userAvailableStatement->execute();

        while($usernameDB = $userAvailableStatement->fetch()) {
            if($username == $usernameDB['username']) {
                $errors['username'] = "Username has already been taken.";
            }
        }
        if(!$email) {
            $errors['email'] = "The provided email was invalid";
        }
        if($password !== $passwordConfirm) {
            $errors['password'] = "Passwords do not match";
        }

        if(!empty($errors)) {
            return $errors;
        }

        $query = "INSERT INTO Users (username, password, role_id, email) VALUES (:username, :password, 1, :email)";
        $statement = $db->prepare($query);

        $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);

        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':email', $email);
        $statement->execute();

        header('Location: login.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <?php require("header.php") ?>

    <form action="register.php" method="post">
        <?php if($errorFlag): ?>
            <p>The following fields cannot be empty:</p>
            <ul>
                <?php foreach($fieldsToValidate as $field => $info): ?>
                    <?php if($info['isEmpty']): ?>
                        <li><?= $field ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        <fieldset>
            <p>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <?php if(isset($additionalErrors['username'])): ?>
                    <p><?= $additionalErrors['username'] ?></p>
                <?php endif ?>
            </p>

            <p>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required> 
                <?php if(isset($additionalErrors['email'])): ?>
                    <p><?= $additionalErrors['email'] ?></p>
                <?php endif ?>
            </p>

            <p>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </p>

            <p>
                <label for="passwordConfirm">Re-enter Password</label>
                <input id="passwordConfirm" name="passwordConfirm" type="password" required>
                <?php if(isset($additionalErrors['password'])): ?>
                    <p><?= $additionalErrors['password'] ?></p>
                <?php endif ?>
            </p>

            <p>
                <button type="submit" value="register" name="button">Register</button>
            </p>
        </fieldset>
    </form>
</body>
</html>