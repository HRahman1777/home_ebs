<?php
if (!session_start()) session_start();
if (!isset($_SESSION['user_id']) || isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
}
require "backend/db_conn.php";
$user_id = $_SESSION['user_id'];
$unit_month_id = $_GET['repid'];
$ttk = $_GET['tk'];

$query = "SELECT * FROM `bills` WHERE `meter_unit_id`='$unit_month_id' AND `user_id`='$user_id' AND `is_active`='1'";
$chk_res = mysqli_query($conn, $query) or die("Query Failed");
$res_header = mysqli_query($conn, $query) or die("Query Failed");
$res_header = mysqli_fetch_array($res_header);
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
                    <h2 class="text-center">Record - [<a href="home.php">All Records</a>]</h2>
                    <hr>
                    <div class="d-flex justify-content-center">
                        <div class="card text-center" style="width: 25rem;">
                            <div class="card-header">
                                <b> <?php echo isset($res_header['month']) ? $res_header['month'] . '<br>' : '--<br>'; ?></b>
                            </div>
                            <div class="card-body">
                                All Meter's Bill - <b><?php echo $ttk . "tk<br>"; ?> </b>
                                Total Unit - <b><?php echo isset($res_header['t_month_t_unit']) ? $res_header['t_month_t_unit'] . '<br>' : '--<br>' ?></b>
                                Tk Per Unit - <b><?php echo isset($res_header['tk_per_unit']) ? $res_header['tk_per_unit'] . 'tk<br>' : '--<br>' ?> </b>
                            </div>
                        </div>
                    </div>

                    <table class="table text-center table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Serial No</th>
                                <th scope="col">Room</th>
                                <th scope="col">Previous Unit</th>
                                <th scope="col">Current Unit</th>
                                <th scope="col">Counted Unit</th>
                                <th scope="col">Bills</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($chk_res) > 0) {
                                $xx = 1;
                                while ($rows = mysqli_fetch_array($chk_res)) {
                                    echo "<tr>";
                                    echo "<th scope='row'>" . $xx++ . "</th>";
                                    echo "<td>" . $rows['room'] . "</td>";
                                    echo "<td>" . $rows['name'] . "</td>";
                                    echo "<td>" . $rows['unit'] - $rows['t_month_unit'] . "</td>";
                                    echo "<td>" . $rows['unit'] . "</td>";
                                    echo "<td>" . $rows['t_month_unit'] . "</td>";
                                    echo "<th class='text-success'>" . $rows['t_month_bill'] . "</th>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='8'>No Records</td>";
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