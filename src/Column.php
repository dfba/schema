<?php

namespace Dfba\Schema;

class Column {

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
	protected $precision = null;
	protected $scale = null;
	protected $characterSet = null;
	protected $collation = null;
	protected $comment = null;

	protected $table = null;

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

	public function getDataType() {
		return $this->dataType;
	}

	public function getUnsigned() {
		return $this->unsigned;
	}

	public function getZerofill() {
		return $this->zerofill;
	}

	public function getNullable() {
		return $this->nullable;
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getAutoIncrement() {
		return $this->autoIncrement;
	}

	public function getMaximumLength() {
		return $this->maximumLength;
	}

	public function getMinimumValue() {
		return $this->minimumValue;
	}

	public function getMaximumValue() {
		return $this->maximumValue;
	}

	public function getPrecision() {
		return $this->precision;
	}

	public function getScale() {
		return $this->scale;
	}

	public function getCharacterSet() {
		return $this->characterSet;
	}

	public function getCollation() {
		return $this->collation;
	}

	public function getComment() {
		return $this->comment;
	}

}