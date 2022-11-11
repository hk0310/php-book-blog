<?php
    define("DS", DIRECTORY_SEPARATOR);
    define("ROOT", __DIR__);
    $startIndex = strrpos(ROOT, "htdocs") + strlen("htdocs");
    define("BASE", str_replace('\\', '/', substr(ROOT, $startIndex)));
    define("CONNECT_PATH", ROOT . DS . "misc" . DS . "connect.php");
    define("HEADER_PATH", ROOT . DS . "misc" . DS . "header.php");
    define("NO_COVER_PATH", ROOT . DS . "uploads" . "no_cover.png");
?>