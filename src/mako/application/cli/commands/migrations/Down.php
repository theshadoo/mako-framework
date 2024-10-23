<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\application\cli\commands\migrations;

use mako\application\cli\commands\migrations\traits\RollbackTrait;
use mako\cli\input\arguments\Argument;
use mako\reactor\attributes\Arguments;
use mako\reactor\attributes\CommandDescription;

/**
 * Command that rolls back the last batch of migrations.
 */
#[CommandDescription('Rolls back the last batch of migrations.')]
#[Arguments(
	new Argument('-b|--batches', 'Number of batches to roll back', Argument::IS_OPTIONAL | Argument::IS_INT),
	new Argument('-d|--database', 'Sets which database connection to use', Argument::IS_OPTIONAL),
)]
class Down extends Command
{
	use RollbackTrait;

	/**
	 * Executes the command.
	 */
	public function execute(int $batches = 1): void
	{
		$this->rollback($batches);
	}
}
