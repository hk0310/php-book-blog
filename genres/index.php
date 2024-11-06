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

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= STYLE_PATH ?>" rel="stylesheet">
</head>

<body>
    <?php include(HEADER_PATH) ?>
    <div class="container">
        <?php if(isset($bookStatement)): ?>
            <div class="row">
                <?php while ($row = $bookStatement->fetch()) : ?>
                    <div class="col-md-4">
                        <?php if ($row['cover_image_path']) : ?>
                            <img src="<?= $row['cover_image_path'] ?>" alt="<?= $row['title'] ?>_cover" class="img-fluid">
                        <?php else : ?>
                            <img src="<?= NO_COVER_PATH ?>" alt="no_cover" class="img-fluid">
                        <?php endif ?>

                        <h3><a href="<?= $row['book_id'] ?>/<?= $row['slug_text'] ?>"><?= $row['title'] ?></a></h3>
                        <p>By <?= $row['author'] ?></p>
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] > 1): ?>
                            <p><a href="<?= BASE ?>/books/book-edit.php?id=<?= $row['book_id'] ?>" class="btn btn-primary">Make Changes</a></p>
                        <?php endif ?>
                    </div>
                <?php endwhile ?>
            </div>

        <?php else: ?>
            <?php if (isset($_SESSION['role_id']) && $_SESSION["role_id"] > 1) : ?>
                <p><a href="genre-create.php" class="btn btn-primary">Add a new genre</a></p>
            <?php endif ?>
            <?php while($row = $statement->fetch()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><a href="<?= $row['genre_id'] ?>/<?= $row['slug_text'] ?>"><?= $row['genre_name'] ?></a></h5>
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] > 1): ?>
                            <p class="card-text"><a href="genre-edit.php?id=<?= $row['genre_id'] ?>">Make Changes</a></p>
                        <?php endif ?>
                    </div>
                </div>
            <?php endwhile ?>
        <?php endif ?>
    </div>

    <!-- Bootstrap JS and dependencies (jQuery) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>