<?php
    use Mobicms\Captcha;

    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(AUTOLOAD_PATH);
    session_start();

    $code = (string) new Captcha\Code;
    $_SESSION['code'] = $code;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <?php require(HEADER_PATH) ?>

    <form action="<?= BASE ?>/users/user-process.php" method="post">
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

            <p>
                <img src="<?= new Captcha\Image($code) ?>" alt="captcha">
            </p>
            <p>
                <label for="captcha">Enter the captcha code</label>
                <input type="text" name="captcha" id="captcha" required>
            </p>
        </fieldset>

        <p>
            <button type="submit" value="create" name="command">Register</button>
        </p>
    </form>
</body>
</html>