<?php
session_start();
unset($_SESSION['customer_id'], $_SESSION['customer_name']);
session_destroy();
header('Location: /customer/login.php');
exit;
