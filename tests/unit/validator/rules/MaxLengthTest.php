<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\validator\rules;

use mako\tests\TestCase;
use mako\validator\rules\MaxLength;

/**
 * @group unit
 */
class MaxLengthTest extends TestCase
{
	/**
	 *
	 */
	public function testWithValidValue()
	{
		$rule = new MaxLength;

		$rule->setParameters([6]);

		$this->assertTrue($rule->validate('foo', []));
		$this->assertTrue($rule->validate('foobar', []));
	}

	/**
	 *
	 */
	public function testWithInvalidValue()
	{
		$rule = new MaxLength;

		$rule->setParameters([6]);

		$this->assertFalse($rule->validate('foobarbaz', []));

		$this->assertSame('The value of the foobar field must be at most 6 characters long.', $rule->getErrorMessage('foobar'));
	}
}
