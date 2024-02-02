<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

abstract class PersistableObject
{
	public abstract function memento (): Memento;
}