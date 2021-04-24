<?php
    require_once 'config.php';
    require_once 'vendor/autoload.php';

    use Medoo\Medoo;

    $database = new Medoo($database_config);

    //For SQLIte
    $database->query("
    CREATE TABLE IF NOT EXISTS status(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        type TEXT NOT NULL,
        name TEXT NOT NULL,
        hostname TEXT NOT NULL,
        status BIGINT NOT NULL,
        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
    );
    ");