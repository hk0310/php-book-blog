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
        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $ascSymbol = '△';
        $descSymbol = '▽';
        $beingSorted = [
            'title' => ['symbol' => '', 'sql' => ''],
            'author' => ['symbol' => '', 'sql' => ''], 
            'date_published' => ['symbol' => '', 'sql' => '']
        ];

        if(!is_null($sort) && !empty($sort)) {
            $query = '';
            $statement = null;

            if($sort === "book") {
                if(isset($_SESSION['bookSort']) && $_SESSION['bookSort'] === 'ASC') {
                    $beingSorted['title']['sql'] = 'DESC';
                    $beingSorted['title']['symbol'] = $descSymbol;
                    $_SESSION['bookSort'] = 'DESC';
                }
                elseif(isset($_SESSION['bookSort']) && $_SESSION['bookSort'] === 'DESC') {
                    $beingSorted['title']['sql'] = 'ASC';
                    $beingSorted['title']['symbol'] = $ascSymbol;
                    $_SESSION['bookSort'] = 'ASC';
                }
                else {
                    $_SESSION['bookSort'] = 'ASC';
                    $beingSorted['title']['sql'] = 'ASC';
                    $beingSorted['title']['symbol'] = $ascSymbol;
                }
            }
            elseif($sort === "author") {
                if(isset($_SESSION['bookSort']) && $_SESSION['bookSort'] === 'ASC') {
                    $beingSorted['author']['sql'] = 'DESC';
                    $beingSorted['author']['symbol'] = $descSymbol;
                    $_SESSION['bookSort'] = 'DESC';
                }
                elseif(isset($_SESSION['bookSort']) && $_SESSION['bookSort'] === 'DESC') {
                    $beingSorted['author']['sql'] = 'ASC';
                    $beingSorted['author']['symbol'] = $ascSymbol;
                    $_SESSION['bookSort'] = 'ASC';
                }
                else {
                    $_SESSION['bookSort'] = 'ASC';
                    $beingSorted['author']['sql'] = 'ASC';
                    $beingSorted['author']['symbol'] = $ascSymbol;
                }
            }
            if($sort === "date") {
                if(isset($_SESSION['bookSort']) && $_SESSION['bookSort'] === 'ASC') {
                    $beingSorted['date_published']['sql'] = 'DESC';
                    $beingSorted['date_published']['symbol'] = $descSymbol;
                    $_SESSION['bookSort'] = 'DESC';
                }
                elseif(isset($_SESSION['bookSort']) && $_SESSION['bookSort'] === 'ASC') {
                    $beingSorted['date_published']['sql'] = 'ASC';
                    $beingSorted['date_published']['symbol'] = $ascSymbol;
                    $_SESSION['bookSort'] = 'ASC';
                }
                else {
                    $_SESSION['bookSort'] = 'ASC';
                    $beingSorted['date_published']['sql'] = 'ASC';
                    $beingSorted['date_published']['symbol'] = $ascSymbol;
                }
            }

            foreach($beingSorted as $column => $info) {
                if(!empty($info['sql'])) {
                    $query = "SELECT * FROM Books ORDER BY {$column} {$info['sql']}";
                    $statement = $db->prepare($query);
                    $statement->execute();
                }
            }
        }
        else {
            $query = "SELECT * FROM Books";
            $statement = $db->prepare($query);
            $statement->execute();
        }
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
        <p>
            <a href="?sort=book">Book<strong><?= $beingSorted['title']['symbol'] ?></strong></a>
            <a href="?sort=author">Author<strong><?= $beingSorted['author']['symbol'] ?></strong></a>
            <a href="?sort=date">Published Date<strong><?= $beingSorted['date_published']['symbol'] ?></strong></a>
        </p>
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