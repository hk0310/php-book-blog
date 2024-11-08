<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    if(!isset($_SESSION['username']) || $_SESSION['role_id'] <= 1) {
        header("Location: " . BASE . "/books");
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
            <legend>Add Book</legend>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="author">Author</label>
                <input id="author" name="author" type="text" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="genres">Genres</label>
                <select id="genres" name="genres[]" class="form-control" multiple required>
                    <?php while($row = $statement->fetch()): ?>
                        <option value="<?= $row['genre_id'] ?>"><?= $row['genre_name'] ?></option>
                    <?php endwhile ?>
                </select>
            </div>

            <div class="form-group">
                <label for="pagecount">Page count</label>
                <input id="pagecount" name="pagecount" type="number" class="form-control" required> 
            </div>

            <div class="form-group">
                <label for="publisheddate">Published date</label>
                <input id="publisheddate" name="publisheddate" type="date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="synopsis">Synopsis</label>
                <textarea id="synopsis" name="synopsis" rows="8" cols="70" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="cover">Cover image</label>
                <input type="file" name="cover" id="cover" class="form-control-file">
            </div>
        </fieldset>

        <button type="submit" value="create" name="command" class="btn btn-primary">Add Book</button>
    </div>
</body>
</html>

