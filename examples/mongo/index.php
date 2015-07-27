<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Chadicus\Slim\OAuth2\Routes;
use Chadicus\Slim\OAuth2\Middleware; use Slim\Slim;
use OAuth2\Server;
use OAuth2\Storage;
use OAuth2\GrantType;

$mongoDb = (new MongoClient())->selectDb('slim_oauth2');

$storage = new Storage\Mongo($mongoDb);
$storage->setClientDetails('librarian', 'secret', null, null, 'bookCreate');
$storage->setClientDetails('student', 's3cr3t');

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
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $books = $mongoDb->books->find([])->skip($offset)->limit($limit);

        $result = [
            'offset' => $offset,
            'limit' => $books->count(true),
            'total' => $books->count(),
            'books' => [],
        ];

        foreach ($books as $book) {
            $result['books'][] = [
                'id' => (string)$book['_id'],
                'url' => "/books/{$book['_id']}",
            ];
        }
    } catch (\Exception $e) {
        $app->response()->status(400);
        $result = ['error' => $e->getMessage()];
    }

    $app->contentType('application/json');
    $app->response->setBody(json_encode($result));
})->name('books-search');

$app->get('/books/:id', $authorization, function ($id) use ($app, $mongoDb) {
    $book = $mongoDb->books->findOne(['_id' => new \MongoId($id)]);
    if ($book === null) {
        $app->response()->status(404);
        $book = ['error' => "Book with id '{$id}' was not found"];
    }

    $book['id'] = (string)$book['_id'];
    $book['url'] = "/books/{$book['_id']}";
    unset($book['_id']);

    $app->contentType('application/json');
    $app->response->setBody(json_encode($book));
})->name('books-detail');

$app->post('/books', $authorization->withRequiredScope(['bookCreate']), function () use ($app, $mongoDb) {
    $book = json_decode($app->request->getBody(), true);

    $mongoDb->books->insert($book);
    $app->response()->status(201);
    $app->response()->headers->set('Location', "/books/{$book['_id']}");
});

$app->run();
