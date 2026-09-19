<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal App DB | Login</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="compenents/toast/toast.css">
    <script src="compenents/toast/toast.js"></script>

    <link rel="shortcut icon" href="assets/diamond.png" type="image/png">
</head>
<body>
    <div align="center">
        <img src="assets/diamond.png" id="logo">
        <br><br>

        <form action="index.php" method="post">
            <input type="text" placeholder="Username :" name="username"><br>
            <input type="password" placeholder="Password :" name="password"><br>

            <button type="submit">Login</button>
            <div id="toast-container"></div>
        </form>

        <?php
            // get creds
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $password = $_POST['password'];
            
            
            // connect
            $connection_string = "host=localhost port=5432 dbname=goal_app_db user=postgres password=password";
            $dbconn = pg_connect($connection_string);

            // 1. Query only the user matching the provided username
            $query = 'SELECT password_hash FROM "user" WHERE username = ' ."'$username'";

            // 2. Execute safely with parameters
            $result = pg_query($dbconn, $query);

            if ($result && pg_num_rows($result) > 0) {
                // 3. Fetch the row (no loop needed since usernames should be unique)
                $row = pg_fetch_assoc($result);
                
                if ($row["password_hash"] == hash("sha256",$password)){
                    echo "<script'>toast('Correct password.', 'success')</script>";

                    // redirect to dashboard
                    echo "<script>document.location.href = 'dashboard/index.php?p=zi3di(ufe31ck433Klls&user=$username'</script>";
                }

                else{
                    echo "<script>toast('Wrong password.', 'error')</script>";
                }
        
            } else {
                echo "<script>toast('User not found.', 'error')</script>";
            }

            // Free memory result
            pg_free_result($result);
            }
        ?>
    </div>
</body>
</html>