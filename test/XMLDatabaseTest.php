<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

class XMLDatabaseTest extends DatabaseTest
{
	protected function setUp (): void
	{
		$filename = __DIR__ . "/data.xml";
		file_put_contents($filename, <<<XML
		<?xml version="1.0" encoding="UTF-8"?>
		<repositories>
		</repositories>
		XML, LOCK_EX);
		$this->db = new XMLDatabase($filename);
	}
}