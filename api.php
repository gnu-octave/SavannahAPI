<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/src/api.php");

echo((new api())->processRequest($_GET));

?>
