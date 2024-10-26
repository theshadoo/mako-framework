<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\cli\output;

/**
 * Cursor.
 */
class Cursor
{
	/**
	 * Is the cursor hidden?
	 */
	protected bool $hidden = false;

	/**
	 * Constructor.
	 */
	public function __construct(
		protected Output $output
	) {
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		$this->restore();
	}

	/**
	 * Is the cursor hidden?
	 */
	public function isHidden(): bool
	{
		return $this->hidden;
	}

	/**
	 * Hides the cursor.
	 */
	public function hide(): void
	{
		$this->output->write("\e[?25l");

		$this->hidden = true;
	}

	/**
	 * Shows the cursor.
	 */
	public function show(): void
	{
		$this->output->write("\e[?25h");

		$this->hidden = false;
	}

	/**
	 * Restores the cursor.
	 */
	public function restore(): void
	{
		if ($this->hidden) {
			$this->show();
		}
	}

	/**
	 * Moves the cursor to the beginning of the line.
	 */
	public function beginningOfLine(): void
	{
		$this->output->write("\r");
	}

	/**
	 * Moves the cursor up.
	 */
	public function up(int $lines = 1): void
	{
		$this->output->write("\033[{$lines}A");
	}

	/**
	 * Moves the cursor down.
	 */
	public function down(int $lines = 1): void
	{
		$this->output->write("\033[{$lines}B");
	}

	/**
	 * Moves the cursor right.
	 */
	public function right(int $columns = 1): void
	{
		$this->output->write("\033[{$columns}C");
	}

	/**
	 * Moves the cursor left.
	 */
	public function left(int $columns = 1): void
	{
		$this->output->write("\033[{$columns}D");
	}

	/**
	 * Moves the cursor to a specific position.
	 */
	public function moveTo(int $row, int $column): void
	{
		$this->output->write("\033[{$row};{$column}H");
	}

	/**
	 * Moves the cursor to the beginning of the line.
	 */
	public function moveToBeginningOfLine(): void
	{
		$this->output->write("\r");
	}

	/**
	 * Moves the cursor to the end of the line.
	 */
	public function moveToEndOfLine(): void
	{
		$this->right(9999);
	}

	/**
	 * Clears the line.
	 */
	public function clearLine(): void
	{
		$this->output->write("\r\33[2K");
	}

	/**
	 * Clears the line from the cursor.
	 */
	public function clearLineFromCursor(): void
	{
		$this->output->write("\33[K");
	}

	/**
	 * Clears n lines.
	 */
	public function clearLines(int $lines): void
	{
		for ($i = 0; $i < $lines; $i++) {
			if ($i > 0) {
				$this->up();
			}

			$this->clearLine();
		}
	}

	/**
	 * Clears the screen.
	 */
	public function clearScreen(): void
	{
		$this->output->write("\e[H\e[2J");
	}

	/**
	 * Clears the screen from the cursor.
	 */
	public function clearScreenFromCursor(): void
	{
		$this->output->write("\e[J");
	}
}
