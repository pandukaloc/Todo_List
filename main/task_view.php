<?php

session_start(); // Initialize the session
require_once "../database/config.php";

if (!isset($_SESSION["id"])) { // Check if the user is logged in, if not then redirect him to login page
    header("location: ../index.php");
    exit;
}

$err = $assigned_users = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Processing form data when form is submitted
    if (!(empty(trim($_POST["assign_user"]))) && !(empty(trim($_POST["task_id"])))) { // Check input errors before inserting in database
        $sql = "INSERT INTO `task_user` (`task_id`, `user_id`) VALUES (?, ?)"; // Prepare an insert statement
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $param_task_id, $param_user_id); // Bind variables to the prepared statement as parameters
            $param_task_id = mysqli_real_escape_string($link, trim($_POST["task_id"]));
            $param_user_id = mysqli_real_escape_string($link, trim($_POST["assign_user"]));
            if (mysqli_stmt_execute($stmt)) { // Attempt to execute the prepared statement
                // header("location: dashboard.php");
                echo ("<script LANGUAGE='JavaScript'>window.alert('Succesfully Assign');</script>");
            } else {
                echo ("<script LANGUAGE='JavaScript'>window.alert('Something went wrong. Please Contact Admin');</script>");
            }
        }
        mysqli_stmt_close($stmt); // Close statement
    }
}

$sql = "SELECT `id`, `task_name`, `priority`, `deadline`, `details`, `created_user`, `is_complete`, `created_at` FROM `tasks` WHERE `id`=?"; // Prepare an select statement
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id); // Bind variables to the prepared statement as parameters
    $param_id = mysqli_real_escape_string($link, trim($_GET["id"])); // Set parameters
    if (mysqli_stmt_execute($stmt)) { // Attempt to execute the prepared statement
        mysqli_stmt_store_result($stmt); // Store result
        if (mysqli_stmt_num_rows($stmt) == 1) { // Check if id have 1 task exists
            mysqli_stmt_bind_result($stmt, $id, $task_name, $priority, $deadline, $details, $created_user, $is_complete, $created_at); // Bind result variables
            mysqli_stmt_fetch($stmt);
        } else {
            $err = "No Task avalable for this id"; // Display an error message if id doesn't exist
        }
    } else {
        $err = "Oops! Something went wrong. Contact Admin."; // Display an error message
    }
}
mysqli_stmt_close($stmt); // Close statement

$sql = "SELECT users.name AS user_name FROM task_user INNER JOIN users ON task_user.user_id=users.id WHERE task_user.task_id=?"; // Prepare an select statement
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id); // Bind variables to the prepared statement as parameters
    $param_id = mysqli_real_escape_string($link, trim($_GET["id"])); // Set parameters
    if (mysqli_stmt_execute($stmt)) { // Attempt to execute the prepared statement
        $assigned_user_list = mysqli_stmt_get_result($stmt);
        while ($assigned_user = mysqli_fetch_assoc($assigned_user_list)) {
            $assigned_users = $assigned_users . " | " . $assigned_user['user_name'];
            // echo "<script>alert('".$assigned_user['name']."');</script>";
        }
    } else {
        $err = "Oops! Something went wrong. Contact Admin."; // Display an error message
    }
}
mysqli_stmt_close($stmt); // Close statement

$sql = "SELECT `id`, `name`, `email` FROM `users`"; // Prepare an select statement
if (!($result = mysqli_query($link, $sql))) { // Execute the SELECT Query
    echo "<script>alert('Retrieval of data from Database Failed. Contact Admin');</script>";
}

