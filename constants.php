<?php
    define("DS", DIRECTORY_SEPARATOR);
    define("ROOT", __DIR__);
    $startIndex = strrpos(ROOT, "htdocs") + strlen("htdocs");
    define("BASE", str_replace('\\', '/', substr(ROOT, $startIndex)));
    define("CONNECT_PATH", ROOT . DIRECTORY_SEPARATOR . "misc" . DIRECTORY_SEPARATOR . "connect.php");
    define("HEADER_PATH", ROOT . DIRECTORY_SEPARATOR . "misc" . DIRECTORY_SEPARATOR . "header.php");
?>