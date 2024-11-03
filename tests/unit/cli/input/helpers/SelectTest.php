<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\cli\input\helpers;

use mako\cli\input\helpers\Select;
use mako\cli\input\Input;
use mako\cli\output\Output;
use mako\tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Group;

#[Group('unit')]
class SelectTest extends TestCase
{
	/**
	 *
	 */
	public function testSelectAndPickFirstOptionWithNumericInput(): void
	{
		/** @var \mako\cli\input\Input|\Mockery\MockInterface $input */
		$input = Mockery::mock(Input::class);

		$input->shouldReceive('read')->once()->andReturn('1');

		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('writeLn')->once()->with('Favorite food?');

		$output->shouldReceive('write')->once()->with('1) Burgers' . PHP_EOL . '2) Sushi' . PHP_EOL . '> ');

		$select = new Select($input, $output);

		$this->assertSame(0, $select->ask('Favorite food?', ['Burgers', 'Sushi']));
	}

	/**
	 *
	 */
	public function testSelectAndPickFirstOptionWithNumericInputAndCustomPrompt(): void
	{
		/** @var \mako\cli\input\Input|\Mockery\MockInterface $input */
		$input = Mockery::mock(Input::class);

		$input->shouldReceive('read')->once()->andReturn('1');

		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('writeLn')->once()->with('Favorite food?');

		$output->shouldReceive('write')->once()->with('1) Burgers' . PHP_EOL . '2) Sushi' . PHP_EOL . '[ ');

		$select = new Select($input, $output, '[');

		$this->assertSame(0, $select->ask('Favorite food?', ['Burgers', 'Sushi']));
	}

	/**
	 *
	 */
	public function testSelectAndPickFirstOptionWithTextInput(): void
	{
		/** @var \mako\cli\input\Input|\Mockery\MockInterface $input */
		$input = Mockery::mock(Input::class);

		$input->shouldReceive('read')->once()->andReturn('burgers');

		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('writeLn')->once()->with('Favorite food?');

		$output->shouldReceive('write')->once()->with('1) Burgers' . PHP_EOL . '2) Sushi' . PHP_EOL . '> ');

		$select = new Select($input, $output);

		$this->assertSame(0, $select->ask('Favorite food?', ['Burgers', 'Sushi']));
	}

	/**
	 *
	 */
	public function testSelectAndPickSecondOption(): void
	{
		/** @var \mako\cli\input\Input|\Mockery\MockInterface $input */
		$input = Mockery::mock(Input::class);

		$input->shouldReceive('read')->once()->andReturn('2');

		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('writeLn')->once()->with('Favorite food?');

		$output->shouldReceive('write')->once()->with('1) Burgers' . PHP_EOL . '2) Sushi' . PHP_EOL . '> ');

		$select = new Select($input, $output);

		$this->assertSame(1, $select->ask('Favorite food?', ['Burgers', 'Sushi']));
	}

	/**
	 *
	 */
	public function testSelectAndPickFirstOptionAfterPickingInvalidOption(): void
	{
		/** @var \mako\cli\input\Input|\Mockery\MockInterface $input */
		$input = Mockery::mock(Input::class);

		$input->shouldReceive('read')->once()->andReturn('3');

		$input->shouldReceive('read')->once()->andReturn('1');

		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('writeLn')->twice()->with('Favorite food?');

		$output->shouldReceive('write')->twice()->with('1) Burgers' . PHP_EOL . '2) Sushi' . PHP_EOL . '> ');

		$select = new Select($input, $output);

		$this->assertSame(0, $select->ask('Favorite food?', ['Burgers', 'Sushi']));
	}
}
