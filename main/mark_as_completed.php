<?php

session_start();
require_once "../database/config.php";

if (!isset($_SESSION["id"])) { // Check if the user is logged in, if not then redirect him to login page
    header("location: logout.php");
    exit;
}

if (($_SERVER["REQUEST_METHOD"] == "POST") && (!(empty(trim($_POST["id"]))))) { // Processing form data when form is submitted
    $sql = "UPDATE `tasks` SET `is_complete`= 1 WHERE `id` = ?;"; // Prepare an insert statement
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id); // Bind variables to the prepared statement as parameters
        $param_id = mysqli_real_escape_string($link, trim($_POST["id"]));
        if (!(mysqli_stmt_execute($stmt))) { // Attempt to execute the prepared statement
            echo ("<script LANGUAGE='JavaScript'>window.alert('Succesfully Added');window.location.href='task_view.php?id=" . $param_id ."';</script>");
        }
    }
    mysqli_stmt_close($stmt); // Close statement
    mysqli_close($link); // Close connection
}
