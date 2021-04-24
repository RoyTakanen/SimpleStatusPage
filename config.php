<?php
    $alert_email = "email@example.com";
    
    //SQlite TODO: add script to fix permissions (users cannot download db etc)
    $database_config = array(
        "database_type" => "sqlite",
        "database_file" => './database.db'
    );

    //MySQL

    /* $database_config = array(
        'database_type' => 'mysql',
        'database_name' => 'name',
        'server' => 'localhost',
        'username' => 'your_username',
        'password' => 'your_password',
    ); */