<?php
    require("misc" . DIRECTORY_SEPARATOR . "connect.php");
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit();
    }

    if (!$_SESSION['role_id'] === 3) {
        header("Location: index.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Create</title>
</head>
<body>
<?php require("misc" . DIRECTORY_SEPARATOR . "header.php") ?>

<form action="user-process.php" method="post">
    <fieldset>
        <p>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </p>

        <p>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required> 
        </p>

        <p>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </p>

        <p>
            <label for="passwordConfirm">Re-enter password</label>
            <input id="passwordConfirm" name="passwordConfirm" type="password" required>
        </p>
    </fieldset>

    <p>
        <button type="submit" value="create" name="command">Create User</button>
    </p>
</form>
</body>
</html>