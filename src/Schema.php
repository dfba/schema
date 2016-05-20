<?php

namespace Dfba\Schema;

class Schema {

	protected $name = '';
	protected $characterSet = null;
	protected $collation = null;

	protected $tables = [];

	public function __construct(array $attributes=[]) {
		$this->setAttributes($attributes);
	}

	public function setAttributes(array $attributes) {
		foreach ($attributes as $key => $value) {
			if (!property_exists($this, $key)) {
				throw new \InvalidArgumentException("Invalid attribute: $key");
			}

			$this->{$key} = $value;
		}
	}

	public function getName() {
		return $this->name;
	}

	public function getCharacterSet() {
		return $this->characterSet;
	}

	public function getCollation() {
		return $this->collation;
	}

	public function getTables() {
		return $this->tables;
	}

	public function hasTable($name) {
		return !!$this->getTable($name);
	}

	public function getTable($name) {
		foreach ($this->tables as $table) {
			if ($table->getName() == $name) {
				return $table;
			}
		}

		return null;
	}

	public function addTable(Table $table) {
		$this->tables[] = $table;
	}

	public function newTable(array $attributes=[]) {
		return new Table($this, $attributes);
	}

}