<?php
if (!session_start()) session_start();
if (!isset($_SESSION['user_id']) || isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
}
require "backend/db_conn.php";

if (isset($_GET['reset'])) {
    if ($_GET['reset'] == 1) {
        unset($_SESSION['month']);
        unset($_SESSION['month_unit']);
        unset($_SESSION['total_tk']);
    }
}
if (isset($_SESSION['month'])) {
    ("Location: room.php");
}

if (isset($_POST['set_meter_values'])) {

    @$user_id = $_SESSION['user_id'];
    @$month = $_POST['meter_month'];
    @$values = $_POST['meter_values'];

    $all_variable_arr = explode(" ", $values);
    $ava_siz = count($all_variable_arr);
    $axa = "";
    for ($i = 0; $i < $ava_siz; $i++) {
        $axa .= ',' . $all_variable_arr[$i];
    }

    $all_variable = trim($axa, ',');
    $all_variable_arr = explode(",", $all_variable);
    $ava_siz = count($all_variable_arr);
    $res = 0;
    $cnt = 0;
    $month_unit = '<p class="text-danger">';
    for ($i = 0; $i < $ava_siz; $i++) {
        $all_variable_arr[$i] = trim($all_variable_arr[$i], ',');
        $all_variable_arr[$i] = trim($all_variable_arr[$i]);

        if ($all_variable_arr[$i] != "") {
            $month_unit .= $all_variable_arr[$i] . ',';
            $float = floatval($all_variable_arr[$i]);
            $res += $float;
            $cnt++;
        }
    }

    $my_query = '';
    $chk = "SELECT * FROM `meter_units` WHERE `user_id`='$user_id' AND `month`='$month' AND `is_active`='1';";
    $chk_res = mysqli_query($conn, $chk) or die("Query Failed");
    if (mysqli_num_rows($chk_res) > 0) {
        $my_query = "UPDATE `meter_units` SET `meter`='$cnt',`tk`='$res' WHERE `user_id`='$user_id' AND `month`='$month' AND `is_active`='1'";
    } else {
        $my_query = "INSERT INTO `meter_units`(`user_id`, `month`, `meter`, `tk`) VALUES ('$user_id','$month','$cnt','$res')";
    }
    $result = mysqli_query($conn, $my_query) or die("Insert Failed");

    $get_id_q = "SELECT id FROM `meter_units` WHERE `user_id`='$user_id' AND `month`='$month' AND is_active='1'";
    $get_id_r = mysqli_query($conn, $get_id_q) or die("Insert Failed");
    $row = mysqli_fetch_array($get_id_r);
    $_SESSION['month_unit_id'] = $row['id'];

    $_SESSION['month'] = $month;
    $_SESSION['month_unit'] = $month_unit . "</p>";
    $_SESSION['total_tk'] = $res;

    header("Location: room.php");
}
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
                    <h2 class="text-center">Calculate</h2>
                    <hr>
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col">
                            <div class="card p-4">
                                <form class="" action="" method="post">
                                    <div class="mb-3">
                                        <label for="mon" class="form-label">Select Month</label>
                                        <input type="month" name="meter_month" class="form-control" id="mon" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="unit_values" class="form-label">All Meters Value</label>
                                        <input type="text" name="meter_values" class="form-control" placeholder="with comma or space" id="unit_values" required>
                                    </div>
                                    <input type="submit" class="btn btn-outline-primary" name="set_meter_values" value="Save">
                                </form>
                            </div>
                        </div>
                        <div class="col-2"></div>
                    </div>
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