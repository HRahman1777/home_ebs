<?php
if (!session_start()) session_start();
if (!isset($_SESSION['user_id']) || isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
}
require "backend/db_conn.php";
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM `meter_units` WHERE `user_id`='$user_id' AND `is_active`='1'";
$chk_res = mysqli_query($conn, $query) or die("Query Failed");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        body {
            background-color: #f7ede361;
        }

        .color-black {
            color: black;
        }
    </style>
    <title>Home - EBS</title>
</head>

<body>
    <!-- top navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-white fixed-top" style="background-color: #bbded7">
        <div class="container-fluid">
            <button class="navbar-toggler navbar-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="offcanvasExample">
                <span class="navbar-toggler-icon" data-bs-target="#sidebar"></span>
            </button>
            <a class="navbar-brand me-auto ms-lg-0 ms-3 text-uppercase fw-bold color-black" href="home.php">EBS</a>
            <button class="navbar-toggler navbar-light" type="button" data-bs-toggle="collapse" data-bs-target="#topNavBar" aria-controls="topNavBar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavBar">
                <div class="d-flex ms-auto my-3 my-lg-0"></div>
                <ul class="navbar-nav">
                    <li>
                        <form action="" method="POST">
                            <input type="submit" value="Logout" name="logout" class="btn btn-sm btn-outline-danger" />
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- top navigation bar -->

    <!-- offcanvas -->
    <div class="offcanvas offcanvas-start sidebar-nav" tabindex="-1" id="sidebar" style="background-color: #bbded7ab">
        <div class="offcanvas-body p-0">
            <nav class="navbar-white">
                <ul class="navbar-nav">
                    <li>
                        <div class="text-muted small fw-bold text-uppercase px-3"></div>
                    </li>
                    <li>
                        <a href="home.php" class="nav-link px-3 color-black">
                            <span class="me-2"><i class="bi bi-speedometer2"></i></span>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="calculate.php" class="nav-link px-3 color-black">
                            <span class="me-2"><i class="bi bi-speedometer2"></i></span>
                            <span>Calculate</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="nav-link px-3 color-black">
                            <span class="me-2"><i class="bi bi-person-fill"></i></span>
                            <span>About</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- offcanvas -->

    <main class="mt-5 pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center">All Record of <?php echo $_SESSION['user_name'] ?></h2>
                    <hr>
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Month</th>
                                <th scope="col">Meter's Total Bill</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($chk_res) > 0) {
                                $xx = 1;
                                while ($rows = mysqli_fetch_array($chk_res)) {
                                    echo "<tr>";
                                    echo "<th scope='row'>" . $xx++ . "</th>";
                                    echo "<td>" . $rows['month'] . "</td>";
                                    echo "<td>" . $rows['tk'] . "</td>";
                                    echo "<td> <a class='btn btn-outline-dark' href='report.php?repid=" . $rows['id'] . "&tk=" . $rows['tk'] . "'>Show</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='4'>No Records</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="js/bootstrap.js"></script>
    <script src="js/index.js"></script>
</body>

</html>

<?php
require "backend/footer.php";
?>