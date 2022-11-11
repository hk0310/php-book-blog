<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    if(!isset($_SESSION['username']) || $_SESSION['role_id'] <= 1) {
        header("Location: " . BASE);
        exit();
    }

    $query = "SELECT * FROM genres";
    $statement = $db->prepare($query);
    $statement->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a Book</title>
</head>
<body>
    <?php include(HEADER_PATH) ?>
    <form action="book-process.php" method="post" enctype='multipart/form-data'>
        <fieldset>
            <p>
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </p>

            <p>
                <label for="author">Author</label>
                <input id="author" name="author" type="text" required>
            </p>

            <p>
                <label for="author">Author</label>
                <input id="author" name="author" type="text" required>

            <p>
                <label for="pagecount">Page count</label>
                <input id="pagecount" name="pagecount" type="number" required> 
            </p>

            <p>
                <label for="publisheddate">Published date</label>
                <input id="publisheddate" name="publisheddate" type="date" required>
            </p>

            <p>
                <label for="synopsis">Synopsis</label>
                <textarea id="synopsis" name="synopsis" rows="8" cols="70"></textarea>
            </p>

            <label for="cover">Cover image</label>
            <input type="file" name="cover" id="cover">
        </fieldset>

        <p>
            <button type="submit" value="create" name="command">Add Book</button>
        </p>
    </form>
</body>
</html>