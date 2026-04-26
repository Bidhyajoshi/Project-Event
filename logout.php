<?php
session_start();
session_unset();
session_destroy();
header("Location: gate.php?msg=self-destructed");
exit();
?>
