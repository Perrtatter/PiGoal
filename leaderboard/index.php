<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Leaderboard</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="leaderboard.css">
    <script src="../compenents/send_post/send_post.js"></script>
    <link rel="shortcut icon" href="../assets/logo.png" type="image/png">
</head>
<body class="leaderboard-page">
    <?php
        require_once __DIR__ . '/../compenents/get_env/get_env.php';
        $username = trim((string)($_POST['user'] ?? ''));
        $token = (string)($_POST['token'] ?? '');
        if ($token !== 'zi3di(ufe31ck433Klls' || $username === '') {
            echo "<script>alert('Please login again.');document.location.href = '../index.php';</script>";
            exit;
        }

        $db_host = get_env('../', 'host');
        $db_port = get_env('../', 'port');
        $db_password = get_env('../', 'password');
        $db_username = get_env('../', 'username');
        $db_name = get_env('../', 'dbname');
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);
        if ($dbconn === false) {
            http_response_code(500);
            exit('Could not connect to the database.');
        }

        $ranking_result = pg_query(
            $dbconn,
            'SELECT ROW_NUMBER() OVER (ORDER BY ranked.nbr_point DESC, ranked.id ASC) AS rank, ranked.id, ranked.username, ranked.nbr_point, ranked.profile_picture, ranked.total_goals, ranked.completed_goals
             FROM (
                 SELECT u.id, u.username, u.nbr_point, u.profile_picture,
                     COUNT(DISTINCT g.id) FILTER (WHERE g.id <> 0 AND g.type <> 0) AS total_goals,
                     COUNT(DISTINCT g.id) FILTER (WHERE g.id <> 0 AND g.type <> 0 AND g.is_complete = true) AS completed_goals
                 FROM public."user" u
                 LEFT JOIN public.categorie c ON c.user_id = u.id
                 LEFT JOIN public.goal g ON g.id = c.goal_id
                 GROUP BY u.id, u.username, u.nbr_point, u.profile_picture
             ) ranked
             ORDER BY ranked.nbr_point DESC, ranked.id ASC'
        );
        if ($ranking_result === false) {
            http_response_code(500);
            exit('Could not load the leaderboard.');
        }

        $ranking = [];
        while ($ranking_row = pg_fetch_assoc($ranking_result)) {
            $ranking_row['rank'] = (int)$ranking_row['rank'];
            $ranking_row['nbr_point'] = (int)$ranking_row['nbr_point'];
            $ranking_row['total_goals'] = (int)$ranking_row['total_goals'];
            $ranking_row['completed_goals'] = (int)$ranking_row['completed_goals'];
            $ranking[] = $ranking_row;
        }

        $top_three = array_slice($ranking, 0, 3);
        $podium_order = [1 => 2, 0 => 1, 2 => 3];
        $current_rank = 0;
        $max_points = max(array_map(static fn(array $entry): int => $entry['nbr_point'], $ranking) ?: [1]);
        $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $avatar_for = static function (array $entry): string {
            $picture = trim((string)($entry['profile_picture'] ?? ''));
            $legacy_avatars = [
                'alex' => '../assets/profile/avatars/lumen.svg',
                'steve' => '../assets/profile/avatars/ember.svg',
                'iron_golem' => '../assets/profile/avatars/alloy.svg',
            ];
            if (isset($legacy_avatars[$picture])) {
                return $legacy_avatars[$picture];
            }
            if (preg_match('#^/dashboard/uploads/avatars/[a-f0-9]{32}\.(?:webp|png)$#', $picture)) {
                return $picture;
            }
            if (preg_match('#^/assets/[a-zA-Z0-9_./-]+$#', $picture) && is_file(__DIR__ . '/..' . $picture)) {
                return '..' . $picture;
            }
            return '../assets/diamond.png';
        };
        foreach ($ranking as $entry) {
            if ($entry['username'] === $username) {
                $current_rank = $entry['rank'];
                break;
            }
        }
    ?>

    <?php require __DIR__ . '/../compenents/user_navigation.php'; ?>

    <main class="leaderboard-shell">
        <section class="leaderboard-heading">
            <span class="leaderboard-eyebrow">PIGOAL · SAISON EN COURS</span>
            <h1>Leaderboard</h1>
            <p>Les points gagnés en terminant tes objectifs.</p>
            <div class="leaderboard-you">Ton rang <strong>#<?php echo $current_rank > 0 ? $current_rank : '—'; ?></strong></div>
        </section>

        <?php if (count($top_three) > 0): ?>
            <section class="leaderboard-podium" aria-label="Podium">
                <?php foreach ($podium_order as $index => $place): ?>
                    <?php if (isset($top_three[$index])): ?>
                        <?php $entry = $top_three[$index]; $is_current = $entry['username'] === $username; ?>
                        <article class="podium-entry podium-place-<?php echo $place; ?> <?php echo $is_current ? 'is-current-user' : ''; ?>">
                            <div class="podium-avatar-wrap"><img src="<?php echo $escape($avatar_for($entry)); ?>" alt=""></div>
                            <span class="podium-place-label"><?php echo $place === 1 ? '1ER' : $place . 'E'; ?></span>
                            <strong class="podium-name"><?php echo $escape($entry['username']); ?></strong>
                            <span class="podium-points"><?php echo number_format($entry['nbr_point'], 0, ',', ' '); ?> pts</span>
                            <span class="podium-base" aria-hidden="true"></span>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <p class="leaderboard-empty">Aucun joueur dans le classement pour le moment.</p>
        <?php endif; ?>

        <section class="ranking-section">
            <div class="ranking-section-heading"><h2>Classement général</h2><span><?php echo count($ranking); ?> joueurs</span></div>
            <ol class="ranking-list">
                <?php foreach ($ranking as $entry): ?>
                    <?php
                        $is_current = $entry['username'] === $username;
                        $progress = $entry['total_goals'] > 0 ? (int)round(($entry['completed_goals'] / $entry['total_goals']) * 100) : 0;
                        $points_width = $max_points > 0 ? max(4, (int)round(($entry['nbr_point'] / $max_points) * 100)) : 4;
                    ?>
                    <li class="ranking-row <?php echo $is_current ? 'is-current-user' : ''; ?>">
                        <span class="ranking-number"><?php echo $entry['rank']; ?></span>
                        <img class="ranking-avatar" src="<?php echo $escape($avatar_for($entry)); ?>" alt="">
                        <div class="ranking-player">
                            <strong><?php echo $escape($entry['username']); ?><?php echo $is_current ? ' <span class="you-label">TOI</span>' : ''; ?></strong>
                            <span><?php echo $entry['completed_goals']; ?>/<?php echo $entry['total_goals']; ?> objectifs · <?php echo $progress; ?> %</span>
                        </div>
                        <div class="ranking-score">
                            <strong><?php echo number_format($entry['nbr_point'], 0, ',', ' '); ?></strong><span>pts</span>
                            <span class="ranking-score-track"><i style="width:<?php echo $points_width; ?>%"></i></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
    </main>

    <footer class="leaderboard-footer"><a href="https://github.com/Perrtatter/PiGoal">Github</a></footer>
    <script type="module">
        import { CreateThemeSwitcher } from "../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../compenents/go_back/create_go_back.js";
        CreateThemeSwitcher();
        const goBackData = <?php echo json_encode(['user' => $username, 'token' => $token], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        CreateGoBack("../dashboard/index.php", goBackData);
    </script>
</body>
</html>