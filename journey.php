<?php
session_start();
if (!isset($_COOKIE['email'])) {
  header("Location:index.php");
}
include "connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<br>";

// $sql = "SELECT `name` FROM `".$table_name."`";
// $result = $conn->query($sql);
// while ($row = $result->fetch_assoc()) {
//   if($row['name'] == 'dushyant')
//   echo $row['name'];
// }

echo "<br>";

$expenses = 0;
$final = 0;

$table_name = $_SESSION["tablecode"];


if (isset($_POST["add"])) {                                    //here is what happens after 
  unset($_POST['add']);
  $name = trim($_POST["payer"]);                                // add payment button is clicked
  $payment = $_POST["payment"];
  //..............
  $find = "SELECT `code` FROM `" . $table_name . "` WHERE `name` = '{$name}'";
  $select = $conn->query($find) or die($conn->error);
  if ($select->num_rows) {
    $row = $select->fetch_array();
    $code = $row['code'];

    $sql = "SELECT `name`,`code`,`expenses` FROM `" . $table_name . "`";
    if ($result = $conn->query($sql)) {
      while ($row = $result->fetch_array()) {
        if ($row['code'] == $code) {
          $expenses = $row['expenses'];
          $final = $expenses + $payment;

          //updating expenses in journey table to particular code
          $sql = "UPDATE `" . $table_name . "` SET `expenses` = '{$final}' WHERE `code` = '{$code}'";
          if ($conn->query($sql)) {
          }
        }
      }
    } else {
      echo $conn->error;
    }
  } else {
    echo "no person of that name here";
  }
  //..............



}

if (isset($_POST["endjourney"])) {
  unset($_POST['endjourney']);
  $total = 0;
  $mean = 0;
  $count = 0;
  $final = 0;
  $sql = "SELECT `name`,`expenses` FROM `" . $table_name . "`";
  if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_array()) {
      $total = $total + $row['expenses'];
      $count = $count + 1;
    }
    $mean = $total / $count;
  }
  //calculating mean

  $sql = "SELECT `name`,`code`,`expenses` FROM `" . $table_name . "`";
  if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_array()) {
      $final = $row['expenses'] - $mean;
      $final = round($final, 2);
      $sql = "UPDATE `" . $table_name . "` SET `expenses` = '{$final}' WHERE `code` = '{$row["code"]}'";
      $conn->query($sql);
      $sql = "INSERT INTO `" . $row['code'] . "` (`groupname`,`groupcode`,`money`) VALUES ('" . $_SESSION['groupname'] . "','" . $table_name . "','" . $final . "')";
      $conn->query($sql);
      $conn->error;
    }
  }
  //updating values
  unset($_POST['endjourney']);
  header("Location:result.php");
}



?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Add Expenses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="css/project1_addexpense.css">
  <link rel="icon" href="images/logo.jpg" type="image/x-icon">
</head>

<body>

  <div class="main">
    <h3><?php echo $_SESSION['groupname']; ?> Member's Expenses</h3>
    <div class="tab">
      <table class='table table-striped'>
        <thead>
          <tr>
            <th scope='col'>Name</th>
            <th scope='col'>expenses</th>
          </tr>
        </thead>
        <tbody>
          <!-- PHP CODE TO FETCH DATA FROM ROWS-->
          <?php   // LOOP TILL END OF DATA 
          $result = $conn->query("SELECT `name`,`expenses` FROM `" . $table_name . "`") or die($conn->error);
          while ($rows = $result->fetch_assoc()) {
          ?>
            <tr>
              <!--FETCHING DATA FROM EACH 
                      ROW OF EVERY COLUMN-->
              <td><?php echo $rows['name']; ?></td>
              <td><?php echo $rows['expenses']; ?></td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
    <div class="container">
      <div class="title">Add Expenses</div>
      <!-- <div class="content"> -->
        <form action="#" method='post'>
          <!-- <div class="user-details"> -->
            <div class="input-box">
              <span class="details">Name</span>
              <input type="text" placeholder="Enter Your Name" name="payer">
            </div>
            <div class="input-box">
              <span class="details">Expenses</span>
              <input type="number" placeholder="0/-" name="payment">
            </div>
            <div class="button" id="up">
              <input type="submit" name='add' value="Enter">
            </div>

            <div class="button">
              <input type="submit" name='endjourney' value="End Journey">
            </div>

          <!-- </div> -->
        </form>
      <!-- </div> -->
    </div>
  </div>

  <!-- <div>
    <form action="" method="post">
      <input type="text" placeholder="name" name="payer">
      <input type="number" placeholder="0/-" name="payment">
      <input type="submit" value="add payment" name="add">
    </form>
  </div>
  <div>
    <form method="post">
      <button name="endjourney">End journey</button>
    </form>
  </div> -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</h