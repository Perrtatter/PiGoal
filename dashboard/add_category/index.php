<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Dashboard</title>
    <link rel="shortcut icon" href="../../assets/diamond.png" type="image/png">
    <link rel="stylesheet" href="/style.css">

    <link rel="stylesheet" href="../../compenents/toast/toast.css">
    <script src="../../compenents/toast/toast.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
</head>
<body>
    <div align="center">
        <div class="header">
            <div class="flex-container-invisible">
                <div id="go_back_div"></div>
                <div id="theme_switcher_div"></div>
            </div>
        </div>
        <div id="logo_bg" style="">
            <img src="/assets/logo.png" id="logo">
        <div id="logo_glow"></div>
        </div>
    </div>

    <div align="center">
    <div id="toast-container"></div>

    <div id="menu-container"></div>

    <?php
        // import env
        require_once __DIR__ . '/../../compenents/get_env/get_env.php';
        $db_host = get_env("../../","host");
        $db_port = get_env("../../","port");
        $db_password = get_env("../../","password");
        $db_username = get_env("../../","username");
        $db_name = get_env("../../","dbname");

        // check pass
        if ($_POST["token"] != "zi3di(ufe31ck433Klls"){
            echo "<script>alert('Please Login !');document.location.href = '/'</script>";
        }

        else{
            $username = $_POST["user"];
            echo "<h1 style='margin-top:10px; font-size:40px; background: linear-gradient(182deg, #ffffff, #b4b4b4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;' id='hello_h1'>Hello <span style='background: linear-gradient(179deg, #6fe9d0, #129dd4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>$username</span></h1>";
        }
    ?>

    <!-- the form -->
    <form>
        <input class="input" type="text" style="text-align:center;" placeholder="Name :" name="category_name"><br>
        <button class="glow_button" type="submit">+</button>
     </form>

    <footer>
            <p id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
    </footer>
    </div>
    <script type="module">
        import { CreateThemeSwitcher } from "../../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../../compenents/go_back/create_go_back.js";
        
        CreateThemeSwitcher();
        
        const goBackData = <?php echo json_encode(array("user" => $username, "token" => "zi3di(ufe31ck433Klls")); ?>;
        CreateGoBack("../index.php", goBackData);
    </script>
</body>
</html>