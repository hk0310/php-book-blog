<?php 
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    require(SLUG_GEN_PATH);
    session_start();

    if (!isset($_SESSION['role_id']) || $_SESSION["role_id"] < 2) {
        header("Location: " . BASE . "/genres");
        exit();
    }

    if(isset($_POST['command'])) {
        $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(!is_null($genre) && !empty($genre)) {
            $query = "INSERT INTO Genres (genre_name, slug_text) VALUE (:genre, :slug)";
            $statement = $db->prepare($query);
            $statement->bindValue(':genre', $genre);
            $statement->bindValue(':slug', generateSlug($genre));
            $statement->execute();
            
            header("Location: " . BASE . "/genres");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Genre</title>
</head>
<body>
    <?php include(HEADER_PATH); ?>
    <form method="post" action="#">
        <p>
            <label for="Genre">Genre</label>
            <input type="text" id="genre" name="genre" required>
        </p>
        <button type="submit" name="command" value="create">Add Genre</button>
    </form>
</body>
</html>