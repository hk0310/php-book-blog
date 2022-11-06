<?php
    require("misc" . DIRECTORY_SEPARATOR . "connect.php");
    session_start();

    $errorFlag = false;
    $errors = [
        'username' => ["isEmpty" => false, "otherError" => ""],
        'email' => ["isEmpty" => false, "otherError" => ""],
        'password' => ["isEmpty" => false, "otherError" => ""],
        'passwordConfirm' => ["isEmpty" => false, "otherError" => ""],
    ];

    foreach($errors as $field => $error) {
        if(!isset($_POST[$field]) || (empty(trim($_POST[$field]))))
        {
            $errors[$field]['isEmpty'] = true;
            $hasEmpty = true;
        }
    }

    if(!$errors['email']['isEmpty']) {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if(!$email) {
            $errors['email']['otherError'] = "The provided email is invalid.";
        }
    }


    if(!isset($_POST['command'])) {
        header("Location: index.php");
        exit();
    }

    $command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($command == 'create') {
        foreach($errors as $field => $info) {
            if($info['isEmpty']) {
                $errorFlag = true;
            }
        }

        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $passwordConfirm = filter_input(INPUT_POST, "passwordConfirm", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $userAvailableQuery = "SELECT username FROM Users";
        $userAvailableStatement = $db->prepare($userAvailableQuery);
        $userAvailableStatement->execute();

        while($usernameDB = $userAvailableStatement->fetch()) {
            if($username == $usernameDB['username']) {
                $errors['username']['otherError'] = "Username has already been taken.";
            }
        }

        if($password != $passwordConfirm) {
            $errors['passwordConfirm']['otherError'] = "The passwords do not match.";
        }

        foreach($errors as $field => $info) {
            if(!empty($info['otherError'])) {
                $errorFlag = true;
            }
        }

        if(!$errorFlag) {
            $createQuery = "INSERT INTO Users (username, password, role_id, email) VALUES (:username, :password, 1, :email)";
            $createStatement = $db->prepare($createQuery);

            $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);

            $createStatement->bindValue(':username', $username);
            $createStatement->bindValue(':password', $password);
            $createStatement->bindValue(':email', $email);
            $createStatement->execute();

            if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 3) {
                header("Location: users.php");
                exit();
            }
            
            header("Location: login.php");
            exit();
        }
    }
    elseif($command == "update") {
        foreach($errors as $field => $info) {
            if($info['isEmpty'] && ($field != 'password' && $field != 'passwordConfirm')) {
                $errorFlag = true;
            }
        }

        if(!isset($_POST['id'])) {
            $errorFlag = true;
        }
        else {
            if(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

                $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
                $userAvailableQuery = "SELECT username FROM Users where user_id <> :id";
                $userAvailableStatement = $db->prepare($userAvailableQuery);
                $userAvailableStatement->bindValue(':id', $id);
                $userAvailableStatement->execute();
        
                while($usernameDB = $userAvailableStatement->fetch()) {
                    if($username == $usernameDB['username']) {
                        $errors['username']['otherError'] = "Username has already been taken.";
                    }
                }
            }
            else {
                $errorFlag = true;
            }
        }

        foreach($errors as $field => $info) {
            if(!empty($info['otherError'])) {
                $errorFlag = true;
            }
        }

        if(!$errorFlag) {
            if($errors['password']['isEmpty'] && $errors['passwordConfirm']['isEmpty']) {
                $updateQueryNoPass = "UPDATE Users SET username = :username, email = :email WHERE user_id = :id";
                $updateStatementNoPass = $db->prepare($updateQueryNoPass);

                $updateStatementNoPass->bindValue(':username', $username);
                $updateStatementNoPass->bindValue(':email', $email);
                $updateStatementNoPass->bindValue(':id', $id);
                $updateStatementNoPass->execute();

                header("Location: users.php");
                exit();
            }
            elseif(!$errors['password']['isEmpty'] && !$errors['passwordConfirm']['isEmpty']) {
                $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $passwordConfirm = filter_input(INPUT_POST, "passwordConfirm", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                if($password != $passwordConfirm) {
                    $errors['passwordConfirm']['otherError'] = "The passwords do not match.";
                    $errorFlag = true;
                }

                if(!$errorFlag) {
                    $updateQueryPass = "UPDATE Users SET username = :username, email = :email, password = :password WHERE user_id = :id";
                    $updateStatementPass = $db->prepare($updateQueryPass);

                    $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);

                    $updateStatementPass->bindValue(':username', $username);
                    $updateStatementPass->bindValue(':email', $email);
                    $updateStatementPass->bindValue(':password', $password);
                    $updateStatementPass->bindValue(':id', $id);
                    $updateStatementPass->execute();

                    header("Location: users.php");
                    exit();
                }
            }
            else {
                $errors['passwordConfirm']['otherError'] = "The passwords do not match.";
                echo("asbd");
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body>
    <?php include("misc" . DIRECTORY_SEPARATOR . "header.php") ?>
    <?php if($command == 'create'): ?>
        <h2>Error creating account. Please fix the following errors and try again.</h2>

        <?php if(isset($hasEmpty)): ?>
            <p>The following fields cannot be empty:</p>
            <ul>
                <?php foreach($errors as $field => $info): ?>
                    <?php if($info['isEmpty']): ?>
                        <li><?= $field ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        
        <p>Errors:</p>
        <ul>
            <?php foreach($errors as $field => $info): ?>
                <?php if(!empty($info['otherError'])): ?>
                    <li><?= $info['otherError'] ?></li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>

    <?php elseif($command == 'update'): ?>
        <h2>Error updating user information. Please fix the following errors and try again.</h2>

        <?php if(isset($hasEmpty)): ?>
            <p>The following fields cannot be empty:</p>
            <ul>
                <?php foreach($errors as $field => $info): ?>
                    <?php if($info['isEmpty'] && ($field != 'password' || $field != 'passwordConfirm')): ?>
                        <li><?= $field ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        
        <p>Errors:</p>
        <ul>
            <?php foreach($errors as $field => $info): ?>
                <?php if(!empty($info['otherError'])): ?>
                    <li><?= $info['otherError'] ?></li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>

    <?php endif ?>
</body>
</html>