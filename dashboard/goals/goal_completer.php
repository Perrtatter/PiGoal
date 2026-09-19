<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
<body>
    <?php
        // get params
        $goal_id = $_GET["goal_id"];
        $username = $_GET["username"];
        $category = $_GET["category"];
        $goal_type = $_GET["goal_type"];

        // connect
        $connection_string = "host=localhost port=5432 dbname=goal_app_db user=postgres password=password";
        $dbconn = pg_connect($connection_string);

        // complete goal in db
        $query = "update goal set is_complete=true where id=" . $goal_id;
        $result = pg_query($dbconn, $query);

        // add point 
        $goal_point_dict = [
            1=> 100,
            2=> 75,
            3=> 50,
            4=> 25
        ];

        $query = 'update "user" set nbr_point=nbr_point+' . $goal_point_dict[$goal_type];
        $result = pg_query($dbconn, $query);

        // 6. Free the result memory
        pg_free_result($result);
        

        // go back on page 
        echo '<script>document.location.href = "http://localhost:3000/dashboard/goals/index.php?username=' . $username . "&category=" . $category . '"</script>';
    ?>

</body>
</html>