<?php
    define("DS", DIRECTORY_SEPARATOR);
    define("ROOT", __DIR__);
    $startIndex = strrpos(ROOT, "htdocs") + strlen("htdocs");
    define("BASE", str_replace('\\', '/', substr(ROOT, $startIndex)));
    define("CONNECT_PATH", ROOT . DS . "misc" . DS . "connect.php");
    define("STYLE_PATH", BASE . "/styles/style.css");
    define("HEADER_PATH", ROOT . DS . "misc" . DS . "header.php");
    define("AUTOLOAD_PATH", ROOT . DS . "vendor" . DS . "autoload.php");
    define("NO_COVER_PATH", BASE . "/uploads/no_cover.png");
    define("UPLOAD_DIR", "uploads");
    define("SLUG_GEN_PATH", ROOT . DS . "misc" . DS . "slug-generator.php");
?>