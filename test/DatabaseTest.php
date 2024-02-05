<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use PHPUnit\Framework\TestCase;

abstract class DatabaseTest extends TestCase
{
	protected Database $db;

	/**
	 * @test
	 */
	public function addingAPersistableObject (): void
	{
		$po = new User("Darío Candotti", "dario.candotti@gmail.com");

		$id = $this->db->add($po);

		$this->assertNotNull($id);
	}

	/**
	 * @test
	 */
	public function readingAPersistableObject (): void
	{
		$po1 = new User("Darío Candotti", "dario.candotti@gmail.com");
		$id = $this->db->add($po1);
		$po2 = $this->db->get(User::class, $id);

		$this->assertSame($po1, $po2);
	}

	/**
	 * @test
	 */
	public function updatingAPersistableObject (): void
	{
		$po1 = new User("Darío Candotti", "dario.candotti@gmail.com");
		$id = $this->db->add($po1);
		$po1->setName("Darío");
		$po2 = $this->db->get(User::class, $id);

		$this->assertEquals("Darío", $po2->name());
	}

	/**
	 * @test
	 */
	public function removingAPersistableObject (): void
	{
		$po1 = new User("Darío Candotti", "dario.candotti@gmail.com");
		$id = $this->db->add($po1);
		$this->db->remove(User::class, $id);
		$po2 = $this->db->get(User::class, $id);

		$this->assertNull($po2);
	}

	/**
	 * @test
	 */
	public function browsingPersistableObjects (): void
	{
		$this->db->add(new User("Darío Candotti", "dario.candotti@gmail.com"));
		$this->db->add(new User("Cesar Candotti", "candotti.cesar@gmail.com"));
		$this->db->add(new User("Mauro Candotti", "cmduilio@gmail.com"));

		$collection = $this->db->filter(User::class, function ($user) {
			return $user->name() == "Darío Candotti";
		});

		$this->assertEquals(1, $collection->count());
	}

	/**
	 * @test
	 * Ignoring this test for now.
	 *
	public function databasesShouldBeSetsNotCollections (): void
	{
		$po = new User("Darío Candotti", "dario.candotti@gmail.com");
		$id1 = $this->db->add($po);
		$id2 = $this->db->add($po);

		$this->assertEquals($id1, $id2);
	}
	*/
}

class User extends PersistableObject {
	private string $name;
	private string $email;

	public function __construct (string $name, string $email)
	{
		$this->name = $name;
		$this->email = $email;
	}

	public function name (): string
	{
		return $this->name;
	}

	public function setName (string $name): void
	{
		$this->name = $name;
	}

	public function memento (): Memento
	{
		return new UserMemento($this->name, $this->email);
	}
}

class UserMemento implements Memento {
	private string $name;
	private string $email;

	public function __construct (string $name, string $email)
	{
		$this->name = $name;
		$this->email = $email;
	}

	public function accept (MementoVisitor $visitor): void
	{
		$visitor->setString("name", $this->name);
		$visitor->setString("email", $this->email);
	}

	public function restore (): PersistableObject
	{
		return new User($this->name, $this->email);
	}
}