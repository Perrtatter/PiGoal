---------------------
-- (1) Diamand  :  100
-- (2) Or       :  75
-- (3) Argent   :  50
-- (4) Cuire    :  25
---------------------

-- avoir c et g nom
---------------------
select q.nom, c.nom from public.category c
inner join goal q on q.id = c.goal_id


-- avoir c par u nom
---------------------
select distinct c.nom from public.category c
inner join "user" u on u.id = c.user_id
where u.username = 'Mathys' -- $username


-- avoir g par c et u nom
---------------------
SELECT g.nom,g.type,g.is_complete FROM public.goal g
inner join category c on g.id = c.goal_id

where c.nom = 'Gym' -- $category_name
and c.user_id = ( select id from "user" where username = 'Mathys' /* $username */ )
order by type asc


-- suppreîmer c
---------------------
delete from category where nom = 'Gym' -- $category_name


-- ajouter c et q
---------------------
/*
insert into q 
insert into c ( select id limit 1 )+1,+1,+1,+1
*/


-- ajouter des points 
---------------------
update "user" set nbr_point=25 where username='Mathys' -- $username


-- completer goal 
---------------------
update goal set is_complete=true where id=$goal_id;


-- avoir p par g_id
---------------------
select type from goal where id=$goal_id;
-- voir la table des point en haut


        /*
        // fetch all category for user 
        // connect
        $connection_string = "host=localhost port=5432 dbname=goal_app_db user=postgres password=password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT DISTINCT c.nom FROM public.category c
                INNER JOIN \"user\" u ON u.id = c.user_id
                WHERE u.username = '$username'";

        // 2. Execute the query
        $result = pg_query($dbconn, $query);


        // 4. Check if any categories were found
        if (pg_num_rows($result) === 0) {
            echo "No categories found for user $username.";
        } 
        
        else {
            echo "<ul>";
            
            // 5. Loop through and print each category name
            while ($row = pg_fetch_assoc($result)) {
                // echo "<li>" . htmlspecialchars($row['nom']) . "<br><button class='edit_cal_btn'>✏️</button><button class='del_cal_btn'>🗑️</button></li>";
                echo "<li>" . htmlspecialchars($row['nom']) . "<br></li>";

            }
            
            echo "</ul><br>";
        }

        // 6. Free the result memory
        pg_free_result($result);
        */