<?php
    session_start();
    require(".." . DIRECTORY_SEPARATOR . "constants.php");

    if(isset($_SESSION["username"])) {
        session_destroy();
        header("Location: " . BASE);
        exit();
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
    <p>Page not found.</p>
</body>
</html>