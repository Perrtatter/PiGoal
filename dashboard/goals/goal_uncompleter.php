<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
<body>
    <?php
        // import env
        require_once __DIR__ . '/../../compenents/get_env/get_env.php';
        $db_host = get_env("../../","host");
        $db_port = get_env("../../","port");
        $db_password = get_env("../../","password");
        $db_username = get_env("../../","username");
        $db_name = get_env("../../","dbname");

        // get params
        $goal_id = $_GET["goal_id"];
        $username = $_GET["username"];
        $category = $_GET["category"];
        $goal_type = $_GET["goal_type"];

        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        // complete goal in db
        $query_complete = "update goal set is_complete=false where id=" . $goal_id;
        $result = pg_query($dbconn, $query_complete);

        // add point 
        //$query = 'update "user" set nbr_point=nbr_point+' . $goal_point_dict[$goal_type] . " where username='" . $username . "';";
        $query_point = 'update "user" set nbr_point=nbr_point-' . 125-($goal_type*25) . " where username='" . $username . "';";
        $result = pg_query($dbconn, $query_point);

        // 6. Free the result memory
        pg_free_result($result);
        

        // go back on page 
        echo '<script>document.location.href = "index.php?username=' . $username . "&category=" . $category . '"</script>';

        /*
                // complete or not category
        // execute
        $query = "SELECT count(g.*) 
                FROM public.goal g 
                INNER JOIN category c ON g.id = c.goal_id 
                WHERE c.nom = $category
                    AND c.user_id = (SELECT id FROM \"user\" WHERE username = $username) 
                    AND g.is_complete = true;";


        $result = pg_execute($dbconn, "count_completed_goals", array($category, $username));
        $row = pg_fetch_assoc($result);

        if ($row["count"] == 4){
            // update category
            $query = "UPDATE category 
                SET is_complete = true 
                WHERE nom = $category_name 
                AND user_id = (SELECT id FROM \"user\" WHERE username = $username)";

            $result = pg_query($dbconn, $query);
        }
        */
    ?>

</body>
</html>