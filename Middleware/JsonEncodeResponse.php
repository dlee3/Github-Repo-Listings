<?php

namespace Middleware;

use Model\Config;

class JsonEncodeResponse extends \Slim\Middleware
{
    public function call()
    {
    	// add output to response object for encoding later
		$this->app->hook(
			'slim.before',
			function () // use ($app)
			{
				$this->app->response->output = (object) array(
					'version' => Config::$instance->app->version,
					'status' => 'error',
					'message' => '',
					'data' => (object) array()
				);
			}
		);



		// encode output for response
		$this->app->hook(
			'slim.after.router',
			function () // use ($app)
			{
				// fill in the blanks, depending on the status
				switch ($this->app->response->output->status)
				{
					case 'error':

						// default code
						if ( !isset($this->app->response->output->code) )
						{
							$this->app->response->output->code = 500;
						}

						// default message
						if ( empty($this->app->response->output->message) )
						{
							$this->app->response->output->message = 'Internal Server Error';
						}

						break;

					case 'fail':

						// default message
						if ( empty($this->app->response->output->message) )
						{
							$this->app->response->output->message = 'Sorry, something went wrong.';
						}

						break;

					case 'success':

						// default message
						if ( empty($this->app->response->output->message) )
						{
							$this->app->response->output->message = 'Request was successful';
						}

						break;

					// If not one of the above 3, something's wrong
					default:
						$this->app->response->output->status = 'error';
						$this->app->response->output->code = 500;
						$this->app->response->output->message = 'Internal Server Error';
				}



				// format all responses using json
				$this->app->response->setBody(
					json_encode($this->app->response->output)
				);
			}
		);

        $this->next->call();
    }
}