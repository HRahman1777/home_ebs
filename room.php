<?php
if (!session_start()) session_start();
if (!isset($_SESSION['user_id']) || isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
}
require "backend/db_conn.php";

if (!isset($_SESSION['month'])) {
    header("Location: calculate.php?reset=1");
}


$userid =  $_SESSION['user_id'];
$month = $_SESSION['month'];
$month_unit_id = $_SESSION['month_unit_id'];

$my_query = "SELECT default_room FROM users WHERE id='$userid'";
$result = mysqli_query($conn, $my_query) or die("Query Failed");
$row = mysqli_fetch_array($result);
if ($row['default_room'] > 0) {
    $_GET['total_rooms'] = $row['default_room'];

    $prev_month = date('Y-m', strtotime($month . " - 1 month"));

    $my_query2 = "SELECT * FROM `meter_units` WHERE `user_id`='$userid' AND `month`='$prev_month' AND `is_active`='1'";
    $result2 = mysqli_query($conn, $my_query2) or die("Query Failed");
    $result2 = mysqli_fetch_array($result2);
    $result2 = $result2['id'];

    @$room_info_name = [];
    @$room_info_unit = [];
    $my_query3 = "SELECT * FROM `bills` WHERE `meter_unit_id`='$result2' AND `is_active`='1'";
    $result3 = mysqli_query($conn, $my_query3) or die("Query Failed");
    $num_rows = mysqli_num_rows($result3);
    if ($num_rows < 1) {
        for ($i = 0; $i < $row['default_room']; $i++) {
            $room_info_name['serial' . $i + 1] =  $i + 1;
            $room_info_unit['serial' . $i + 1] = 0;
        }
    } else {
        while ($rows = mysqli_fetch_array($result3)) {
            $room_info_name[$rows['room']] = $rows['name'];
            $room_info_unit[$rows['room']] = $rows['unit'];
        }
    }

    //var_dump($room_info_name);
    //var_dump($room_info_unit);
}

if (isset($_POST['set_room_det'])) {
    @$rooms = $_POST['rooms'];
    @$names = $_POST['names'];
    @$units = $_POST['units'];

    $total_tk = $_SESSION['total_tk'];
    $total_units = 0;

    for ($i = 0; $i < count($units); $i++) {
        $float = floatval($units[$i]);
        $unit_after_minus = $float - floatval($room_info_unit[$rooms[$i]]);
        $total_units += $unit_after_minus;
    }
    $tk_per_unit = $total_tk / $total_units;
    $tk_per_unit = round($tk_per_unit, 2);
    $mk_val = " ";
    for ($i = 0; $i < count($units); $i++) {
        $float = floatval($units[$i]);
        $unit_after_minus = $float - floatval($room_info_unit[$rooms[$i]]);
        $bill = $unit_after_minus * $tk_per_unit;
        $mk_val .= "('$month_unit_id','$userid','$rooms[$i]','$names[$i]','$float','$month', '$tk_per_unit', '$unit_after_minus', '$total_units', '$bill'),";
    }
    $mk_val = rtrim($mk_val, ",");

    /*
    $my_query = '';
    $chk = "SELECT * FROM `bills` WHERE `user_id`='$user_id' AND `month`='$month' AND `is_active`='1';";
    $chk_res = mysqli_query($conn, $chk) or die("Query Failed");
    if (mysqli_num_rows($chk_res) > 0) {
        $my_query = "UPDATE `bills` SET `name`='$cnt',`tk`='$res' WHERE `user_id`='$user_id' AND `meter_unit_id`='$month_unit_id' AND `is_active`='1'";
    } else {
        
    }*/

    $my_query = "INSERT INTO `bills`(`meter_unit_id`, `user_id`, `room`, `name`, `unit`, `month`, `tk_per_unit`, `t_month_unit`, `t_month_t_unit`, `t_month_bill`) 
    VALUES" .  $mk_val;
    $result = mysqli_query($conn, $my_query) or die("Insert Failed");
    $urll = "report.php?repid=" . $month_unit_id . "&tk=" . $total_tk;
    header('Location: ' . $urll);
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
                    <h2 class="text-center">Room + Unit Set</h2>
                    <hr>
                    <div class="card mb-2">
                        <div class="card-header text-center">Meter Setup Details</div>
                        <div class="card-body  p-4">

                            <?php
                            echo "<b>Month: </b>" . $_SESSION['month'] . '<br>';
                            echo "<b>Meter Wise TK: </b>" . $_SESSION['month_unit'];
                            echo "<b>Total TK: </b>" . $_SESSION['total_tk'];
                            ?>
                            <p class="text-center text-danger"> Want to reset month and meters value....
                                <a class="btn btn-sm btn-outline-warning" href="calculate.php?reset=1">Reset?</a>
                            </p>
                        </div>
                    </div>
                    <?php
                    if (!isset($_GET['total_rooms'])) {
                    ?>
                        <div class="row">
                            <div class="col-1"></div>
                            <div class="col">
                                <div class="card">
                                    <div class="card-header text-center">
                                        Total Room To Calculate???
                                    </div>
                                    <div class="card-body  p-4">
                                        <form class="" action="" method="get">
                                            <div class="mb-3">
                                                <label for="unit_values" class="form-label">Total Rooms</label>
                                                <input type="number" name="total_rooms" class="form-control" id="unit_values" required>
                                            </div>
                                            <input type="submit" class="btn btn-outline-primary" value="Set">
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2"></div>
                        </div>
                    <?php
                    } else if (isset($_GET['total_rooms'])) {
                        $cnt = $_GET['total_rooms'];
                    ?>
                        <div class="text-center mb-2" hidden>
                            <a href="room.php" class="btn btn-outline-danger">Reset Total Room</a>
                        </div>
                        <form class="" action="" method="post">
                            <?php
                            for ($i = 0; $i < $cnt; $i++) {
                            ?>
                                <div class="row">
                                    <div class="col-1"></div>
                                    <div class="col">
                                        <div class="card p-4 mb-2">
                                            <h3>Serial - <?php echo $i + 1; ?></h3>
                                            <div class="mb-1" hidden>
                                                <label for="" class="form-label">Room No</label>
                                                <input type="text" name="rooms[]" class="form-control" value="<?php echo 'serial' . $i + 1; ?>" id="">
                                            </div>
                                            <div class="mb-1">
                                                <label for="" class="form-label">Name</label>
                                                <input type="text" name="names[]" class="form-control" value="<?php echo $room_info_name['serial' . $i + 1]; ?>" placeholder="room name/no" id="" required>
                                            </div>
                                            <div class="mb">
                                                <label for="" class="form-label">Unit</label>
                                                <input type="text" name="units[]" class="form-control" placeholder="unit" id="" required>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="text-center">
                                <input type="submit" class="btn btn-warning" name="set_room_det" value="Calculate">
                            </div>
                        </form>
                    <?php
                    }
                    ?>
                    <div style="margin-bottom: 10rem;"></div>
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