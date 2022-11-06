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
    else {
        $selectAllQuery = "SELECT * FROM Users u JOIN Roles r ON r.role_id = u.role_id ORDER BY u.role_id DESC, u.username";
        $selectAllStatement = $db->prepare($selectAllQuery);
        $selectAllStatement->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <div class="container">
        <?php include("misc" . DIRECTORY_SEPARATOR . "header.php") ?>
        <h3>Manage Users</h3>
        <p><a href="user-create.php">Add User</a></p>

        <div class="accordion accordion-flush" id="accordionFlush">
            <?php while ($row = $selectAllStatement->fetch()) : ?>
                <div class="accordion-item">
                    <h4 class="accordion_header" id="flush-heading<?= $row['user_id'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $row['user_id'] ?>" aria-expanded="false" aria-controls="flush-collapse<?= $row['user_id'] ?>">
                            <?= $row['username'] ?>
                        </button>
                    </h4>

                    <div id="flush-collapse<?= $row['user_id'] ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?= $row['user_id'] ?>">
                        <div class="accordion-body">
                            <p>Username: <?= $row['username'] ?></p>
                            <p>Email: <?= $row['email'] ?></p>
                            <p>Role: <?= $row['role_name'] ?></p>
                            <p>Date joined: <?= $row['date_joined'] ?></p>
                            <p><a href="user-change.php?id=<?= $row['user_id'] ?>">Make Changes</a></p>
                        </div>
                    </div>
                </div>
            <?php endwhile ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>

</html>