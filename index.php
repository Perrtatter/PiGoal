<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Login</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="compenents/toast/toast.css">
    <script src="compenents/toast/toast.js"></script>
    <script src="compenents/get_env/get_env.js"></script>
    <script src="compenents/send_post/send_post.js"></script>

    <link rel="shortcut icon" href="assets/logo.png" type="image/png">

    <script type="module" defer>
        import { CreateThemeSwitcher } from "/compenents/theme_switcher/create_theme_switcher.js"
        import { CreateGoBack } from "/compenents/go_back/create_go_back.js"
        CreateThemeSwitcher()
        CreateGoBack()
    </script>

</head>
<body>
    <div align="center">
        <div class="header">
            <div class="flex-container-invisible">
                <div id="go_back_div" style="opacity:0;"></div>
                <div id="theme_switcher_div"></div>
            </div>
        </div>
        <img src="assets/logo.png" id="logo">
        <div id="logo_glow"></div>
        <h1 style="margin-top:-10px; font-size:70px; background: linear-gradient(182deg, #ffffff, #b4b4b4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Pi<span style="background: linear-gradient(179deg, #6fe9d0, #129dd4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Goal</span></h1>
        <form action="index.php" method="post" style="margin-top:-30px;">
            <input class="input" type="text" style="background-image: url('./assets/icons/user.svg');" placeholder="Username :" name="username"><br>
            <input class="input" type="password" style="background-image: url('./assets/icons/key.svg');" placeholder="Password :" name="password"><br>
            <button class="glow_button" type="submit">Login</button>
            <div id="toast-container"></div>
        </form>
        <p id="OrAccount"> or </p>
        <button onclick="document.location.href = 'create_account/'" class="button">Create Account</button>

        <?php
            // import env
            require_once __DIR__ . '/compenents/get_env/get_env.php';
            $db_host = get_env("./","host");
            $db_port = get_env("./","port");
            $db_password = get_env("./","password");
            $db_username = get_env("./","username");
            $db_name = get_env("./","dbname");
            

            // get creds
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $password = $_POST['password'];
            
                // connect
                $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
                $dbconn = pg_connect($connection_string);

                // 1. Query only the user matching the provided username
                $query = 'SELECT password_hash FROM "user" WHERE username = ' ."'$username'";

                // 2. Execute safely with parameters
                $result = pg_query($dbconn, $query);

                if ($result && pg_num_rows($result) > 0) {
                    // 3. Fetch the row (no loop needed since usernames should be unique)
                    $row = pg_fetch_assoc($result);
                    
                    if ($row["password_hash"] == hash("sha256",$password)){
                        echo "<script>toast('Correct password.', 'success')</script>";

                        // redirect to dashboard
                        $data = json_encode(array(
                            "token"=>"zi3di(ufe31ck433Klls",
                            "user"=>$username
                        ));

                        
                        echo "<script>send_post('dashboard/index.php',$data)</script>";
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

        <footer>
            <p id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
            <p>Version : <strong>2.beta.12</strong></p>
        </footer>
    </div>
</body>
</html>