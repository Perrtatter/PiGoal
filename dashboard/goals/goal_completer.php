<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
<body style="background-color:rgb(44,44,44);">
    <?php
        // import env
        require_once __DIR__ . '/../../compenents/get_env/get_env.php';
        $db_host = get_env("../../","host");
        $db_port = get_env("../../","port");
        $db_password = get_env("../../","password");
        $db_username = get_env("../../","username");
        $db_name = get_env("../../","dbname");

        // get params
        $goal_id = $_POST["goal_id"];
        $username = $_POST["username"];
        $category = $_POST["category"];
        $goal_type = $_POST["goal_type"];

        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        // complete goal in db
        $query_complete = "update goal set is_complete=true where id=" . $goal_id;
        $result = pg_query($dbconn, $query_complete);

        // add point 
        //$query = 'update "user" set nbr_point=nbr_point+' . $goal_point_dict[$goal_type] . " where username='" . $username . "';";
        $query_point = 'update "user" set nbr_point=nbr_point+' . 125-($goal_type*25) . " where username='" . $username . "';";
        $result = pg_query($dbconn, $query_point);

        // 6. Free the result memory
        pg_free_result($result);

    
        // complete or not category
        // execute
        // 1. Parameterized SELECT Query (Replaced variables with $1 and $2)
        $query_category = 'SELECT count(CASE WHEN g.is_complete = true THEN 1 END) AS complete_count,count(g.*) AS total_count
                        FROM public.goal g 
                        INNER JOIN category c ON g.id = c.goal_id 
                        WHERE c.nom = $1
                            AND c.user_id = (SELECT id FROM "user" WHERE username = $2);';

        // Execute directly with parameters in a single step
        $result = pg_query_params($dbconn, $query_category, array($category, $username));
        $row = pg_fetch_assoc($result);

        // Using the explicit alias "total_count" defined in the SELECT statement
        if ((int)$row["complete_count"] == (int)$row["total_count"]) {
            
            // 2. Parameterized UPDATE Query (Replaced variables with $1 and $2)
            $query_update = 'UPDATE category 
                            SET is_complete = true 
                            WHERE nom = $1 
                            AND user_id = (SELECT id FROM "user" WHERE username = $2);';

            // Execute the update query safely
            $update_result = pg_query_params($dbconn, $query_update, array($category, $username));
        }

        // 3. Go back to page (Cleaned up URL parameter encoding)
        $data = json_encode(array(
            "category"=>$category,
            "username"=>$username
        ));

                    
        echo "<script>send_post('index.php',$data)</script>";        
    ?>

</body>
</html>