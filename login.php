<?php
    require('connect.php');

    $error = "";
    if(isset($_POST['button'])) {
        if(!isset($_POST['username']) || !isset($_POST['password'])) {
            $error = "Username/password cannot be blank.";
        }

        if(empty($error)) {
            $error = CheckLogin($db);
        }
    }

    function CheckLogin($db) {
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT * FROM Users WHERE username = :username";
        $statement = $db->prepare($query);
        $statement->bindValue(":username", $username);
        $statement->execute();

        $user = $statement->fetch();

        if(empty($user)) {
            return "Username and password do not match.";
        }

        if(password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit();
        }
        else {
            return "Username and password do not match.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<?php require("header.php") ?>

<form action="login.php" method="post">
        <?php if(!empty($error)): ?>
            <p><?= $error ?></p>
        <?php endif ?>
        <fieldset>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            <p>
                <button type="submit" value="Login" name="button">Log In</button>
            </p>
        </fieldset>
    </form>
</body>
</html>