<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\validator\rules;

use mako\tests\TestCase;
use mako\validator\rules\AlphanumericDashUnicode;

/**
 * @group unit
 */
class AlphanumericDashUnicodeTest extends TestCase
{
	/**
	 *
	 */
	public function testWithValidValue()
	{
		$rule = new AlphanumericDashUnicode;

		$this->assertTrue($rule->validate('foo-bær_1', []));
	}

	/**
	 *
	 */
	public function testWithInvalidValue()
	{
		$rule = new AlphanumericDashUnicode;

		$this->assertFalse($rule->validate('foo-bær_1.', []));

		$this->assertSame('The foobar field must contain only numbers, letters and dashes.', $rule->getErrorMessage('foobar'));
	}
}
