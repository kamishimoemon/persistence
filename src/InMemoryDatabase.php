<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use Closure;

class InMemoryDatabase implements Database
{
	private array $objects = [];

	public function add (PersistableObject $po): int
	{
		$this->objects[] = $po;
		return count($this->objects);
	}

	public function remove (string $persistableClass, int $id): void
	{
		unset($this->objects[$id - 1]);
	}

	public function get (string $persistableClass, int $id): ?PersistableObject
	{
		if (isset($this->objects[$id - 1])) {
			return $this->objects[$id - 1];
		}
		return null;
	}

	public function filter (string $persistableClass, Closure $predicate): Collection
	{
		$c = new Collection();
		foreach ($this->objects as $po) {
			if ($predicate($po)) {
				$c->add($po);
			}
		}
		return $c;
	}
}