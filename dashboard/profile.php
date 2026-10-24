<?php
require_once __DIR__ . '/../compenents/get_env/get_env.php';

$token = (string)($_POST['token'] ?? '');
$username = trim((string)($_POST['user'] ?? ''));

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

$account_result = pg_query_params(
    $dbconn,
    'SELECT id, username, password_hash, nbr_point, profile_picture FROM public."user" WHERE username = $1',
    [$username]
);
$account = $account_result !== false ? pg_fetch_assoc($account_result) : false;

if (!$account) {
    http_response_code(404);
    exit('User not found.');
}

$error = '';
$saved = false;
$avatar_options = [
    '/assets/profile/avatars/lumen.svg' => 'Lumen',
    '/assets/profile/avatars/ember.svg' => 'Ember',
    '/assets/profile/avatars/alloy.svg' => 'Alloy',
    '/assets/diamond.png' => 'Diamond',
    '/assets/gold.png' => 'Gold',
    '/assets/iron.png' => 'Iron',
    '/assets/copper.png' => 'Copper',
    '/assets/moon.png' => 'Moon',
    '/assets/logo.png' => 'PiGoal',
];
$legacy_avatar_paths = [
    'alex' => '/assets/profile/avatars/lumen.svg',
    'steve' => '/assets/profile/avatars/ember.svg',
    'iron_golem' => '/assets/profile/avatars/alloy.svg',
];
$resolve_avatar = static function (string $picture) use ($avatar_options, $legacy_avatar_paths): string {
    if (isset($legacy_avatar_paths[$picture])) {
        return $legacy_avatar_paths[$picture];
    }
    if (array_key_exists($picture, $avatar_options)) {
        return $picture;
    }
    if (preg_match('#^/dashboard/uploads/avatars/[a-f0-9]{32}\.(?:webp|png)$#', $picture)) {
        return $picture;
    }
    return '/assets/diamond.png';
};
$profile_picture = (string)($account['profile_picture'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $current_password = (string)($_POST['current_password'] ?? '');
    $new_username = trim((string)($_POST['new_username'] ?? ''));
    $new_password = (string)($_POST['new_password'] ?? '');
    $confirm_password = (string)($_POST['confirm_password'] ?? '');
    $avatar_choice = (string)($_POST['avatar_choice'] ?? 'keep');
    $new_picture = $profile_picture;
    $created_upload = '';

    if (!hash_equals((string)$account['password_hash'], hash('sha256', $current_password))) {
        $error = 'Le mot de passe actuel est incorrect.';
    } elseif ($new_username === '') {
        $error = 'Le nom d’utilisateur ne peut pas être vide.';
    } elseif ($new_username !== $username && !preg_match('/^[A-Za-z0-9_.-]{2,24}$/', $new_username)) {
        $error = 'Le pseudo doit contenir 2 à 24 caractères : lettres, chiffres, point, tiret ou underscore.';
    } elseif ($new_password !== '' && strlen($new_password) < 8) {
        $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
    } elseif ($new_password !== '' && !hash_equals($new_password, $confirm_password)) {
        $error = 'La confirmation du mot de passe ne correspond pas.';
    } elseif ($avatar_choice !== 'keep' && !array_key_exists($avatar_choice, $avatar_options)) {
        $error = 'Avatar sélectionné invalide.';
    } else {
        if ($new_username !== $username) {
            $duplicate_result = pg_query_params(
                $dbconn,
                'SELECT 1 FROM public."user" WHERE LOWER(username) = LOWER($1) AND id <> $2 LIMIT 1',
                [$new_username, (int)$account['id']]
            );
            if ($duplicate_result === false) {
                $error = 'Impossible de vérifier la disponibilité du pseudo.';
            } elseif (pg_num_rows($duplicate_result) > 0) {
                $error = 'Ce pseudo est déjà utilisé.';
            }
        }

        if ($error === '' && $avatar_choice !== 'keep') {
            $new_picture = $avatar_choice;
        }

        $upload = $_FILES['profile_photo'] ?? null;
        if ($error === '' && is_array($upload) && (int)($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if ((int)$upload['error'] !== UPLOAD_ERR_OK || (int)$upload['size'] > 4 * 1024 * 1024) {
                $error = 'La photo doit faire moins de 4 Mo.';
            } else {
                $image_info = @getimagesize($upload['tmp_name']);
                $file_info = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $file_info->file($upload['tmp_name']);
                $image_loaders = [
                    'image/jpeg' => 'imagecreatefromjpeg',
                    'image/png' => 'imagecreatefrompng',
                    'image/webp' => 'imagecreatefromwebp',
                ];

                if ($image_info === false || !isset($image_loaders[$mime_type]) || $image_info[0] > 4096 || $image_info[1] > 4096) {
                    $error = 'Choisis une image JPG, PNG ou WebP valide, de dimensions inférieures à 4096 × 4096.';
                } else {
                    $loader = $image_loaders[$mime_type];
                    $source_image = function_exists($loader) ? @$loader($upload['tmp_name']) : false;
                    if ($source_image === false) {
                        $error = 'Impossible de lire cette image.';
                    } else {
                        $source_width = imagesx($source_image);
                        $source_height = imagesy($source_image);
                        $crop_size = min($source_width, $source_height);
                        $source_x = (int)(($source_width - $crop_size) / 2);
                        $source_y = (int)(($source_height - $crop_size) / 2);
                        $thumbnail = imagecreatetruecolor(512, 512);
                        imagealphablending($thumbnail, false);
                        imagesavealpha($thumbnail, true);
                        $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
                        imagefill($thumbnail, 0, 0, $transparent);
                        imagecopyresampled($thumbnail, $source_image, 0, 0, $source_x, $source_y, 512, 512, $crop_size, $crop_size);

                        $upload_directory = __DIR__ . '/uploads/avatars';
                        if (!is_dir($upload_directory) && !mkdir($upload_directory, 0755, true) && !is_dir($upload_directory)) {
                            $error = 'Le dossier des photos de profil ne peut pas être créé.';
                        } else {
                            $filename = bin2hex(random_bytes(16));
                            if (function_exists('imagewebp')) {
                                $filename .= '.webp';
                                $write_ok = imagewebp($thumbnail, $upload_directory . '/' . $filename, 84);
                            } else {
                                $filename .= '.png';
                                $write_ok = imagepng($thumbnail, $upload_directory . '/' . $filename, 6);
                            }

                            if (!$write_ok) {
                                $error = 'Impossible d’enregistrer la photo de profil.';
                            } else {
                                $created_upload = $upload_directory . '/' . $filename;
                                $new_picture = '/dashboard/uploads/avatars/' . $filename;
                            }
                        }
                        imagedestroy($thumbnail);
                        imagedestroy($source_image);
                    }
                }
            }
        }

        if ($error === '') {
            $password_hash = $new_password === '' ? $account['password_hash'] : hash('sha256', $new_password);
            $update_result = pg_query_params(
                $dbconn,
                'UPDATE public."user" SET username = $1, password_hash = $2, profile_picture = $3 WHERE id = $4',
                [$new_username, $password_hash, $new_picture !== '' ? $new_picture : null, (int)$account['id']]
            );

            if ($update_result === false) {
                if ($created_upload !== '') {
                    @unlink($created_upload);
                }
                $error = 'Impossible d’enregistrer les paramètres.';
            } else {
                if ($profile_picture !== '' && str_starts_with($profile_picture, '/dashboard/uploads/avatars/')) {
                    $old_file = __DIR__ . '/uploads/avatars/' . basename($profile_picture);
                    if (is_file($old_file) && $old_file !== $created_upload) {
                        @unlink($old_file);
                    }
                }
                $payload = json_encode([
                    'user' => $new_username,
                    'token' => $token,
                    'saved' => '1',
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
                echo '<script>send_post("profile.php", ' . $payload . ');</script>';
                exit;
            }
        } elseif ($created_upload !== '') {
            @unlink($created_upload);
        }
    }
}

$username = (string)$account['username'];
$profile_picture = (string)($account['profile_picture'] ?? '');
$avatar_url = $resolve_avatar($profile_picture);
$saved = (string)($_POST['saved'] ?? '') === '1';
$user_id = (int)$account['id'];

$category_stats_result = pg_query_params(
    $dbconn,
    'SELECT c.nom, COUNT(DISTINCT g.id) FILTER (WHERE g.id <> 0 AND g.type <> 0) AS total_goals, COUNT(DISTINCT g.id) FILTER (WHERE g.id <> 0 AND g.type <> 0 AND g.is_complete = true) AS completed_goals FROM public.categorie c LEFT JOIN public.goal g ON g.id = c.goal_id WHERE c.user_id = $1 GROUP BY c.nom ORDER BY c.nom',
    [$user_id]
);
$category_stats = [];
$total_goals = 0;
$completed_goals = 0;
if ($category_stats_result !== false) {
    while ($stat_row = pg_fetch_assoc($category_stats_result)) {
        $stat_row['total_goals'] = (int)$stat_row['total_goals'];
        $stat_row['completed_goals'] = (int)$stat_row['completed_goals'];
        $category_stats[] = $stat_row;
        $total_goals += $stat_row['total_goals'];
        $completed_goals += $stat_row['completed_goals'];
    }
}

$type_stats_result = pg_query_params(
    $dbconn,
    'SELECT g.type, COUNT(DISTINCT g.id) AS completed_count FROM public.categorie c INNER JOIN public.goal g ON g.id = c.goal_id WHERE c.user_id = $1 AND g.id <> 0 AND g.type BETWEEN 1 AND 4 AND g.is_complete = true GROUP BY g.type ORDER BY g.type',
    [$user_id]
);
$type_stats = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
if ($type_stats_result !== false) {
    while ($type_row = pg_fetch_assoc($type_stats_result)) {
        $type_stats[(int)$type_row['type']] = (int)$type_row['completed_count'];
    }
}

$rank_result = pg_query_params(
    $dbconn,
    'SELECT COUNT(*) + 1 AS rank FROM public."user" WHERE nbr_point > $1',
    [(int)$account['nbr_point']]
);
$rank_row = $rank_result !== false ? pg_fetch_assoc($rank_result) : ['rank' => 0];
$completion_rate = $total_goals > 0 ? (int)round(($completed_goals / $total_goals) * 100) : 0;
$chart_categories = array_map(static fn(array $stat): string => (string)$stat['nom'], $category_stats);
$chart_completed = array_map(static fn(array $stat): int => (int)$stat['completed_goals'], $category_stats);
$chart_remaining = array_map(static fn(array $stat): int => max(0, (int)$stat['total_goals'] - (int)$stat['completed_goals']), $category_stats);
$chart_payload = json_encode([
    'labels' => $chart_categories,
    'completed' => $chart_completed,
    'remaining' => $chart_remaining,
    'types' => array_values($type_stats),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | PiGoal</title>
    <link rel="shortcut icon" href="../assets/diamond.png" type="image/png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="profile.css">
    <script src="../compenents/send_post/send_post.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body class="profile-page">
    <?php require __DIR__ . '/../compenents/user_navigation.php'; ?>

    <main class="profile-shell">
        <section class="profile-hero">
            <div class="profile-avatar-frame">
                <img class="profile-avatar" src="<?php echo htmlspecialchars($avatar_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de profil de <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="profile-identity">
                <span class="profile-eyebrow">TON ESPACE</span>
                <h1><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Rang #<?php echo (int)$rank_row['rank']; ?> · <?php echo number_format((int)$account['nbr_point'], 0, ',', ' '); ?> points</p>
            </div>
            <div class="profile-level-mark" aria-hidden="true">✦</div>
        </section>

        <section class="profile-stats" aria-label="Statistiques du compte">
            <article class="profile-stat"><span>OBJECTIFS</span><strong><?php echo $total_goals; ?></strong><small>dans <?php echo count($category_stats); ?> catégories</small></article>
            <article class="profile-stat"><span>TERMINÉS</span><strong><?php echo $completed_goals; ?></strong><small><?php echo $completion_rate; ?> % de progression</small></article>
            <article class="profile-stat"><span>POINTS</span><strong><?php echo number_format((int)$account['nbr_point'], 0, ',', ' '); ?></strong><small>rang #<?php echo (int)$rank_row['rank']; ?> du leaderboard</small></article>
        </section>

        <section class="profile-analytics">
            <div class="profile-section-heading">
                <div><span class="profile-eyebrow">PROGRESSION</span><h2>Ton activité</h2></div>
                <span class="profile-completion-pill"><?php echo $completion_rate; ?> % complété</span>
            </div>
            <div class="profile-chart-grid">
                <article class="profile-chart-panel profile-chart-wide">
                    <h3>Objectifs par catégorie</h3>
                    <div class="profile-chart-wrap profile-category-chart-wrap" style="--category-count:<?php echo max(1, count($category_stats)); ?>;"><canvas id="category-progress-chart" aria-label="Objectifs terminés et restants par catégorie"></canvas></div>
                </article>
                <article class="profile-chart-panel">
                    <h3>Récompenses obtenues</h3>
                    <div class="profile-chart-wrap profile-donut-wrap"><canvas id="goal-type-chart" aria-label="Répartition des objectifs terminés par récompense"></canvas></div>
                    <div class="profile-chart-legend"><span><i class="legend-diamond"></i>Diamant</span><span><i class="legend-gold"></i>Or</span><span><i class="legend-iron"></i>Argent</span><span><i class="legend-copper"></i>Cuivre</span></div>
                </article>
            </div>
        </section>

        <section class="profile-settings">
            <div class="profile-section-heading">
                <div><span class="profile-eyebrow">COMPTE</span><h2>Paramètres du profil</h2></div>
            </div>

            <?php if ($error !== ''): ?>
                <p class="profile-feedback profile-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php elseif ($saved): ?>
                <p class="profile-feedback profile-success" role="status">Profil mis à jour.</p>
            <?php endif; ?>

            <form class="profile-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="user" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="save_profile" value="1">

                <label class="profile-field-label" for="new-username">Pseudo</label>
                <input class="input profile-input" id="new-username" name="new_username" type="text" minlength="2" maxlength="24" value="<?php echo htmlspecialchars((string)($_POST['new_username'] ?? $username), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username" required>

                <fieldset class="avatar-picker">
                    <legend>Photo de profil</legend>
                    <div class="avatar-options">
                        <?php foreach ($avatar_options as $avatar_path => $avatar_label): ?>
                            <label class="avatar-option <?php echo $profile_picture === $avatar_path ? 'is-current' : ''; ?>">
                                <input type="radio" name="avatar_choice" value="<?php echo htmlspecialchars($avatar_path, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $profile_picture === $avatar_path ? 'checked' : ''; ?>>
                                <img src="<?php echo htmlspecialchars($avatar_path, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                <span><?php echo htmlspecialchars($avatar_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            </label>
                        <?php endforeach; ?>
                        <?php if ($profile_picture !== '' && !array_key_exists($profile_picture, $avatar_options)): ?>
                            <label class="avatar-option is-current">
                                <input type="radio" name="avatar_choice" value="keep" checked>
                                <img src="<?php echo htmlspecialchars($resolve_avatar($profile_picture), ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                <span>Actuelle</span>
                            </label>
                        <?php else: ?>
                            <label class="avatar-option avatar-keep-option">
                                <input type="radio" name="avatar_choice" value="keep" <?php echo $profile_picture === '' ? 'checked' : ''; ?>>
                                <span class="avatar-keep-icon">↺</span>
                                <span>Garder</span>
                            </label>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <label class="profile-field-label" for="profile-photo">Ou téléverser une photo</label>
                <input class="profile-file-input" id="profile-photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp">
                <small class="profile-help">JPG, PNG ou WebP · 4 Mo maximum · recadrée en carré</small>

                <div class="profile-form-divider"></div>
                <h3 class="profile-password-title">Changer le mot de passe <span>facultatif</span></h3>
                <label class="profile-field-label" for="new-password">Nouveau mot de passe</label>
                <input class="input profile-input" id="new-password" name="new_password" type="password" minlength="8" autocomplete="new-password" placeholder="8 caractères minimum">
                <label class="profile-field-label" for="confirm-password">Confirmer le nouveau mot de passe</label>
                <input class="input profile-input" id="confirm-password" name="confirm_password" type="password" minlength="8" autocomplete="new-password">
                <label class="profile-field-label" for="current-password">Mot de passe actuel <span>requis pour enregistrer</span></label>
                <input class="input profile-input" id="current-password" name="current_password" type="password" autocomplete="current-password" required>

                <button class="profile-save-button" type="submit">Enregistrer les paramètres</button>
            </form>
        </section>
    </main>

    <footer class="profile-footer"><a href="https://github.com/Perrtatter/PiGoal">Github</a></footer>
    <script type="module">
        import { CreateThemeSwitcher } from "../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../compenents/go_back/create_go_back.js";
        CreateThemeSwitcher();
        CreateGoBack("index.php", <?php echo json_encode(['user' => $username, 'token' => $token], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
    </script>
    <script>
        const profileCharts = <?php echo $chart_payload; ?>;
        const chartPalette = ['#39d7e6', '#f0c65a', '#c4d5da', '#e98456'];
        const chartText = '#c4d5df';
        if (window.Chart) {
            new Chart(document.getElementById('category-progress-chart'), {
                type: 'bar',
                data: {
                    labels: profileCharts.labels,
                    datasets: [
                        { label: 'Terminés', data: profileCharts.completed, backgroundColor: '#39d7e6', borderRadius: 5, maxBarThickness: 22 },
                        { label: 'À faire', data: profileCharts.remaining, backgroundColor: 'rgba(180, 203, 212, 0.22)', borderRadius: 5, maxBarThickness: 22 }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        x: { stacked: true, beginAtZero: true, ticks: { color: chartText, precision: 0 }, grid: { color: 'rgba(190, 220, 230, 0.1)' } },
                        y: { stacked: true, ticks: { color: chartText }, grid: { display: false } }
                    },
                    plugins: { legend: { labels: { color: chartText, usePointStyle: true, boxWidth: 8 } } }
                }
            });
            new Chart(document.getElementById('goal-type-chart'), {
                type: 'doughnut',
                data: {
                    labels: ['Diamant', 'Or', 'Argent', 'Cuivre'],
                    datasets: [{ data: profileCharts.types, backgroundColor: chartPalette, borderColor: '#12212b', borderWidth: 4, hoverOffset: 7 }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    cutout: '70%',
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: context => ` ${context.label} : ${context.raw}` } } }
                }
            });
        }
    </script>
</body>
</html>