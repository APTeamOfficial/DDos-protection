<?php
{
    require_once "adp.php";
    if (validate_check() !== false) {
        echo "Hello this is a page for test";
    } else {
        validate_ref("https://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]);
    }
}
?>