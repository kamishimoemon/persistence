<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

interface MementoVisitor
{
	function setString (string $key, string $value): void;
}