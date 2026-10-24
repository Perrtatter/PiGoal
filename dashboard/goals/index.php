<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Goals</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="../../assets/diamond.png" type="image/png">

    <link rel="stylesheet" href="../../compenents/toast/toast.css">
    <script src="../../compenents/toast/toast.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <div align="center">
         <div class="header">
            <div class="flex-container-invisible">
                <div id="go_back_div"></div>
                <div id="theme_switcher_div"></div>
            </div>
        </div>
        <div id="logo_bg">
            <img src="../../assets/logo.png" id="logo">
            <div id="logo_glow"></div>
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

        $category = (string)($_POST["category"] ?? "");
        $username = (string)($_POST["username"] ?? "");
        $token = (string)($_POST["token"] ?? "zi3di(ufe31ck433Klls");
        echo "<h1 id='hello_h1'>" . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . "</h1>";

        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT g.id, g.nom, g.type, g.is_complete, g.one_time, g.step
            FROM public.goal g
            INNER JOIN public.categorie c ON g.id = c.goal_id
            WHERE c.nom = $1
                AND c.user_id = (SELECT id FROM public.\"user\" WHERE username = $2)
                AND g.id <> 0
                AND g.type <> 0
            ORDER BY g.type ASC";

        $result = pg_query_params($dbconn, $query, array($category, $username));

        if (pg_num_rows($result) === 0) {
            echo "<p>Cette catégorie est vide. Ajoute ton premier objectif !</p>";
        } 
        else {
            $goal_data_dict = [
                1=> "../../assets/diamond.png",
                2=> "../../assets/gold.png",
                3=> "../../assets/iron.png",
                4=> "../../assets/copper.png"
            ];

            echo "<ul class='goal-list'>";
            
            while ($row = pg_fetch_assoc($result)) {
                $is_complete = $row['is_complete'] === "t";
                $classes = 'background ' . ($is_complete ? 'complete ' : '') . 'type' . (int)$row['type'];
                $data = array(
                    "username" => $username,
                    "category" => $category,
                    "goal_id" => (int)$row["id"],
                    "goal_type" => (int)$row["type"],
                    "is_complete" => $is_complete,
                );
                $data_json = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
                $delete_data_json = htmlspecialchars(json_encode([
                    "username" => $username,
                    "category" => $category,
                    "goal_id" => (int)$row["id"],
                    "token" => $token,
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $edit_data_json = htmlspecialchars(json_encode([
                    "goalId" => (int)$row["id"],
                    "name" => $row["nom"],
                    "type" => (int)$row["type"],
                    "oneTime" => $row['one_time'] === 't',
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $edit_button = '<button class="goal-action goal-edit" type="button" title="Modifier cet objectif" aria-label="Modifier cet objectif" data-goal="' . $edit_data_json . '">✏️</button>';
                $delete_button = '<button class="del_cal_btn goal-delete" type="button" title="Supprimer cet objectif" aria-label="Supprimer cet objectif" onclick=\'event.stopPropagation(); if (confirm("Supprimer cet objectif ?")) send_post("delete_goal.php", ' . $delete_data_json . ')\'>🗑️</button>';
                $goal_actions = '<span class="goal-actions">' . $edit_button . $delete_button . '</span>';
                $goal_name = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8');
                $goal_image = $goal_data_dict[(int)$row['type']];

                if ($row['one_time'] === "t") {
                    $action = $is_complete ? 'goal_uncompleter.php' : 'goal_completer.php';
                    echo "<li><div class=\"$classes\" id='goal_li_" . (int)$row['id'] . "' onclick='send_post(\"$action\", $data_json)'><img src='$goal_image' width=50><p>$goal_name</p>$goal_actions</div></li>";
                } else {
                    echo "<li><div class=\"$classes\" id='goal_li_" . (int)$row['id'] . "'><img src='$goal_image' width=50><p>$goal_name</p><input type='range' min=0 max=3 step=1 value='" . (int)$row['step'] . "' data-goal='$data_json' onchange='updateGoalStep(this)'>$goal_actions</div></li>";
                }

            }
            echo "</ul>";
        }
    ?>

    <button class="glow_button" onclick='send_post("create/index.php", <?php echo json_encode(array("username" => $username, "category" => $category, "token" => $token), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>)'>+</button><br>

    <dialog class="goal-dialog" id="edit-goal-dialog">
        <form method="post" action="edit_goal.php">
            <h2>Modifier l’objectif</h2>
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="goal_id">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="edit-goal-name">Nom</label>
            <input class="input" id="edit-goal-name" type="text" name="goal_name" required maxlength="150" autocomplete="off">
            <label for="edit-goal-type">Type</label>
            <select class="input" id="edit-goal-type" name="goal_type">
                <option value="1">Diamant</option>
                <option value="2">Or</option>
                <option value="3">Argent</option>
                <option value="4">Cuivre</option>
            </select>
            <label class="goal-one-time-label"><input type="checkbox" name="one_time"> Objectif ponctuel</label>
            <div class="goal-dialog-actions">
                <button class="goal-dialog-cancel" type="button" onclick="document.getElementById('edit-goal-dialog').close()">Annuler</button>
                <button class="goal-dialog-save" type="submit">Enregistrer</button>
            </div>
        </form>
    </dialog>

    <script>
        const goalDialog = document.getElementById('edit-goal-dialog');
        document.querySelectorAll('.goal-edit').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                const goal = JSON.parse(button.dataset.goal);
                goalDialog.querySelector('[name="goal_id"]').value = goal.goalId;
                goalDialog.querySelector('[name="goal_name"]').value = goal.name;
                goalDialog.querySelector('[name="goal_type"]').value = goal.type;
                goalDialog.querySelector('[name="one_time"]').checked = goal.oneTime;
                goalDialog.showModal();
                const nameInput = goalDialog.querySelector('[name="goal_name"]');
                nameInput.focus();
                nameInput.select();
            });
        });
        goalDialog.addEventListener('click', (event) => {
            if (event.target === goalDialog) goalDialog.close();
        });
    </script>

        <footer>
            <p style="pointer-events: none;" id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>
    <script type="module">
        import { CreateThemeSwitcher } from "../../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../../compenents/go_back/create_go_back.js";
        
        CreateThemeSwitcher();
        
        const goBackData = <?php echo json_encode(array("user" => $username, "token" => $token), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        CreateGoBack("../index.php", goBackData);
    </script>
</body>
</html>
