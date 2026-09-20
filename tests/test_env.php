<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php
    
    // import env
    require_once __DIR__ . '/../compenents/get_env/get_env.php';
    echo get_env("../","host") . "\n";
    echo get_env("../","port") . "\n";
    echo get_env("../","password") . "\n";
    echo get_env("../","username") . "\n";
    echo get_env("../","dbname");

    ?>
</body>
</html>