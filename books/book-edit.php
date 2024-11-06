<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
        header("Location: " . BASE . "/books");
        exit();
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if($id !== false) {
        $bookQuery = "SELECT * FROM Books WHERE book_id = :id";
        $bookStatement = $db->prepare($bookQuery);
        $bookStatement->bindValue(':id', $id);
        $bookStatement->execute();
        $bookRow = $bookStatement->fetch();

        $genreQuery = "SELECT * FROM Genres";
        $genreStatement = $db->prepare($genreQuery);
        $genreStatement->execute();

        $bookGenreQuery = "SELECT * FROM Book_Genres WHERE book_id = :book_id";
        $bookGenreStatement = $db->prepare($bookGenreQuery);
        $bookGenreStatement->bindValue(':book_id', $id);
        $bookGenreStatement->execute();

        $bookGenres = [];
        while($row = $bookGenreStatement->fetch()) {
            array_push($bookGenres, $row['genre_id']);
        }
    }
    else {
        header("Location: " . BASE . "/books");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Book Info</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= STYLE_PATH ?>" rel="stylesheet">

    <script type="text/javascript" src='https://cdn.tiny.cloud/1/ttropo0sdn9lxhwx30krozgrjul57zo9sqlm8t48wf6jadbd/tinymce/6/tinymce.min.js' referrerpolicy="origin"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea',
            plugins: ['autolink', 'link', 'lists advlist', 'autoresize', 'wordcount', 'visualchars'],
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | visualchars |' +
                     'forecolor backcolor emoticons | help | wordcount'
        });
    </script>
</head>
<body>
    <?php include(HEADER_PATH) ?>
    <div class="container">
        <form action="book-process.php" method="post" enctype='multipart/form-data'>
            <fieldset>
                <?php if(!is_null($bookRow['cover_image_path'])): ?>
                    <img src="<?= $bookRow['cover_image_path'] ?>" alt="<?= $bookRow['title'] ?>_cover" class="img-fluid">
                <?php endif ?>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" class="form-control" name="title" value="<?= $bookRow['title'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="author">Author</label>
                    <input id="author" name="author" class="form-control" type="text" value="<?= $bookRow['author'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="genres">Genres</label>
                    <select id="genres" name="genres[]" multiple required>
                        <?php while($row = $genreStatement->fetch()): ?>
                            <option value="<?= $row['genre_id'] ?>"  <?= in_array($row['genre_id'], $bookGenres) ? "selected" : "" ?>><?= $row['genre_name'] ?></option>
                        <?php endwhile ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pagecount">Page count</label>
                    <input id="pagecount" name="pagecount" class="form-control" type="number" value="<?= $bookRow['page_count'] ?>" required> 
                </div>

                <div class="form-group">
                    <label for="publisheddate">Published date</label>
                    <input id="publisheddate" name="publisheddate" class="form-control" type="date" value="<?= $bookRow['date_published'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="synopsis">Synopsis</label>
                    <textarea id="synopsis" name="synopsis" class="form-control" rows="8" cols="70"><?= $bookRow['synopsis'] ?></textarea>
                </div>

                <div class="form-group">
                    <label for="cover">Replace cover image:</label>
                    <input type="file" name="cover" class="form-control-file" id="cover">
                </div>
                
                <?php if(!is_null($bookRow['cover_image_path'])): ?>
                    <p>
                        <input type="checkbox" name="removeImage" id="removeImage">
                        <label for="removeImage">Remove the cover image</label>
                    </p>
                <?php endif ?>
            </fieldset>
            <p>
                <input type="hidden" name="id" value="<?= $bookRow['book_id'] ?>">
                <button type="submit" value="update" name="command" class="btn btn-primary">Update Book</button>
                <button type="submit" value="delete" name="command" class="btn btn-danger" onclick="return confirm('Do you really want to delete this book?')">Delete Book</button>
            </p>
        </form>
    </div>
</body>
</html>