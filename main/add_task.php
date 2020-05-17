<?php
session_start(); // Initialize the session
require_once "../database/config.php";

if (!isset($_SESSION["id"])) { // Check if the user is logged in, if not then redirect him to login page
    header("location: ../index.php");
    exit;
}

$task_name = $priority = $deadline = $details = $task_name_err = $priority_err = $deadline_err = $details_err = ""; // Define variables and initialize with empty values

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Processing form data when form is submitted
    // echo nl2br("Got POST method\n");

    // Validate task_name
    if (empty(trim($_POST["task_name"]))) {
        $task_name_err = "Please enter a Task Name.";
    } else {
        $task_name = mysqli_real_escape_string($link, trim($_POST["task_name"]));
    }

    // Validate priority
    if (empty(trim($_POST["priority"]))) {
        $priority_err = "Please enter a Priority.";
    } else {
        $priority = mysqli_real_escape_string($link, trim($_POST["priority"]));
    }

    // Validate deadline
    if (empty(trim($_POST["deadline"]))) {
        $deadline_err = "Please enter a Deadline.";
    } else {
        $deadline = mysqli_real_escape_string($link, trim($_POST["deadline"]));
    }

    // Validate details
    if (empty(trim($_POST["details"]))) {
        $details_err = "Please enter a Details.";
    } else {
        $details = mysqli_real_escape_string($link, trim($_POST["details"]));
    }

    if (empty($task_name_err) && empty($priority_err) && empty($deadline_err) && empty($details_err)) { // Check input errors before inserting in database 
        // echo nl2br("Ready to Data Insert\n");
        $sql = "INSERT INTO `tasks`(`task_name`, `priority`, `deadline`, `details`, `created_user`) VALUES (?, ?, ?, ?, ?)"; // Prepare an insert statement
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sisss", $param_task_name, $param_priority, $param_deadline, $param_details, $param_created_user); // Bind variables to the prepared statement as parameters
            $param_task_name = $task_name;
            $param_priority = $priority;
            $param_deadline = $deadline;
            $param_details = $details;
            $param_created_user = $_SESSION["email"];
            if (mysqli_stmt_execute($stmt)) { // Attempt to execute the prepared statement
                // header("location: dashboard.php");
                echo ("<script LANGUAGE='JavaScript'>window.alert('Succesfully Added');window.location.href='dashboard.php';</script>");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt); // Close statement
    }
    mysqli_close($link); // Close connection
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
            <li class="nav-item ml-4">
                <a class="nav-link" href="dashboard.php">Task List</a>
            </li>
            <li class="nav-item active ml-4">
                <a class="nav-link" class="disable">Add Task</a>
            </li>
            <?php
                if($_SESSION["is_admin"] == 1) { echo '<li class="nav-item ml-4"><a class="nav-link" href="register.php">Add Users</a></li>'; }
            ?>
            </ul>
            <span class="navbar-text mx-4">
                <a class="nav-link" href="logout.php">Logout</a>
            </span>
        </div>
    </nav>

    <!-- First Container -->
    <div class="container mt-5">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1 class="h3 mb-5 font-weight-normal text-center">Add New Task</h1>
            <div class="form-row mb-3">
                <div class="form-group col-md-6">
                    <label>Task Name:</label>
                    <input type="text" class="form-control" name="task_name" placeholder="Task Name">
                    <?php if($task_name_err!=""){ echo "<div class='form_err'>" . " $task_name_err " . "</div>"; } ?>
                </div>
                <div class="form-group col-md-3">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="1">Lowest</option>
                        <option value="2">Medium</option>
                        <option value="3">High</option>
                        <option value="4">Highest</option>
                    </select>
                    <?php if($priority_err!=""){ echo "<div class='form_err'>" . " $priority_err " . "</div>"; } ?>
                </div>
                <div class="form-group col-md-3">
                    <label>DeadLine:</label>
                    <input type="date" class="form-control" name="deadline">
                    <?php if($deadline_err!=""){ echo "<div class='form_err'>" . " $deadline_err " . "</div>"; } ?>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="form-group">
                    <label>Tesk Details:</label>
                    <textarea class="form-control" name="details" rows="3" cols="200" autocomplete="off"></textarea>
                    <?php if($details_err!=""){ echo "<div class='form_err'>" . " $details_err " . "</div>"; } ?>
                </div>
            </div>
            <div class="form-row">
                <input type="submit" class="btn btn-success btn-center" value="Add Task">
            </div>
        </form>
    </div>

</body>
</html>