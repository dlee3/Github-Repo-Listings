<?php

namespace Middleware;

use Model\Config;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use Exception;



class DartAuthorize extends \Slim\Middleware
{
    public function call()
    {
		$this->app->hook(
			'slim.before',
			function ()
			{
				$apiKey = self::getApiKey();



				// if nothing to authenticate, error
				if ( empty($apiKey) )
				{
					$this->returnError();
				}



				// otherwise, test it
				$client = new Client();

				try
				{
					$response = $client->get(
						sprintf(
							'https://auth.dartmusic.com/authenticate/%s',
							$apiKey
						)
					);
				}
				catch (Exception $e)
				{
					$this->returnError();
				}
			}
		);

        $this->next->call();
    }



    static function getApiKey ()
    {
		// try to get the api key from headers in PHP 5.6 and up
		if ( isset($_SERVER['HTTP_AUTHORIZATION']) )
		{
			$apiKey = $_SERVER['HTTP_AUTHORIZATION'];
		}

		// try to get the api key from headers in PHP 5.5 and down
		if ( empty($apiKey) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) )
		{
			$apiKey = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
		}

		// otherwise check $_GET and $_POST
		if ( empty($apiKey) && isset($_REQUEST['api_key']) )
		{
			$apiKey = $_REQUEST['api_key'];
		}

		return $apiKey;
    }



    private function returnError ()
    {
    	http_response_code(401);
		$this->app->response->output->code = 401;
		$this->app->response->output->status = 'fail';
    	throw new Exception('Authorization failed');
    }

}



