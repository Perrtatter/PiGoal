<?php
$navigation_user = trim((string)($_POST['user'] ?? $_POST['username'] ?? ''));
$navigation_token = (string)($_POST['token'] ?? '');
$navigation_payload = htmlspecialchars(json_encode([
    'user' => $navigation_user,
    'token' => $navigation_token,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
$navigation_page = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
$profile_active = $navigation_page === 'profile.php';
$leaderboard_active = $navigation_page === 'index.php' && str_contains((string)($_SERVER['SCRIPT_NAME'] ?? ''), '/leaderboard/');
?>
<header class="header app-header">
    <div id="go_back_div"></div>
    <nav class="app-user-navigation" aria-label="Navigation du compte">
        <button class="app-nav-button <?php echo $profile_active ? 'is-active' : ''; ?>" type="button" <?php echo $profile_active ? 'aria-current="page"' : ''; ?> onclick='send_post("/dashboard/profile.php", <?php echo $navigation_payload; ?>)'>Profil</button>
        <button class="app-nav-button <?php echo $leaderboard_active ? 'is-active' : ''; ?>" type="button" <?php echo $leaderboard_active ? 'aria-current="page"' : ''; ?> onclick='send_post("/leaderboard/index.php", <?php echo $navigation_payload; ?>)'>Leaderboard</button>
    </nav>
    <div id="theme_switcher_div"></div>
</header>