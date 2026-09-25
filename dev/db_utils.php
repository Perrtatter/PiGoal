<?php

// connect
$connection_string = "host=$host port=$port dbname=$dbname user=$username password=$password";
$dbconn = pg_connect($connection_string);


// simple fetch ( 1 row output )
$query = 'select nbr_point from "user" where username = ' . "'$username'";
$result = pg_query($dbconn, $query);

$row = pg_fetch_assoc($result);
echo $row["nbr_point"];


// complexe fetch ( many output )
$query = "SELECT DISTINCT c.nom FROM public.category c
        INNER JOIN \"user\" u ON u.id = c.user_id
        WHERE u.username = '$username'";

$result = pg_query($dbconn, $query);

// for like
while ($row = pg_fetch_assoc($result)) {
    echo $row['nom'];
}

/*
// 4. Check if any categories were found
if (pg_num_rows($result) === 0) {
    echo "No categories found for user $username.";
} 
*/

// Free memory result
pg_free_result($result);

?>