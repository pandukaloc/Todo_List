<?php

session_start();
require_once "../database/config.php";

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["id"]) || $_SESSION["is_admin"] != 1) {
    header("location: logout.php");
    exit;
}

$email = $password = $name = $is_admin = $email_err = $password_err = $name_err = $type_err = $registerform_err = ""; // Define variables and initialize with empty values

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Processing form data when form is submitted
    // echo nl2br("Got POST method\n");

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a valied email";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?"; // Prepare a select statement
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email); // Bind variables to the prepared statement as parameters
            $param_email = trim($_POST["email"]); // Set parameters
            if (mysqli_stmt_execute($stmt)) { // Attempt to execute the prepared statement
                mysqli_stmt_store_result($stmt); //store result
                if (mysqli_stmt_num_rows($stmt) == 0) {
                    $email = trim($_POST["email"]);
                    // echo nl2br("Email Correct\n");
                } else {
                    $email_err = "This email is already used.";
                }
            } else {
                $registerform_err = "Something went wrong. Contact Admin.";
            }
        }
        mysqli_stmt_close($stmt); // Close statement
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a valied password.";
    } elseif (strlen(trim($_POST["password"])) < 5) {
        $password_err = "Password must have atleast 5 characters.";
    } else {
        $password = mysqli_real_escape_string($link, trim($_POST["password"]));
    }

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a Name.";
    } else {
        $name = mysqli_real_escape_string($link, trim($_POST["name"]));
    }

    // Validate type
    if (empty(trim($_POST["type"]))) {
        $type_err = "Please enter a user Type.";
    } else {
        $type = mysqli_real_escape_string($link, trim($_POST["type"]));
    }

    // echo "List: ".$email_err.$password_err.$name_err.$type_err.$registerform_err;

    if (empty($email_err) && empty($password_err) && empty($name_err) && empty($type_err) && empty($registerform_err)) { // Check input errors before inserting in database 
        // echo nl2br("Ready to Data Insert\n");
        $sql = "INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)"; // Prepare an insert statement
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_email, $param_password, $param_type); // Bind variables to the prepared statement as parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_name = $name;
            $param_type = $type;
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
                <li class="nav-item ml-4">
                    <a class="nav-link" href="add_task.php">Add Task</a>
                </li>
                <li class="nav-item active ml-4">
                    <a class="nav-link" class="disabled">Add Users</a>
                </li>
            </ul>
            <span class="navbar-text mx-4">
                <a class="nav-link" href="logout.php">Logout</a>
            </span>
        </div>
    </nav>

    <!-- First Container -->
    <div class="container mt-5">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1 class="h3 mb-5 font-weight-normal text-center">Add New User</h1>
            <div class="form-row mb-3">
                <div class="form-group col-md-6">
                    <label for="inputName">Name:</label>
                    <input type="text" class="form-control" id="inputName" name="name" placeholder="Full Name">
                    <?php if($name_err!=""){ echo "<div class='form_err'>" . " $name_err " . "</div>"; } ?>
                </div>
                <div class="form-group col-md-6">
                    <label>User Type</label>
                    <select name="type" class="form-control">
                        <option value="0">User</option>
                        <option value="1">Admin</option>
                    </select>
                    <?php if($type_err!=""){ echo "<div class='form_err'>" . " $type_err " . "</div>"; } ?>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="form-group col-md-6">
                    <label for="inputEmail">Email:</label>
                    <input type="text" class="form-control" id="inputEmail" name="email" placeholder="Email" autocomplete="off">
                    <?php if($email_err!=""){ echo "<div class='form_err'>" . " $email_err " . "</div>"; } ?>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputpassword">Password<span style="font-size:80%; color:#000;"> - (Password must have atleast 5 characters)</span></label>
                    <input type="password" class="form-control" id="inputpassword" name="password" autocomplete="off">
                    <?php if($password_err!=""){ echo "<div class='form_err'>" . " $password_err " . "</div>"; } ?>
                </div>
            </div>
            <div class="form-row">
                <input type="submit" class="btn btn-success btn-center" value="Add User">
            </div>
        </form>
    </div>

</body>

</html>