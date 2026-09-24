<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal App DB | Create Account</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="../compenents/toast/toast.css">
    <script src="../compenents/toast/toast.js"></script>
    <script src="../compenents/get_env/get_env.js"></script>
    <script src="../compenents/send_post/send_post.js"></script>

    <link rel="shortcut icon" href="../assets/diamond.png" type="image/png">
</head>
<body>
    <div align="center">
        <img src="../assets/diamond.png" id="logo">
        <br><br>

        <form method="post">
            <input type="text" placeholder="Username :" name="username"><br>
            <input type="password" placeholder="Password :" name="password"><br>
            <input type="password" placeholder="Confirm :" name="confirm"><br>

            <button type="submit">Create</button>
            <div id="toast-container"></div>
        </form>

        <?php
            // import env
            require_once __DIR__ . '/../compenents/get_env/get_env.php';
            $db_host = get_env("../","host");
            $db_port = get_env("../","port");
            $db_password = get_env("../","password");
            $db_username = get_env("../","username");
            $db_name = get_env("../","dbname");
            

            // get creds
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $confirm = $_POST['confirm'];

                # compare
                if ($password != $confirm){
                    echo "<script>toast('Password not matching.', 'error')</script>";
                }

                else{
                    // connect 
                    $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
                    $dbconn = pg_connect($connection_string);

                    // insert
                    $password_hash = hash("sha256",$password);

                    $query = "INSERT INTO \"user\"(username, password_hash) VALUES ('$username', '$password_hash');";
                    $result = pg_query($dbconn, $query);

                    // redirect
                    echo "<script>document.location.href = '../index.php';</script>";
    
                }
            }
        ?>
    </div>
</body>
</html>