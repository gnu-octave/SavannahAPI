<?php

require_once(dirname(__FILE__) . "/server/api.php");

echo((new api())->processRequest($_GET));

?>
