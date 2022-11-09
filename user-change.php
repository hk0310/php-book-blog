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
    <title>Update User Info</title>
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
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="1" <?= $updateRow['role_id'] == 1 ? "selected" : "" ?>>Regular user</option>
                    <option value="2" <?= $updateRow['role_id'] == 2 ? "selected" : "" ?>>Administrator</option>
                    <option value="3" <?= $updateRow['role_id'] == 3 ? "selected" : "" ?>>Owner</option>
                </select>
            </p>

            <p>
                <label for="password">Set new password</label>
                <input id="password" name="password" type="password">
            </p>

            <p>
                <label for="passwordConfirm">Re-enter new password</label>
                <input id="passwordConfirm" name="passwordConfirm" type="password">
            </p>

            <input name="id" type="hidden" value="<?= $updateRow['user_id'] ?>" readonly>
        </fieldset>

        <p>
            <button type="submit" value="update" name="command">Update</button>
            <button type="submit" value="delete" name="command" onclick="return confirm('Do you really want to delete this user?')">Delete</button>
        </p>
    </form>
</body>
</html>