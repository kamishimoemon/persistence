<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

interface Memento
{
	function accept (MementoVisitor $visitor): void;
	function restore (): PersistableObject;
}