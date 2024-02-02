<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use Closure;
use SimpleXMLElement;

use function simplexml_load_file;

class XMLDatabase implements Database
{
	private string $filename;

	public function __construct (string $filename)
	{
		$xml = simplexml_load_file($filename);
		if (!$xml) {
			throw new Exception("Couldn't open file {$filename}");
		}
		$this->filename = $filename;
	}

	public function add (PersistableObject $po): int
	{
		$repositories = simplexml_load_file($this->filename);
		foreach ($repositories->repository as $repository) {
			if (strval($repository["class"]) == get_class($po)) {
				$currentId = intval($repository["sequence"]);
				$id = $currentId + 1;
				$child = $repository->addChild(str_replace("\\", ".", get_class($po)));
				$po->memento()->accept(new XMLMementoVisitor($child));
				$repository["sequence"] = strval($id);
				$repositories->asXML($this->filename);
				return $id;
			}
		}

		$repository = $repositories->addChild("repository");
		$repository->addAttribute("class", get_class($po));
		$repository->addAttribute("sequence", "1");
		$child = $repository->addChild(str_replace("\\", ".", get_class($po)));
		$po->memento()->accept(new XMLMementoVisitor($child));
		$repositories->asXML($this->filename);
		return 1;
	}

	public function remove (int $id): void
	{
		throw new \Exception("Method not implemented yet");
	}

	public function get (int $id): ?PersistableObject
	{
		throw new \Exception("Method not implemented yet");
	}

	public function filter (Closure $predicate): Collection
	{
		throw new \Exception("Method not implemented yet");
	}
}

class XMLMementoVisitor implements MementoVisitor
{
	private SimpleXMLElement $element;

	public function __construct (SimpleXMLElement $element)
	{
		$this->element = $element;
	}

	public function setString (string $key, string $value): void
	{
		$this->element->addAttribute($key, $value);
	}
}