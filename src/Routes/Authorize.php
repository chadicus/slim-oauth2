<?php

namespace Slim\OAuth2\Routes;

use OAuth2;
use Slim\Slim;

class Authorize
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
        //@TODO create bridge for request, do not create from globals
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();
        $isValid = $this->server->validateAuthorizeRequest($request, $response);

        if (!$isValid) {
            //@TODO send error response
        }

        $authorized = $this->slim->request()->params('authorized');
        if (empty($authorized)) {
            //@TODO send to authorize landing page
        }

        $isAuthorized = $authorized === 'yes';
        //@TODO implement user_id
        $this->server->handleAuthorizeRequest($request, $response, $isAuthorized);

        $redirect = $response->getHttpHeader('Location');
        if (!empty($redirect)) {
            $this->slim->response()->redirect($redirect);
        }

        //@TODO send error response
    }
}
