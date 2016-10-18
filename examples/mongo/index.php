<?php

require_once __DIR__ . '/vendor/autoload.php';

use Chadicus\Slim\OAuth2\Routes;
use Chadicus\Slim\OAuth2\Middleware;
use Slim\Http;
use Slim\Views;
use OAuth2\Storage;
use OAuth2\GrantType;

$mongoDb = (new MongoClient())->selectDb('slim_oauth2');

$storage = new Storage\Mongo($mongoDb);
$storage->setClientDetails('librarian', 'secret', '/receive-code', null, 'bookCreate');
$storage->setClientDetails('student', 's3cr3t');

$server = new OAuth2\Server(
    $storage,
    [
        'access_lifetime' => 3600,
    ],
    [
        new GrantType\ClientCredentials($storage),
        new GrantType\AuthorizationCode($storage),
    ]
);

$app = new Slim\App([]);

$renderer = new Views\PhpRenderer( __DIR__ . '/vendor/chadicus/slim-oauth2-routes/templates');

$app->map(['GET', 'POST'], Routes\Authorize::ROUTE, new Routes\Authorize($server, $renderer))->setName('authorize');
$app->post(Routes\Token::ROUTE, new Routes\Token($server))->setName('token');
$app->map(['GET', 'POST'], Routes\ReceiveCode::ROUTE, new Routes\ReceiveCode($renderer))->setName('receive-code');

$authorization = new Middleware\Authorization($server, $app->getContainer());

$app->get('/books', function ($request, $response) use ($mongoDb) {
    $result = [];
    $status = 200;
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
        $status = 400;
        $result = ['error' => $e->getMessage()];
    }

    $stream = fopen('php://temp', 'r+');
    fwrite($stream, json_encode($result));
    rewind($stream);

    return $response->withStatus($status)->withHeader('Content-Type', 'application/json')->withBody(new Http\Stream($stream));
})->setName('books-search')->add($authorization);

$app->get('/books/{id}', function ($request, $response, $args) use ($mongoDb) {
    $id = $args['id'];
    $book = $mongoDb->books->findOne(['_id' => new \MongoId($id)]);
    $status = 200;
    $result = null;
    if ($book === null) {
        $status = 404;
        $result = ['error' => "Book with id '{$id}' was not found"];
    } else {
        $result = [
            'id' => (string)$book['_id'],
            'url' => "/books/{$book['_id']}",
        ];
    }

    $stream = fopen('php://temp', 'r+');
    fwrite($stream, json_encode($result));
    rewind($stream);

    return $response->withStatus($status)->withHeader('Content-Type', 'application/json')->withBody(new Http\Stream($stream));
})->setName('books-detail')->add($authorization);

$app->post('/books', function ($request, $response, $args) use ($mongoDb) {
    $book = json_decode((string)$request->getBody(), true);
    $mongoDb->books->insert($book);
    return $response->withStatus(201)->withHeader('Location', "/books/{$book['_id']}");
})->setName('book-create')->add($authorization->withRequiredScope(['bookCreate']));

$app->run();
