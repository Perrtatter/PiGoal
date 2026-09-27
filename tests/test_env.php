
<?php
    
    // import env
    require_once __DIR__ . '/../compenents/get_env/get_env.php';
    echo get_env("../","host") . "<br>";
    echo get_env("../","port") . "<br>";
    echo get_env("../","password") . "<br>";
    echo get_env("../","username") . "<br>";
    echo get_env("../","dbname");

?>
