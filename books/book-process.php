<?php
require(".." . DIRECTORY_SEPARATOR . "constants.php");
require(CONNECT_PATH);
session_start();

if (!isset($_POST['command'])) {
    header('Location: ' . BASE);
    exit();
}

if ($_SESSION['role_id'] < 2) {
    header('Location: ' . BASE);
    exit();
}

// Variables for keeping track of errors
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

$imagePath = validateAndSaveImage();

$command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($command == 'create' && !$errorFlag) {
    $query = 'INSERT INTO Books(book_name, synopsis, page_count, date_published, author, cover_image_path) VALUES(:title, :synopsis, :page_count, :date_published, :author, :imagePath)';
    $statement = $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':synopsis', $synopsis);
    $statement->bindValue(':page_count', $pageCount);
    $statement->bindValue(':date_published', $date);
    $statement->bindValue(':author', $author);
    $statement->bindValue(':imagePath', $imagePath);
    $statement->execute();

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
    define("UPLOAD_DIR", "uploads");

    $uploadSuccess = isset($_FILES['cover']) && ($_FILES['cover']['error'] === 0);

    if($uploadSuccess) {
        $filename = $_FILES['cover']['name'];
        $tempPath = $_FILES['cover']['tmp_name'];
        $newPath = join(DS, [ROOT, UPLOAD_DIR, basename($filename)]);
        if(fileIsAnImage($tempPath, $newPath)) {
            move_uploaded_file($tempPath, $newPath);
            return $newPath;
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