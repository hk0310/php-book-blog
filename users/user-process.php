<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    if(!isset($_POST['command'])) {
        header("Location: /Project");
        exit();
    }

    // Variables for keeping track of errors
    $errorFlag = false;
    $errors = [
        'username' => ["isEmpty" => false, "otherError" => ""],
        'email' => ["isEmpty" => false, "otherError" => ""],
        'password' => ["isEmpty" => false, "otherError" => ""],
        'passwordConfirm' => ["isEmpty" => false, "otherError" => ""],
    ];

    // Checks for empty inputs.
    foreach($errors as $field => $error) {
        if(!isset($_POST[$field]) || (empty(trim($_POST[$field]))))
        {
            $errors[$field]['isEmpty'] = true;
            $hasEmpty = true;
        }
    }

    // Validates the submitted user email.
    if(!$errors['email']['isEmpty']) {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if(!$email) {
            $errors['email']['otherError'] = "The provided email is invalid.";
        }
    }

    $command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($command == 'create') {
        foreach($errors as $field => $info) {
            if($info['isEmpty']) {
                $errorFlag = true;
            }
        }

        // Captures user inputs.
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $passwordConfirm = filter_input(INPUT_POST, "passwordConfirm", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Validates the availability of the chosen username.
        $userAvailableQuery = "SELECT username FROM Users";
        $userAvailableStatement = $db->prepare($userAvailableQuery);
        $userAvailableStatement->execute();

        while($usernameDB = $userAvailableStatement->fetch()) {
            if($username == $usernameDB['username']) {
                $errors['username']['otherError'] = "Username has already been taken.";
            }
        }

        // Validates that the two provided passwords match.
        if($password != $passwordConfirm) {
            $errors['passwordConfirm']['otherError'] = "The passwords do not match.";
        }

        foreach($errors as $field => $info) {
            if(!empty($info['otherError'])) {
                $errorFlag = true;
            }
        }

        // Add the user to the database if the input is error free.
        if(!$errorFlag) {
            $createQuery = "INSERT INTO Users (username, password, role_id, email) VALUES (:username, :password, 1, :email)";
            $createStatement = $db->prepare($createQuery);

            $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);

            $createStatement->bindValue(':username', $username);
            $createStatement->bindValue(':password', $password);
            $createStatement->bindValue(':email', $email);
            $createStatement->execute();

            if(isset($_SESSION['username']) && $_SESSION['role_id'] === 3) {
                header("Location: users.php");
                exit();
            }
            
            header("Location: /Project/auth/login.php");
            exit();
        }
    }
    elseif($command == "update") {
        // If the user is not an owner, they are not allowed to update user information.
        if($_SESSION['role_id'] != 3) {
            header("Location: /Project");
            exit();
        }

        foreach($errors as $field => $info) {
            if($info['isEmpty'] && ($field != 'password' && $field != 'passwordConfirm')) {
                $errorFlag = true;
            }
        }

        // Validates the provided user_id
        if(!isset($_POST['id'])) {
            $errorFlag = true;
        }
        else {
            if(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

                // Validates the availability of the updated username. Checks against usernames in the database except for the account current username.
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

        // Validates the role_id.
        if(!isset($_POST['role'])) {
            $errorFlag = true;
            $errors['role']['isEmpty'] = true;
        }
        elseif(filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 3]])) {
            $role_id = filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 3]]);
        }
        else {
            $errors['role']['otherError'] = "The role chosen is invalid";
        }

        foreach($errors as $field => $info) {
            if(!empty($info['otherError'])) {
                $errorFlag = true;
            }
        }

        if(!$errorFlag) {
            // If both password and passwordConfirm fields are empty then updates the user information with the provided information
            // excluding the password.
            if($errors['password']['isEmpty'] && $errors['passwordConfirm']['isEmpty']) {
                $updateQueryNoPass = "UPDATE Users SET username = :username, email = :email, role_id = :role WHERE user_id = :id";
                $updateStatementNoPass = $db->prepare($updateQueryNoPass);

                $updateStatementNoPass->bindValue(':role', $role_id);
                $updateStatementNoPass->bindValue(':username', $username);
                $updateStatementNoPass->bindValue(':email', $email);
                $updateStatementNoPass->bindValue(':id', $id);
                $updateStatementNoPass->execute();

                header("Location: users.php");
                exit();
            }
            // If both password and passwordConfirm fields have inputs then updates the user information with the provided information 
            // including the password.
            elseif(!$errors['password']['isEmpty'] && !$errors['passwordConfirm']['isEmpty']) {
                $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $passwordConfirm = filter_input(INPUT_POST, "passwordConfirm", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                // Validates that both passwords match.
                if($password != $passwordConfirm) {
                    $errors['passwordConfirm']['otherError'] = "The passwords do not match.";
                    $errorFlag = true;
                }

                if(!$errorFlag) {
                    $updateQueryPass = "UPDATE Users SET username = :username, role_id = :role, email = :email, password = :password WHERE user_id = :id";
                    $updateStatementPass = $db->prepare($updateQueryPass);

                    $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);

                    $updateStatementPass->bindValue(':role', $role_id);
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
            }
        }
    }
    elseif($command == "delete") {
        // If the user is not an owner, they are not allowed to delete users.
        if($_SESSION['role_id'] != 3) {
            header("Location: /Project");
            exit();
        }

        // Validates the POSTed user_id.
        if(!isset($_POST['id'])) {
            $errorFlag = true;
        }
        elseif(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        }
        else {
            $errorFlag = true;
        }

        // If the user_id is valid, then deletes the user.
        if(!$errorFlag) {
            $deleteQuery = "DELETE FROM Users WHERE user_id = :user_id LIMIT 1";
            $deleteStatement = $db->prepare($deleteQuery);
            $deleteStatement->bindValue(":user_id", $id);
            $deleteStatement->execute();

            header("Location: users.php");
            exit();
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
    <?php include(HEADER_PATH) ?>

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
    
    <?php elseif($command == 'update'): ?>
        <h2>Error deleting the user. Please try again.</h2>
    <?php endif ?>
</body>
</html>