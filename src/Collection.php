<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

class Collection
{
	private array $elements = [];

	public function add ($element): void
	{
		$this->elements[] = $element;
	}

	public function count (): int
	{
		return count($this->elements);
	}
}