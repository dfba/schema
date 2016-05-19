<?php

namespace Dfba\Schema;

class Table {

	protected $schema = null;

	protected $name = '';
	protected $engine = null;
	protected $characterSet = null;
	protected $collation = null;
	protected $comment = '';

	protected $columns = [];

	public function __construct(Schema $schema, array $attributes=[]) {
		$this->schema = $schema;

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

	public function getSchema() {
		return $this->schema;
	}

	public function getName() {
		return $this->name;
	}

	public function getColumns() {
		return $this->columns;
	}

	public function hasColumn($name) {
		return !!$this->getColumn($name);
	}

	public function getColumn($name) {
		foreach ($this->columns as $column) {
			if ($column->getName() == $name) {
				return $column;
			}
		}

		return null;
	}

	public function addColumn(Column $column) {
		$this->columns[] = $column;
	}

	public function newColumn(array $attributes=[]) {
		return new Column($this, $attributes);
	}

}