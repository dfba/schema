<?php

namespace Dfba\Schema;

use PDO;

class Manager {

	protected $cachedSchemas = [];

	public function clearCache() {

		$this->cachedSchemas = [];
	}

	protected function addSchemaToCache(PDO $pdo, $schemaName, $schema) {

		$this->cachedSchemas[] = [
			'pdo' => $pdo,
			'name' => $schemaName,
			'schema' => $schema,
		];

	}

	protected function getCachedSchemaIndex(PDO $pdo, $schemaName) {

		foreach ($this->cachedSchemas as $index => $cachedSchema) {
			if ($cachedSchema['pdo'] === $pdo && $cachedSchema['name'] == $schemaName) {
				return $index;
			}
		}

		return false;
	}

	public function hasSchemaInCache(PDO $pdo, $schemaName) {

		return $this->getCachedSchemaIndex($pdo, $schemaName) !== false;
	}

	public function getSchemaFromCache(PDO $pdo, $schemaName) {

		$index = $this->getCachedSchemaIndex($pdo, $schemaName);

		if ($index !== false) {
			return $this->cachedSchemas[$index]['schema'];

		} else {
			return null;
		}
	}

	public function removeSchemaFromCache(PDO $pdo, $schemaName) {

		$index = $this->getCachedSchemaIndex($pdo, $schemaName);

		if ($index !== false) {
			array_splice($this->cachedSchemas, $index, 1);
		}
	}

	public function getSchema(PDO $pdo, $schemaName) {

		if ($this->hasSchemaInCache($pdo, $schemaName)) {

			return $this->getSchemaFromCache($pdo, $schemaName);

		} else {
			
			$schemaFactory = $this->newSchemaFactory($pdo);
			$schema = $schemaFactory->fetchSchema($pdo, $schemaName);
			$this->addSchemaToCache($pdo, $schemaName, $schema);

			return $schema;
		}
	}

	protected function getDriverName(PDO $pdo) {
		return $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	public function newMySqlSchemaFactory() {
		return new MySqlSchemaFactory();
	}

	public function newSchemaFactory(PDO $pdo) {
		$driverName = $this->getDriverName($pdo);
		$methodName = 'new'. $driverName .'SchemaFactory';

		return $this->{$methodName}();
	}


}