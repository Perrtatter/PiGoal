<?php
// get params
$username = $_POST["username"];
$category = $_POST["category"];

echo $username . " - " . $category . "\n";

echo "\n#############\n";

$params_list = array(
    "username"=>$username,
    "category"=>$category
);

echo json_encode($params_list);

?>

