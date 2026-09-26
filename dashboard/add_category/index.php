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
    <script type="module">
        import { CreateThemeSwitcher } from "../../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../../compenents/go_back/create_go_back.js";
        
        CreateThemeSwitcher();
        
        const goBackData = <?php echo json_encode(array("user" => $_POST["user"], "token" => ["token"])); ?>;
        CreateGoBack("../index.php", goBackData);
    </script>

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

    <!-- the form -->
    <form method="post">
        <input class="input" type="text" style="text-align:center;" placeholder="Name :" name="category_name"><br>
        <br><br>

        <button onclick="toast('Not aviable yet','info')" type="button" class="button">+</button>
        <p id="OrAccount"> or </p>
        <button class="glow_button" type="submit">Done</button>
    </form>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo $_POST["category_name"];
            /*
            // gen data
            $data = json_encode([
                "user" => $username,
                "token" => $_POST["token"]
            ]);

            // redirect
            echo "<script>toast('Category created ( DEV )', 'success');send_post('../index.php', " . $data . ");</script>";
            */
            ;
        }
    ?>

    <footer>
            <p id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
    </footer>
    </div>
</body>
</html>