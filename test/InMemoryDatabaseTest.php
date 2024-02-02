<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

class InMemoryDatabaseTest extends DatabaseTest
{
	protected function setUp (): void
	{
		$this->db = new InMemoryDatabase();
	}
}