<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\application\services\cli;

use Throwable;

use mako\application\services\Service;
use mako\error\ErrorHandler;
use mako\error\handlers\cli\DevelopmentHandler;
use mako\error\handlers\cli\ProductionHandler;

/**
 * Error handler service.
 *
 * @author Frederic G. Østby
 */
class ErrorHandlerService extends Service
{
	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		$errorHandler = new ErrorHandler;

		if($this->container->get('config')->get('application.error_handler.log_errors'))
		{
			$errorHandler->setLogger(function()
			{
				return 	$this->container->get('logger');
			});
		}

		if($this->container->get('config')->get('application.error_handler.display_errors'))
		{
			$errorHandler->handle(Throwable::class, function($exception)
			{
				return (new DevelopmentHandler($this->container->get('output')))->handle($exception);
			});
		}
		else
		{
			$errorHandler->handle(Throwable::class, function($exception)
			{
				return (new ProductionHandler($this->container->get('output')))->handle($exception);
			});
		}

		$this->container->registerInstance([ErrorHandler::class, 'errorHandler'], $errorHandler);
	}
}
