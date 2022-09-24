<?php
if (!session_start()) session_start();
require "backend/db_conn.php";

if (isset($_POST['submit'])) {

    @$user_id = $_POST['userid'];
    @$passwd = $_POST['password'];

    if (empty($user_id)) {
        @$_SESSION['MESSAGE'] = @$message .= "ID is blank ." . "<br>";
        @$error = true;
    }
    if (empty($passwd)) {
        @$_SESSION['MESSAGE'] = @$message .= "Password field can not be blank ." . "<br>";
        @$error = true;
    }
    if ($user_id != '' && $passwd != '') {
        $my_query = "SELECT * FROM users WHERE user_id='$user_id'";
        $result = mysqli_query($conn, $my_query) or die("Query Failed");
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $passwd = md5($passwd);
            if ($row['user_id'] == $user_id && $row['password'] == $passwd) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $user_id;
                header("Location: home.php");
            } else {
                @$_SESSION['MESSAGE'] = @$message .= "ID, Password Doesn't Match." . "<br>";
                @$error = true;
            }
        } else {
            @$_SESSION['MESSAGE'] = @$message .= "ID Doesn't Exist." . "<br>";
            @$error = true;
        }
    }
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
    <title>EBS - Login</title>
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center">Login</h1>
        <hr>
        <div class="d-flex justify-content-center">
            <div class="card p-4">
                <form class="" action="" method="post">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">ID</label>
                        <input type="text" name="userid" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                    </div>
                    <input type="submit" class="btn btn-outline-primary" name="submit" value="Login">
                </form>
            </div>
        </div>
        <?php
        if (isset($error)) { ?>
            <div class="d-flex justify-content-center">
                <div class="mt-4 alert alert-danger alert-dismissible fade show" style="width: 25rem;" role="alert">
                    <strong>Failed!</strong> <?php echo $_SESSION['MESSAGE'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <script src="js/bootstrap.js"></script>
    <script src="js/index.js"></script>
</body>

</html>

<?php
require "backend/footer.php";
?>