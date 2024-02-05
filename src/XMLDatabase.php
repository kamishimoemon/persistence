<?php

declare(strict_types=1);

namespace Kamishimoemon\Persistence;

use Closure;
use SimpleXMLElement;
use ReflectionClass;
use ReflectionProperty;

use function simplexml_load_file;

class XMLDatabase implements Database
{
	private string $filename;
	private array $cache;

	public function __construct (string $filename)
	{
		$xml = simplexml_load_file($filename);
		if (!$xml) {
			throw new Exception("Couldn't open file {$filename}");
		}
		$this->filename = $filename;
		$this->cache = [];
	}

	public function add (PersistableObject $po): int
	{
		$memento = $po->memento();
		$repositories = simplexml_load_file($this->filename);
		foreach ($repositories->repository as $repository) {
			if (strval($repository["class"]) == get_class($po)) {
				$currentId = intval($repository["sequence"]);
				$id = $currentId + 1;
				$child = $repository->addChild(str_replace("\\", ".", get_class($memento)));
				$child->addAttribute("id", strval($id));
				$memento->accept(new XMLMementoVisitor($child));
				$repository["sequence"] = strval($id);
				$repositories->asXML($this->filename);
				$this->cache[$id] = $po;
				return $id;
			}
		}

		$repository = $repositories->addChild("repository");
		$repository->addAttribute("class", get_class($po));
		$repository->addAttribute("sequence", "1");
		$child = $repository->addChild(str_replace("\\", ".", get_class($memento)));
		$child->addAttribute("id", "1");
		$memento->accept(new XMLMementoVisitor($child));
		$repositories->asXML($this->filename);
		$this->cache[1] = $po;
		return 1;
	}

	public function remove (string $persistableClass, int $id): void
	{
		throw new \Exception("Method not implemented yet");
	}

	public function get (string $persistableClass, int $id): ?PersistableObject
	{
		if (isset($this->cache[$id])) {
			return $this->cache[$id];
		}
		$repositories = simplexml_load_file($this->filename);
		foreach ($repositories->repository as $repository) {
			if (strval($repository["class"]) == $persistableClass) {
				foreach ($repository->children() as $child) {
					if (strval($child['id']) == $id) {
						$mementoClass = new ReflectionClass(str_replace(".", "\\", $child->getName()));
						$memento = $mementoClass->newInstanceWithoutConstructor();
						foreach ($mementoClass->getProperties() as $property) {
							$property->setAccessible(true);
							$property->setValue($memento, $child[$property->getName()]);
						}
						$po = $memento->restore();
						$this->cache[$id] = $po;
						return $po;
					}
				}
			}
		}
		return null;
	}

	public function filter (string $persistableClass, Closure $predicate): Collection
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