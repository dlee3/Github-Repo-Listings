<?php

namespace Middleware;

class SetGlobalResponseHeaders extends \Slim\Middleware
{
    public function call()
    {
        // set all responses to json
        $this->app->response->headers->set('Content-Type', 'application/json');

        $this->next->call();
    }
}