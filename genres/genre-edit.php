<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    require(SLUG_GEN_PATH);
    session_start();

    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
        header("Location: " . BASE . "/genres");
        exit();
    }

    if(!isset($_POST['command'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if(is_null($id) || $id === false) {
            header("Location: " . BASE . "/genres");
            exit();
        }

        $query = "SELECT * FROM Genres WHERE genre_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $row = $statement->fetch();
    }
    else {
        $command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(empty($command) || ($command !== "update" && $command !== "delete")) {
            header("Location: " . BASE . "/genres");
            exit();
        }

        if($command == "update") {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if(!is_null($id) && !empty($id) && !is_null($genre) && !empty($genre)) {
                $query = "UPDATE Genres SET genre_name = :genre, slug_text = :slug WHERE genre_id = :id";
                $statement = $db->prepare($query);
                $statement->bindValue(':genre', $genre);
                $statement->bindValue(':slug', generateSlug($genre));
                $statement->bindValue(':id', $id);
                $statement->execute();

                header("Location: " . BASE . "/genres");
                exit();
            }
        }
        elseif($command == "delete") {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if(!is_null($id) && !empty($id)) {
                $query = "DELETE FROM Genres WHERE genre_id = :id";
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $statement->execute();

                header("Location: " . BASE . "/genres");
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Genres</title>
</head>
<body>
    <?php include(HEADER_PATH) ?>
    <form method="post" action="#">
        <p>
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?= $row['genre_name'] ?>">
        </p>
        <input type="hidden" name="id" value="<?= $row['genre_id'] ?>">
        <button type="submit" value="update" name="command">Update Genre</button>
        <button type="submit" value="delete" name="command" onclick="return confirm('Do you really want to delete this genre?')">Delete Genre</button>
    </form>
</body>
</html>