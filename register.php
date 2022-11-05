<?php
    require("connect.php");

    $errorFlag = false;
    $userDuplicate = "";
    $fieldsToValidate = ['username' => ['hasError' => false,],
                         'email' => ['hasError' => false],
                         'password' => ['hasError' => false],
                         'birthdate' => ['hasError' => false]
                        ];

    if(isset($_POST['button'])) {
        foreach($fieldsToValidate as $field => $info) {
            if(!isset($_POST[$field]) || (empty(trim($_POST[$field]))))
            {
                $errorFlag = true;
                $fieldsToValidate[$field]['hasError'] = true;
            }
        }
        if(!$errorFlag) {
            $userDuplicate = SubmitToDatabase($db);
        }
    }

    function SubmitToDatabase($db) {
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
        $birthdate = filter_input(INPUT_POST, "birthdate", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

        $testDate = date_create($birthdate);

        if(!$testDate || !$email) {
            return;
        }
        
        try {
            $query = "INSERT INTO Users (username, password, role, email, birthdate) VALUES (:username, :password, 0, :email, :birthdate)";
            $statement = $db->prepare($query);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $password);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':birthdate', $birthdate);
            $statement->execute();
        }
        catch(Exception $e) {
            return "Username has already been taken.";
        }


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
            <p>The following fields were invalid:</p>
            <ul>
                <?php foreach($fieldsToValidate as $field => $info): ?>
                    <?php if($info['hasError']): ?>
                        <li><?= $field ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        <fieldset>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <?php if(!empty($userDuplicate)): ?>
                <p><?= $userDuplicate ?></p>
            <?php endif ?>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" required> 

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <label for="birthdate">Date of Birth</label>
            <input id="birthdate" name="birthdate" type="date" required>

            <p>
                <button type="submit" value="register" name="button">Register</button>
            </p>
        </fieldset>
    </form>
</body>
</html>