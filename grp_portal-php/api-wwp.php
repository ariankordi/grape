<?php
if(isset($_GET["old"])){
    require "api-wwp-old.php";
} else {
    require "api-wwp-new.php";
}
exit();
?>