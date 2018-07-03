<?php
    require 'db_connection.php';
    require 'mustache.php/src/Mustache/Autoloader.php';
    Mustache_Autoloader::register();

    $m = new Mustache_Engine([
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials')
    ]);

    $db = new db_connection();

    $parts = parse_url($_SERVER["REQUEST_URI"]);
    parse_str($parts['query'], $query);
    $plan_id = $query['id'];

    $plan = $db->get_plan($plan_id);
    // echo "<pre>";
    // var_dump($plan);
    // echo "</pre>";
    echo $m->render('plan', $plan);
?>