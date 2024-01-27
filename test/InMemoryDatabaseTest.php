<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use PHPUnit\Framework\TestCase;

class InMemoryDatabaseTest extends TestCase
{
	/**
	 * @test
	 */
	public function addingAPersistableObject (): void
	{
		$po = new PersistableObject();
		$db = new InMemoryDatabase();

		$id = $db->add($po);

		$this->assertNotNull($id);
	}

	/**
	 * @test
	 */
	public function readingAPersistableObject (): void
	{
		$db = new InMemoryDatabase();

		$po1 = new PersistableObject();
		$id = $db->add($po1);
		$po2 = $db->get($id);

		$this->assertSame($po1, $po2);
	}

	/**
	 * @test
	 */
	public function updatingAPersistableObject (): void
	{
		$db = new InMemoryDatabase();

		$po1 = new PersistableObject();
		$id = $db->add($po1);
		$po1->name = "Darío";
		$po2 = $db->get($id);

		$this->assertEquals("Darío", $po2->name);
	}

	/**
	 * @test
	 */
	public function removingAPersistableObject (): void
	{
		$db = new InMemoryDatabase();

		$po1 = new PersistableObject();
		$id = $db->add($po1);
		$db->remove($id);
		$po2 = $db->get($id);

		$this->assertNull($po2);
	}

	/**
	 * @test
	 */
	public function browsingPersistableObjects (): void
	{
		$db = new InMemoryDatabase();

		foreach(["Darío", "Cesar", "Mauro"] as $name) {
			$po = new PersistableObject();
			$po->name = $name;
			$db->add($po);
		}

		$collection = $db->filter(function ($po) {
			return $po->name == "Darío";
		});

		$this->assertEquals(1, $collection->count());
	}
}