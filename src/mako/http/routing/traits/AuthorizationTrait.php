<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\http\routing\traits;

use mako\http\exceptions\ForbiddenException;

/**
 * Authorization trait.
 *
 * @property \mako\gatekeeper\authorization\AuthorizerInterface $authorizer
 * @property \mako\gatekeeper\Gatekeeper                        $gatekeeper
 */
trait AuthorizationTrait
{
	/**
	 * Throws a ForbiddenException if the user is not allowed to perform the action on the entity.
	 *
	 * @param string        $action        Action
	 * @param object|string $entity        Entity instance or class name
	 * @param mixed         ...$parameters Additional parameters
	 */
	protected function authorize(string $action, object|string $entity, mixed ...$parameters): void
	{
		if($this->authorizer->can($this->gatekeeper->getUser(), $action, $entity, ...$parameters) === false)
		{
			throw new ForbiddenException;
		}
	}
}