switch ($priority) {
    case 1: $priority_txt = "Lowest"; break;
    case 2: $priority_txt = "Medium"; break;
    case 3: $priority_txt = "High"; break;
    case 4: $priority_txt = "Highest"; break;
    default: $priority_txt = "Undefine"; break;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>E-Gravity</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery, Popper, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Custom styles-->
    <link rel="stylesheet" type="text/css" href="../style/styles.css">
    <!-- favicon -->
    <link rel="icon" type="image/png" href="../img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="../img/favicon-16x16.png" sizes="16x16" />
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <img class="ml-5 mr-1" src="../img/nav_logo.png" style="width:60px; height:60px;">
        <a class="navbar-brand mr-5 disabled"><?php echo trim($_SESSION["name"]) ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active ml-4">
                    <a class="nav-link" href="dashboard.php">Task List</a>
                </li>
                <li class="nav-item ml-4">
                    <a class="nav-link" href="add_task.php">Add Task</a>
                </li>
                <?php
                if ($_SESSION["is_admin"] == 1) {
                    echo '<li class="nav-item ml-4"><a class="nav-link" href="register.php">Add Users</a></li>';
                }
                ?>
            </ul>
            <span class="navbar-text mx-4">
                <a class="nav-link" href="logout.php">Logout</a>
            </span>
        </div>
    </nav>

    <!-- First Container -->
    <div class="container">
        <h3 class="text-center mt-5 mb-3">
            <?php 
                echo $task_name." - "; 
                if($is_complete==1){
                    echo '<input type="checkbox" id="myCheck"  onclick="myFunction()" checked disabled/>';
                } else{ 
                    echo '<input type="checkbox" id="myCheck"  onclick="myFunction()"/>';
                } 
            ?>
        </h3>
        <div class="row mb-2">
            <div class="col-sm">
                <label class="mb">Deadline</label>
                <input type="text" class="form-control" value="<?php echo $deadline; ?>" readonly>
            </div>
            <div class="col-sm">
                <label class="mb">Priority</label>
                <input type="text" class="form-control" value="<?php echo $priority_txt; ?>" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm">
                <label class="mb">Completion</label>
                <input type="text" class="form-control" value="<?php if ($is_complete == 1) { echo "Completed"; } else { echo "In Progress"; } ?>" readonly>
            </div>
            <div class="col-sm">
                <label class="mb">Created User</label>
                <input type="text" class="form-control" value="<?php echo $created_user; ?>" readonly>
            </div>
            <div class="col-sm">
                <label class="mb">Added Date</label>
                <input type="text" class="form-control" value="<?php echo $created_at; ?>" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <label class="mb">Task Description</label>
                <textarea class="form-control" rows="2" cols="200" readonly><?php echo $details; ?></textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <label class="mb">Assigned Users</label>
                <textarea class="form-control" rows="1" cols="200" readonly><?php echo substr($assigned_users, 3); ?></textarea>
            </div>
        </div>
        <div id="assign_to_users" <?php if($is_complete==1){ echo 'style="display:none;"';} ?>>
            <form class="row col mt-5 text-center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$id; ?>" method="post">
                <div class="form-group mb-2">
                    <label class="sr-only">Title</label>
                    <input type="text"  class="form-control-plaintext" value="Assign a User to Task: " readonly>
                    <input type="text"  class="sr-only" name="task_id" value="<?php echo $id; ?>" hidden required>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label class="sr-only">User</label>
                    <select class="form-control" name="assign_user" required>
                        <option value="" selected>Select User</option>
                        <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_array($result)) {
                                    if(!(strpos($assigned_users, $row['name']) !== false)){
                                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                    }
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-row">
                    <input type="submit" class="btn btn-primary btn-block btn-center" style="display: inline-block!important;height: 80%;" value="Assign">
                </div>
            </form>
        </div>
    </div>
    <!-- Is Completed Modle -->
    <!-- <div class="modal fade" id="isCompleteModelChecked" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confamation Dialog</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you need to mark this task as completed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary">Yes</button>
                </div>
            </div>
        </div>
    </div> -->
    <script type='text/javascript'>
        function myFunction() {
            var checkBox = document.getElementById("myCheck");
            if (checkBox.checked == true){
                if (confirm("Do you need to mark this task as completed?")) {
                    console.log("Ready")
                    // $.ajax({url: 'mark_as_completed.php', type: 'POST', data: { id: '', }, success: function(msg) { alert('Email Sent'); } });
                    $.post("mark_as_completed.php", {id: <?php echo $id; ?>}, function(result){ alert('Updated'); });
                    document.getElementById("myCheck").checked = true;
                    document.getElementById("myCheck").disabled = true;
                    document.getElementById("assign_to_users").style.display = "none"
                } else {
                    console.log("Unchecked");
                    document.getElementById("myCheck").checked = false;
                    document.getElementById("myCheck").disabled = false;
                }
            }
        }
    </script>

</body>

</html>