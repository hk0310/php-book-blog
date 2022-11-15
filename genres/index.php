<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($id !== false && !is_null($id) && !is_null($slug)) {
        $genreQuery = "SELECT * FROM Genres WHERE genre_id = :id && slug_text = :slug";
        $genreStatement = $db->prepare($genreQuery);
        $genreStatement->bindValue(':id', $id);
        $genreStatement->bindValue(':slug', $slug);
        $genreStatement->execute();
        $genreRow = $genreStatement->fetch();

        if(empty($genreRow)) {
            header("Location: " . BASE . "/genres");
            exit();
        }

        $bookQuery = "SELECT * FROM Book_Genres bg JOIN Books b ON bg.book_id = b.book_id WHERE bg.genre_id = :id";
        $bookStatement = $db->prepare($bookQuery);
        $bookStatement->bindValue(':id', $genreRow['genre_id']);
        $bookStatement->execute();
    }

    $query = "SELECT * FROM Genres";
    $statement = $db->prepare($query);
    $statement->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genres</title>
</head>

<body>
    <?php include(HEADER_PATH) ?>
    <?php if(isset($bookStatement)): ?>
        <?php while ($row = $bookStatement->fetch()) : ?>
            <?php if ($row['cover_image_path']) : ?>
                <img src="<?= $row['cover_image_path'] ?>" alt="<?= $row['title'] ?>_cover">
            <?php else : ?>
                <img src="<?= NO_COVER_PATH ?>" alt="no_cover">
            <?php endif ?>

            <p><a href="<?= BASE ?>/books/<?= $row['book_id'] ?>/<?= $row['slug_text'] ?>"><?= $row['title'] ?></a></p>
            <p>By <?= $row['author'] ?></p>
            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] > 1): ?>
                <p><a href="<?= BASE ?>/books/book-edit.php?id=<?= $row['book_id'] ?>">Make Changes</a></p>
            <?php endif ?>
        <?php endwhile ?>

    <?php else: ?>
        <?php if (isset($_SESSION['role_id']) && $_SESSION["role_id"] > 1) : ?>
            <p><a href="genre-create.php">Add a new genre</a></p>
        <?php endif ?>
        <?php while($row = $statement->fetch()): ?>
            <p><a href="<?= $row['genre_id'] ?>/<?= $row['slug_text'] ?>"><?= $row['genre_name'] ?></a></p>

            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] > 1): ?>
                <p><a href="genre-edit.php?id=<?= $row['genre_id'] ?>">Make Changes</a></p>
            <?php endif ?>
        <?php endwhile ?>
    <?php endif ?>
</body>

</html>