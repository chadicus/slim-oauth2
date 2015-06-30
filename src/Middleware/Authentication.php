<?php
namespace Slim\OAuth2\Middleware;

use OAuth2;

class Authentication extends \Slim\Middleware
{
    private $server;

    public function __construct(OAuth2\Server $server)
    {
        $this->server = $server;
    }

    public function call()
    {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            // Not authorized return 401 error
            $this->app->halt(401, '');
            return;
        }

        // this line is required for the application to proceed
        $this->next->call();
    }
}
