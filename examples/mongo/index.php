<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Chadicus\Slim\OAuth2\Routes;
use Chadicus\Slim\OAuth2\Middleware;
use Slim\Slim;
use OAuth2\Server;
use OAuth2\Storage;
use OAuth2\GrantType;

$mongoDb = (new MongoClient())->selectDb('slim_oauth2');

$storage = new Storage\Mongo($mongoDb);
$storage->setClientDetails('librarian', 'secret');

$server = new Server(
    $storage,
    [
        'access_lifetime' => 3600,
    ],
    [
        new GrantType\ClientCredentials($storage),
    ]
);

$app = new Slim();

Routes\Token::register($app, $server);
Routes\Authorize::register($app, $server);

$authorization = new Middleware\Authorization($server);
$authorization->setApplication($app);

$app->get('/books', $authorization, function () use ($app, $mongoDb) {
    $result = [];
    try {
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;

        $books = $mongoDb->books->find([], ['_id' => false])->skip($offset)->limit($limit);

        $result = [
            'offset' => $offset,
            'limit' => $books->count(true),
            'total' => $books->count(),
            'result' => iterator_to_array($books),
        ];

    } catch (\Exception $e) {
        $app->response()->status(400);
        $result = ['error' => $e->getMessage()];
    }

    $app->contentType('application/json');
    $app->response->setBody(json_encode($result));
})->name('books-search');

$app->get('/books/:id', $authorization, function ($id) use ($app, $mongoDb) {
    $book = $mongoDb->books->findOne(['id' => $id], ['_id' => false]);
    if ($book === null) {
        $app->response()->status(404);
        $book = ['error' => "Book with id '{$id}' was not found"];
    }

    $app->contentType('application/json');
    $app->response->setBody(json_encode($book));
})->name('books-detail');

$app->run();
