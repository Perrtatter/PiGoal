<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal App DB</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="../../assets/diamond.png" type="image/png">


    <link rel="stylesheet" href="../../compenents/toast/toast.css">
    <script src="../../compenents/toast/toast.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <div align="center">
        <div id="logo_bg">
            <img src="../../assets/diamond.png" id="logo">
        </div>
    </div>

    <div align="center">
    <div id="toast-container"></div>

    <?php
        // import env
        require_once __DIR__ . '/../../compenents/get_env/get_env.php';
        $db_host = get_env("../../","host");
        $db_port = get_env("../../","port");
        $db_password = get_env("../../","password");
        $db_username = get_env("../../","username");
        $db_name = get_env("../../","dbname");

        $category = $_POST["category"];
        $username = $_POST["username"];
        echo "<h1 id='hello_h1'>$category</h1>";

        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT g.id,g.nom,g.type,g.is_complete FROM public.goal g inner join category c on g.id = c.goal_id where c.nom = '$category' and c.user_id = ( select id from \"user\" where username = '$username' ) order by type asc";

        // 2. Execute the query
        $result = pg_query($dbconn, $query);


        // 4. Check if any goal were found
        if (pg_num_rows($result) === 0) {
            echo "No goals found for category $category.";
        } 

        else {
            // goal format dict data 
            $goal_data_dict = [
                1=> "../../assets/diamond.png",
                2=> "../../assets/gold.png",
                3=> "../../assets/iron.png",
                4=> "../../assets/copper.png"
            ];

            echo "<ul>";
            
            // 5. Loop through and print each goal name
            while ($row = pg_fetch_assoc($result)) {
                // gen data
                $data = json_encode(array(
                    "username"=>$username,
                    "category"=>$category,
                    "goal_id"=>$row["id"],
                    "goal_type"=>$row["type"]
                ));

                if ($row['is_complete'] == "t"){
                    echo "<li class='complete' onclick='send_post(" . '"' . "goal_uncompleter.php" . '",' . $data . ")' id='goal_li_" . $row['id'] . "'><img src='" . $goal_data_dict[$row['type']]  . "' width=50><p>" . htmlspecialchars($row['nom']) . "</p></li>";
                }

                else{
                    echo "<li onclick='send_post(" . '"' . "goal_completer.php" . '",' . $data . ")' id='goal_li_" . $row['id'] . "'><img src='" . $goal_data_dict[$row['type']]  . "' width=50><p>" . htmlspecialchars($row['nom']) . "</p></li>";
                }

            }
            
            // go back button 
            // gen data 
            $data = json_encode(array(
                "user"=>$username,
                "token"=>"zi3di(ufe31ck433Klls"
            ));

            echo "<button id='go_back_btn' onclick='send_post(" . '"' . "../index.php" . '",' . $data . ")'>Go back</button>";
            echo "</ul><br>";
        }
        
    ?>
    </div>

</body>
</html>