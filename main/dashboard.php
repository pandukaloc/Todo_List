<?php
session_start(); // Initialize the session
require_once "../database/config.php";

if (!isset($_SESSION["id"])) { // Check if the user is logged in, if not then redirect him to login page
  header("location: ../index.php");
  exit;
}

$sql = "SELECT `id`, `task_name`, `priority`, `deadline`, `created_user` FROM `tasks`"; // Prepare an insert statement
if (!($result = mysqli_query($link, $sql))) { // Execute the SELECT Query
  echo "<script>alert('Retrieval of data from Database Failed. Contact Admin');</script>";
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
  <!-- Data Table -->
  <!-- <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet"> -->
  <!-- <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->
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
          <a class="nav-link" class="disabled">Task List</a>
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
  <div class="container mt-5">
    <h3 class="text-center">Task List</h3>
    <div class="list-group">
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
          echo '<a href="task_view.php?id='. $row['id'] . '" class="list-group-item list-group-item-action">Task Name: ' . $row['task_name'] . '&#8287&#8287 | &#8287&#8287 Deadline:' . $row['deadline'] . '&#8287&#8287 | &#8287&#8287 Priority: ';
          for ($i = 1; $i<=$row['priority']; $i++) {
            echo '<span class="fa fa-star checked"></span>';
          }
          echo '</a>';
        }
      }
      ?>
    </div>

</body>

</html>