<?php



namespace Controller;


use \Slim\Slim;



class SlimController
{
    protected $app;



    // https://github.com/silentworks/sharemyideas/blob/develop/app/application.php
    public function __construct(Slim $slim = null)
    {
        $this->app = ! empty ( $slim ) ? $slim : Slim::getInstance();
    }

}


