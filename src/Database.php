<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use Closure;

interface Database
{
	function add (PersistableObject $po): int;
	function remove (string $persistableClass, int $id): void;
	function get (string $persistableClass, int $id): ?PersistableObject;
	function filter (string $persistableClass, Closure $predicate): Collection;
}