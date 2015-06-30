<?php

namespace Slim\OAuth2\Routes;

use OAuth2;
use Slim\Slim;

class Token
{
    private $slim;
    private $server;

    public function __construct(Slim $slim, OAuth2\Server $server)
    {
        $this->slim = $slim;
        $this->server = $server;
    }

    public function __invoke()
    {
        $response = $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals());
        foreach ($response->getHttpHeaders() as $key => $value) {
            $this->slim->response()->headers->set($key, $value);
        }

        $this->slim->response()->status($response->getStatusCode());
        $this->slim->response()->setBody($response->getResponseBody());
    }
}
