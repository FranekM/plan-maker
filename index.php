<?php
    require 'db_connection.php';

    $db = new db_connection;
    
    $parts = parse_url($_SERVER["REQUEST_URI"]);
    parse_str($parts['query'], $query);
    
    $plan_id = $query['id'];

    $plan = $db->get_plan($plan_id);

    echo "<pre>";

    var_dumb($plan);

?>