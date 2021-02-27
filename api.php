<?php

require_once(dirname(__FILE__) . "/src/api.php");

echo((new api())->processRequest($_GET));

?>
