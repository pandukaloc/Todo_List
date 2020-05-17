<?php
session_start(); // Initialize the session
require_once "../database/config.php";

if (!isset($_SESSION["id"])) { // Check if the user is logged in, if not then redirect him to login page
  header("location: ../index.php");
  exit;
}

$sql = "SELECT `id`, `task_name`, `priority`, `deadline`, `details`, `created_user`, `created_at` FROM `tasks`"; // Prepare an insert statement

if(!($result=mysqli_query($link, $sql))){ // Execute the SELECT Query
  echo 'Retrieval of data from Database Failed';
}else{

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
  <!-- Data Table -->
  <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <!-- Custom styles-->
  <link rel="stylesheet" type="text/css" href="../style/styles.css">
  <!-- favicon -->
  <link rel="icon" type="image/png" href="../img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="../img/favicon-16x16.png" sizes="16x16" />
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand ml-5 mr-5" href="#"><?php echo trim($_SESSION["name"]) ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active ml-4">
          <a class="nav-link" href="#" class="disabled">Task List</a>
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
  <div class="container-fluid text-center">
    <h3>Task List</h3>
    <table id="task_table" class="display">
      <thead><tr><th>Task Name</th><th>DeadLine</th><th>Priority</th><th>Created User</th></tr></thead>
      <tbody>
        <?php
          if(mysqli_num_rows($result)>0){
            while ($row = mysqli_fetch_array($result)) {
              echo "<tr>";
              echo "<td>" . $row['task_name'] . "</td>";
              echo "<td>" . $row['deadline'] . "</td>";
              echo "<td>" . $row['priority'] . "</td>";
              echo "<td>" . $row['created_user'] . "</td>";
              echo "</tr>";
            }
          }
        ?>
      </tbody>
    </table>
  </div>
  <script>
    $(document).ready(function() {
      $('#task_table').DataTable();
    });
  </script>

</body>

</html>