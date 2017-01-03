#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OAuth2\Storage\Pdo as PdoStorage;

$pdo = new \PDO('sqlite:' . __DIR__ . '/slim_oauth2.db');
$storage = new PdoStorage($pdo);
foreach (explode(';', $storage->getBuildSql()) as $statement) {
    $result = $pdo->exec($statement);
}

// set up clients
$sql = 'INSERT INTO oauth_clients (client_id, client_secret, scope, redirect_uri) VALUES (?, ?, ?, ?)';
$pdo->prepare($sql)->execute(['librarian', 'secret', 'bookCreate', '/receive-code']);
$pdo->prepare($sql)->execute(['student', 's3cr3t', null, null]);

copy(__DIR__ . '/../books.json', __DIR__ . '/books.json');
