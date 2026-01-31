<?php
// includes/admin_auth.php

session_start(); // must be FIRST, before any output

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}
