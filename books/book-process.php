<?php

use Gumlet\ImageResize;

    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    require(AUTOLOAD_PATH);
    session_start();

    if (!isset($_POST['command'])) {
        header('Location: ' . BASE . "/books");
        exit();
    }

    if ($_SESSION['role_id'] < 2) {
        header('Location: ' . BASE . "/books");
        exit();
    }

    // Variables for keeping track of errors
    $hasEmpty = false;
    $errorFlag = false;
    $errors = [
        'title' => ["isEmpty" => false, "otherError" => ""],
        'pagecount' => ["isEmpty" => false, "otherError" => ""],
        'author' => ["isEmpty" => false, "otherError" => ""],
        'publisheddate' => ["isEmpty" => false, "otherError" => ""],
        'synopsis' => ["isEmpty" => false, "otherError" => ""]
    ];

    // Checks for empty inputs.
    foreach ($errors as $field => $error) {
        if (!isset($_POST[$field]) || (empty(trim($_POST[$field])))) {
            $errors[$field]['isEmpty'] = true;
            $hasEmpty = true;
            $errorFlag = true;
        }
    }

    // Inputs sanitization and validation.
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $synopsis = filter_input(INPUT_POST, 'synopsis', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!($pageCount = filter_input(INPUT_POST, 'pagecount', FILTER_VALIDATE_INT))) {
        $errors['pagecount']['otherError'] = 'The provided page count is invalid.';
        $errorFlag = true;
    }

    if (!$errors['publisheddate']['isEmpty']) {
        $date = filter_input(INPUT_POST, 'publisheddate', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        try {
            $testDate = new DateTime($date);
        } catch (Exception $e) {
            $error['publisheddate']['otherError'] = 'The provided date is invalid.';
            $errorFlag = true;
        }
    }

    if(!isset($_POST['genres']) || empty($_POST['genres'])) {
        $errors['genres']['isEmpty'] = true;
        $errorFlag = true;
    }
    else {
        $genres = $_POST['genres'];
        foreach($genres as $genre) {
            if(filter_var($genre, FILTER_VALIDATE_INT) === false) {
                $errors['genres']['otherError'] = 'The chosen genres are invalid.';
                $errorFlag = true;
            }
        }
    }

    $imagePath = validateAndSaveImage();

    $command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($command == 'create' && !$errorFlag) {
        $bookQuery = 'INSERT INTO Books(title, synopsis, page_count, date_published, author, cover_image_path) VALUES(:title, :synopsis, :page_count, :date_published, :author, :imagePath)';
        $bookStatement = $db->prepare($bookQuery);
        $bookStatement->bindValue(':title', $title);
        $bookStatement->bindValue(':synopsis', $synopsis);
        $bookStatement->bindValue(':page_count', $pageCount);
        $bookStatement->bindValue(':date_published', $date);
        $bookStatement->bindValue(':author', $author);
        $bookStatement->bindValue(':imagePath', $imagePath);

        $bookStatement->execute();

        $getIdQuery = 'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = \'serverside\' AND TABLE_NAME = \'Books\'';
        $getIdStatement = $db->prepare($getIdQuery);
        $getIdStatement->execute();
        $insertedBookId = $getIdStatement->fetch()['AUTO_INCREMENT'] - 1;

        foreach($genres as $genre) {
            $genreQuery = 'INSERT INTO Book_genres VALUES (:book_id, :genre_id)';
            $genreStatement = $db->prepare($genreQuery);
            $genreStatement->bindValue(':book_id', $insertedBookId);
            $genreStatement->bindValue(':genre_id', $genre);
            $genreStatement->execute();
        }

        header('Location: ' . BASE . '\/books/');
        exit();
    }
    elseif($command == 'update' && !$errorFlag) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $removeImage = filter_input(INPUT_POST, 'removeImage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if($id !== false) {
            $bookQuery = "UPDATE Books SET title = :title, synopsis = :synopsis, page_count = :pageCount, date_published = :datePublished, author=:author WHERE book_id = :id";
            $bookStatement = $db->prepare($bookQuery);
            $bookStatement->bindValue(':title', $title);
            $bookStatement->bindValue(':synopsis', $synopsis);
            $bookStatement->bindValue(':pageCount', $pageCount);
            $bookStatement->bindValue(':datePublished', $date);
            $bookStatement->bindValue(':author', $author);
            $bookStatement->bindValue(':id', $id);
            $bookStatement->execute();

            $removeGenresQuery = "DELETE FROM Book_Genres WHERE book_id = :id";
            $removeGenresStatement = $db->prepare($removeGenresQuery);
            $removeGenresStatement->bindValue(':id', $id);
            $removeGenresStatement->execute();

            foreach($genres as $genre) {
                $genreQuery = 'INSERT INTO Book_genres VALUES (:book_id, :genre_id)';
                $genreStatement = $db->prepare($genreQuery);
                $genreStatement->bindValue(':book_id', $id);
                $genreStatement->bindValue(':genre_id', $genre);
                $genreStatement->execute();
            }

            if($imagePath != null) {

                $coverQuery = "SELECT cover_image_path FROM Books WHERE book_id = :id";
                $coverStatement = $db->prepare($coverQuery);
                $coverStatement->bindValue(':id', $id);
                $coverStatement->execute();
                $oldPath = $coverStatement->fetch()['cover_image_path'];

                if($oldPath != null) {
                    $startPos = strrpos($oldPath, UPLOAD_DIR);
                    $realOldPath = str_replace('/', DS, ROOT . DS . substr($oldPath, $startPos));
                    unlink(realpath($realOldPath));
                }

                $updateCoverQuery = "UPDATE Books SET cover_image_path = :imagePath WHERE book_id = :id";
                $updateCoverStatement = $db->prepare($updateCoverQuery);
                $updateCoverStatement->bindValue(':id', $id);
                $updateCoverStatement->bindValue(':imagePath', $imagePath);
                $updateCoverStatement->execute();
            }
            else if($removeImage == "on") {
                $coverQuery = "SELECT cover_image_path FROM Books WHERE book_id = :id";
                $coverStatement = $db->prepare($coverQuery);
                $coverStatement->bindValue(':id', $id);
                $coverStatement->execute();
                $oldPath = $coverStatement->fetch()['cover_image_path'];
                $startPos = strrpos($oldPath, UPLOAD_DIR);
                $realOldPath = str_replace('/', DS, ROOT . DS . substr($oldPath, $startPos));
                if(realpath($realOldPath)) {
                    unlink(realpath($realOldPath));
                }

                $removeCoverQuery = "UPDATE Books SET cover_image_path = null WHERE book_id = :id";
                $removeCoverStatement = $db->prepare($removeCoverQuery);
                $removeCoverStatement->bindValue(':id', $id);
                $removeCoverStatement->execute();
            }

            header('Location: ' . BASE . '\/books/');
            exit();
        }
        else {
            $errors['id']['otherError'] = "The provided id is invalid.";
        }
    }
    elseif($command == 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if($id !== false) {
            $query = "DELETE FROM Books WHERE book_id = :id LIMIT 1";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
        }

        header('Location: ' . BASE . '\/books/');
        exit();
    }

    function fileIsAnImage($tempPath, $newPath) {
        $allowedMime = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png'];
        $allowedExt = ['gif', 'jpg', 'jpeg', 'png'];

        $fileMime = mime_content_type($tempPath);
        $fileExt = pathinfo($newPath, PATHINFO_EXTENSION);

        return in_array($fileExt, $allowedExt) && in_array($fileMime, $allowedMime);
    }

    function validateAndSaveImage() {
        $uploadSuccess = isset($_FILES['cover']) && ($_FILES['cover']['error'] === 0);

        if($uploadSuccess) {
            $filename = $_FILES['cover']['name'];
            $tempPath = $_FILES['cover']['tmp_name'];
            $newPath = join(DS, [ROOT, UPLOAD_DIR, basename($filename)]);
            if(fileIsAnImage($tempPath, $newPath)) {
                move_uploaded_file($tempPath, $newPath);

                $fileExt = '.' . pathinfo($newPath, PATHINFO_EXTENSION);

                $image = new ImageResize($newPath);
                $image->resizeToWidth(125);
                $resizedName = basename($filename, $fileExt) . "_resized{$fileExt}";
                $image->save(ROOT . DS . UPLOAD_DIR . DS . $resizedName);

                unlink($newPath);

                return join('/', [BASE, UPLOAD_DIR, $resizedName]);
            }
            return null;
        }
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
    <?php include(HEADER_PATH) ?>

    <?php if ($command == 'create') : ?>
        <h2>Error adding book. Please fix the following errors and try again.</h2>
        <ul>
            <?php foreach ($errors as $field => $info) : ?>
                <?php if (!empty($info['otherError'])) : ?>
                    <li><?= $info['otherError'] ?></li>
                <?php endif ?>
            <?php endforeach ?>
        
        <?php if($hasEmpty): ?>
            <li>
                <p>The following fields cannot be empty:</p>
                <ul>
                    <?php foreach ($errors as $field => $info) : ?>
                        <?php if ($info['isEmpty']) : ?>
                            <li><?= $field ?></li>
                        <?php endif ?>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endif ?>
        </ul>

        <p>Errors:</p>


    <?php elseif ($command == 'update') : ?>
        <h2>Error updating user information. Please fix the following errors and try again.</h2>

        <?php if (isset($hasEmpty)) : ?>
            <p>The following fields cannot be empty:</p>
            <ul>
                <?php foreach ($errors as $field => $info) : ?>
                    <?php if ($info['isEmpty'] && ($field != 'password' || $field != 'passwordConfirm')) : ?>
                        <li><?= $field ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <p>Errors:</p>
        <ul>
            <?php foreach ($errors as $field => $info) : ?>
                <?php if (!empty($info['otherError'])) : ?>
                    <li><?= $info['otherError'] ?></li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>

    <?php elseif ($command == 'update') : ?>
        <h2>Error deleting the user. Please try again.</h2>
    <?php endif ?>
</body>

</html>