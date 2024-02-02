<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use Closure;

interface Database
{
	function add (PersistableObject $po): int;
	function remove (int $id): void;
	function get (int $id): ?PersistableObject;
	function filter (Closure $predicate): Collection;
}