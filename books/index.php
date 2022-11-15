<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($id !== false && !is_null($id) && !is_null($slug)) {
        $query = "SELECT * FROM Books WHERE book_id = :id AND slug_text = :slug";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->bindValue(':slug', $slug);
        $statement->execute();
        $row = $statement->fetch();
        if(empty($row)) {
            header("Location: " . BASE . "/books");
            exit();
        }
    }
    else {
        $query = "SELECT * FROM Books";
        $statement = $db->prepare($query);
        $statement->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
</head>

<body>
    <?php include(HEADER_PATH) ?>
    <?php if ($id !== false && !is_null($id) && !is_null($slug) && !empty($row)) : ?>
        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] > 1) : ?>
            <p><a href="<?= BASE ?>/books/book-edit.php?id=<?= $row['book_id'] ?>">Make Changes</a></p>
        <?php endif ?>

        <p><?= $row['title'] ?></p>
        <p>By <?= $row['author'] ?></p>

        <?php if ($row['cover_image_path']) : ?>
            <img src="<?= $row['cover_image_path'] ?>" alt="<?= $row['title'] ?>_cover">
        <?php else : ?>
            <img src="<?= NO_COVER_PATH ?>" alt="no_cover">
        <?php endif ?>

        <p>Published on <?= date("F jS, Y", strtotime($row['date_published'])) ?>, <?= $row['page_count'] ?> pages</p>
        <?= htmlspecialchars_decode($row['synopsis']) ?>
    <?php else : ?>
        <?php if (isset($_SESSION['role_id']) && $_SESSION["role_id"] > 1): ?>
            <p><a href="book-create.php">Add a new book</a></p>
        <?php endif ?>

        <?php while ($row = $statement->fetch()) : ?>
            <?php if ($row['cover_image_path']) : ?>
                <img src="<?= $row['cover_image_path'] ?>" alt="<?= $row['title'] ?>_cover">
            <?php else : ?>
                <img src="<?= NO_COVER_PATH ?>" alt="no_cover">
            <?php endif ?>

            <p><a href="<?= $row['book_id'] ?>/<?= $row['slug_text'] ?>"><?= $row['title'] ?></a></p>
            <p>By <?= $row['author'] ?></p>
            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] > 1): ?>
                <p><a href="book-edit.php?id=<?= $row['book_id'] ?>">Make Changes</a></p>
            <?php endif ?>
        <?php endwhile ?>
    <?php endif ?>
</body>

</html>