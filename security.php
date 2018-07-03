<?php
    if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
        || !$_SERVER['PHP_AUTH_USER'] == 'admin' 
        || !$_SERVER['PHP_AUTH_PW'] == 'admin') {
        header('WWW-Authenticate: Basic realm="karp"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You shall not pass!';
        exit;
    }
?>