<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\cli\output\helpers;

use mako\cli\exceptions\CliException;
use mako\cli\output\formatter\FormatterInterface;
use mako\cli\output\helpers\Table;
use mako\cli\output\helpers\table\AsciiBorder;
use mako\cli\output\Output;
use mako\tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Group;

#[Group('unit')]
class TableTest extends TestCase
{
	/**
	 *
	 */
	public function testBasicTable(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$table = new Table($output);

		$expected  = '';
		$expected .= '┏━━━━━━━┓' . PHP_EOL;
		$expected .= '┃ Col1  ┃' . PHP_EOL;
		$expected .= '┣━━━━━━━┫' . PHP_EOL;
		$expected .= '┃ Cell1 ┃' . PHP_EOL;
		$expected .= '┗━━━━━━━┛' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1'], [['Cell1']]));
	}

	/**
	 *
	 */
	public function testBasicTableWithAsciiBorder(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$table = new Table($output, new AsciiBorder);

		$expected  = '';
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Col1  |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Cell1 |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1'], [['Cell1']]));
	}

	/**
	 *
	 */
	public function testTableWithMultipleRows(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$table = new Table($output);

		$expected  = '';
		$expected .= '┏━━━━━━━┓' . PHP_EOL;
		$expected .= '┃ Col1  ┃' . PHP_EOL;
		$expected .= '┣━━━━━━━┫' . PHP_EOL;
		$expected .= '┃ Cell1 ┃' . PHP_EOL;
		$expected .= '┃ Cell1 ┃' . PHP_EOL;
		$expected .= '┗━━━━━━━┛' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1'], [['Cell1'], ['Cell1']]));
	}

	/**
	 *
	 */
	public function testTableWithMultipleColumns(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$table = new Table($output);

		$expected  = '';
		$expected .= '┏━━━━━━━┳━━━━━━━┓' . PHP_EOL;
		$expected .= '┃ Col1  ┃ Col2  ┃' . PHP_EOL;
		$expected .= '┣━━━━━━━╋━━━━━━━┫' . PHP_EOL;
		$expected .= '┃ Cell1 ┃ Cell2 ┃' . PHP_EOL;
		$expected .= '┗━━━━━━━┻━━━━━━━┛' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1', 'Col2'], [['Cell1', 'Cell2']]));
	}

	/**
	 *
	 */
	public function testTableWithMultipleColumnsAndRows(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$table = new Table($output);

		$expected  = '';
		$expected .= '┏━━━━━━━┳━━━━━━━┓' . PHP_EOL;
		$expected .= '┃ Col1  ┃ Col2  ┃' . PHP_EOL;
		$expected .= '┣━━━━━━━╋━━━━━━━┫' . PHP_EOL;
		$expected .= '┃ Cell1 ┃ Cell2 ┃' . PHP_EOL;
		$expected .= '┃ Cell1 ┃ Cell2 ┃' . PHP_EOL;
		$expected .= '┗━━━━━━━┻━━━━━━━┛' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1', 'Col2'], [['Cell1', 'Cell2'], ['Cell1', 'Cell2']]));
	}

	/**
	 *
	 */
	public function testStyledContent(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		/** @var \mako\cli\output\formatter\FormatterInterface|\Mockery\MockInterface $formatter */
		$formatter = Mockery::mock(FormatterInterface::class);

		$formatter->shouldReceive('stripTags')->times(2)->with('<blue>Col1</blue>')->andReturn('Col1');

		$formatter->shouldReceive('stripTags')->times(2)->with('Cell1')->andReturn('Cell1');

		$output->shouldReceive('getFormatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$expected  = '';
		$expected .= '┏━━━━━━━┓' . PHP_EOL;
		$expected .= '┃ <blue>Col1</blue>  ┃' . PHP_EOL;
		$expected .= '┣━━━━━━━┫' . PHP_EOL;
		$expected .= '┃ Cell1 ┃' . PHP_EOL;
		$expected .= '┗━━━━━━━┛' . PHP_EOL;

		$this->assertSame($expected, $table->render(['<blue>Col1</blue>'], [['Cell1']]));
	}

	/**
	 *
	 */
	public function testDraw(): void
	{
		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$expected  = '';
		$expected .= '┏━━━━━━━┓' . PHP_EOL;
		$expected .= '┃ Col1  ┃' . PHP_EOL;
		$expected .= '┣━━━━━━━┫' . PHP_EOL;
		$expected .= '┃ Cell1 ┃' . PHP_EOL;
		$expected .= '┗━━━━━━━┛' . PHP_EOL;

		$output->shouldReceive('write')->once()->with($expected, Output::STANDARD);

		$table = new Table($output);

		$table->draw(['Col1'], [['Cell1']]);
	}

	/**
	 *
	 */
	public function testInvalidInput(): void
	{
		$this->expectException(CliException::class);

		/** @var \mako\cli\output\Output|\Mockery\MockInterface $output */
		$output = Mockery::mock(Output::class);

		$output->shouldReceive('getFormatter')->once()->andReturn(null);

		$table = new Table($output);

		$table->render(['Col1'], [['Cell1', 'Cell2']]);
	}
}
