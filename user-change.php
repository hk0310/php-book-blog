<?php 
    require("misc" . DIRECTORY_SEPARATOR . "connect.php");
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit();
    }

    if (!$_SESSION['user']['role_id'] === 3) {
        header("Location: index.php");
        exit();
    } 

    if(isset($_POST['button'])) {
        $errors = [
            'username' => ["isEmpty" => false, "otherErrors" => ""],
            'email' => ["isEmpty" => false, "otherErrors" => ""],
            'password' => ["isEmpty" => false, "otherErrors" => ""],
            'passwordConfirm' => ["isEmpty" => false, "otherErrors" => ""],
        ];
        
        $errorFlag = false;

        foreach($errors as $field => $error) {
            if(!isset($_POST[$field]) || (empty(trim($_POST[$field]))))
            {
                $errorFlag = true;
                $errors[$field]['isEmpty'] = true;
            }
        }

        if(!$errorFlag) {
            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $passwordConfirm = filter_input(INPUT_POST, "passwordConfirm", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

            if(!$email) {
                $errors['email']['otherErrors'] = "The provide email is invalid.";
            }

            if($password !== $passwordConfirm) {
                $errors['passwordConfirm']['otherErrors'] = "The passwords do not match.";
            }

            $userAvailableQuery = "SELECT username FROM Users";
            $userAvailableStatement = $db->prepare($userAvailableQuery);
            $userAvailableStatement->execute();

            while($usernameDB = $userAvailableStatement->fetch()) {
                if($username == $usernameDB['username']) {
                    $errors['username'] = "Username has already been taken.";
                }
            }
        }
    }
    
    if(isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if(!$id) {
            header("Location: users.php");
            exit();
        }

        $selectOneQuery = "SELECT * FROM Users u JOIN Roles r ON r.role_id = u.role_id WHERE u.user_id = :id ORDER BY u.role_id DESC, u.username";
        $selectOneStatement = $db->prepare($selectOneQuery);
        $selectOneStatement->bindValue(':id', $id);
        $selectOneStatement->execute();
        $updateRow = $selectOneStatement->fetch();
    }
    else {
        header("Location: users.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include("misc" . DIRECTORY_SEPARATOR . "header.php") ?>
    <h3>Update the Information for <?= $updateRow['username'] ?></h3>
            
    <form action="user-process.php" method="post">
        <fieldset>
            <p>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= $updateRow['username'] ?>" required>
            </p>

            <p>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= $updateRow['email'] ?>" required> 
            </p>

            <p>
                <label for="password">Set new password</label>
                <input id="password" name="password" type="password">
            </p>

            <p>
                <label for="passwordConfirm">Re-enter new password</label>
                <input id="passwordConfirm" name="passwordConfirm" type="password">
            </p>

            <input name="id" type="hidden" value="<?= $updateRow['user_id'] ?>">
        </fieldset>

        <p>
            <button type="submit" value="update" name="command">Update</button>
        </p>
    </form>
</body>
</html>