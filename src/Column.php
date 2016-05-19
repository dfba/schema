<?php

namespace Dfba\Schema;

class Column {

	protected $table = null;

	protected $name = '';
	protected $dataType = '';
	protected $unsigned = false;
	protected $zerofill = false;
	protected $nullable = false;
	protected $defaultValue = null;
	protected $options = null;
	protected $autoIncrement = false;
	protected $maximumLength = null;
	protected $minimumValue = null;
	protected $maximumValue = null;
	protected $characterSet = null;
	protected $collation = null;
	protected $comment = null;

	public function __construct(Table $table, array $attributes=[]) {
		$this->table = $table;

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

	public function getTable() {
		return $this->table;
	}

	public function getName() {
		return $this->name;
	}

}