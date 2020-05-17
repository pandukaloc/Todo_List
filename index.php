<?php

session_start(); // Initialize the session

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) { // Check if the user is already logged in, if yes then redirect him to welcome page
    header("location: main/dashboard.php");
    exit;
}

require_once "database/config.php"; // Include database config file

$email = $password = $form_signin_err = ""; // Define variables and initialize with empty values

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Processing form data when form is submitted

    // echo nl2br("Processing form data when form is submitted\n");

    if (empty(trim($_POST["email"])) && empty(trim($_POST["password"]))) { // Check if email and password is empty
        $form_signin_err = "Please enter your credentials";
        // echo nl2br("Please enter your credentials\n");
    } else {
        $email = mysqli_real_escape_string($link, trim($_POST["email"]));
        $password = mysqli_real_escape_string($link, trim($_POST["password"]));
        // echo nl2br("Got email and password\n");
        // echo $email . " and " . $password;
        // echo nl2br("...\n");
    }

    // Validate credentials
    if (empty($form_signin_err)) {
        // echo nl2br("Tring to Validate credentials\n");
        $sql = "SELECT id, name, password, is_admin FROM users WHERE email = ?"; // Prepare a select statement
        if ($stmt = mysqli_prepare($link, $sql)) {
            // echo nl2br("Get data from Database\n");
            mysqli_stmt_bind_param($stmt, "s", $param_email); // Bind variables to the prepared statement as parameters
            $param_email = $email; // Set parameters
            if (mysqli_stmt_execute($stmt)) { // Attempt to execute the prepared statement
                // echo nl2br("Attempt to execute the prepared statement\n");
                mysqli_stmt_store_result($stmt); // Store result
                if (mysqli_stmt_num_rows($stmt) == 1) { // Check if email exists, if yes then verify password
                    // echo nl2br("Email exists\n");
                    mysqli_stmt_bind_result($stmt, $id, $name, $hashed_password, $is_admin); // Bind result variables
                    if (mysqli_stmt_fetch($stmt)) {
                        // echo nl2br("mysqli_stmt_fetch done\n");
                        if (password_verify($password, $hashed_password)) {
                            // echo nl2br("User Login Successfully\n");
                            session_start(); // Password is correct, so start a new session
                            $_SESSION["id"] = $id; // Store data in session variables
                            $_SESSION["email"] = $email;
                            $_SESSION["name"] = $name;
                            $_SESSION["is_admin"] = $is_admin;
                            header("location: main/dashboard.php");
                            die();
                        } else {
                            // echo nl2br("The password you entered was not valid\n");
                            $form_signin_err = "The password you entered was not valid."; // Display an error message if password is not valid
                        }
                    }
                } else {
                    // echo nl2br("Email not exist\n");
                    $form_signin_err = "No account found with that email."; // Display an error message if email doesn't exist
                }
            } else {
                // echo nl2br("Failed to execute the prepared statement\n");
                $form_signin_err = "Oops! Something went wrong. Contact Admin."; // Display an error message 
            }
        }
        mysqli_stmt_close($stmt); // Close statement
    }
    mysqli_close($link); // Close connection
}
?>


<!doctype html>
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
        <link rel="stylesheet" type="text/css" href="style/styles.css">
        <!-- favicon -->
        <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    </head>
    <body>
        <div class="container text-center">
            <form class="form-signin  mt-5" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <img class="mb-4" src="img/login_icon.png" alt="" width="72" height="72">
                <h1 class="h3 mb-3 font-weight-normal">Please Login</h1>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="email" name="email" class="form-control mb-3" placeholder="Email address" required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <!-- <div class="checkbox mb-3"><label><input type="checkbox" value="remember-me"> Remember me</label></div> -->
                <input type="submit" class="btn btn-lg btn-primary btn-block" value="Login">
                <?php if($form_signin_err!=""){ echo "<div class='full_form_err'>" . " $form_signin_err " . "</div>"; } ?>
            </form>
        </div>
    </body>
</html>
