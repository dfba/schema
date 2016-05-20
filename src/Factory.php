<?php

namespace Dfba\Schema;

use PDO;

class Factory {

	protected $cachedSchemas = [];

	public function getSchemaFromCache(PDO $pdo, $schemaName) {

		foreach ($this->cachedSchemas as $cachedSchema) {
			if ($cachedSchema->getPdo() == $pdo && $cachedSchema->getName() == $schemaName) {
				return $cachedSchema;
			}
		}

		return null;
	}

	public function clearCache($pdo=null) {

		if ($pdo) {

			foreach ($this->cachedSchemas as $i => $cachedSchema) {
				if ($cachedSchema->getPdo() == $pdo) {
					array_splice($this->cachedSchemas, $i, 1);
					break;
				}
			}

		} else {
			$this->cachedSchemas = [];
		}
		
	}

	protected function addSchemaToCache(Schema $schema) {

		$this->cache[] = $schema;

	}

	public function getSchema(PDO $pdo, $schemaName) {

		$schema = $this->getSchemaFromCache($pdo, $schemaName);

		if ($schema) {
			return $schema;
		}

		$schemaFactory = $this->newSchemaFactory($pdo);
		$schema = $schemaFactory->fetchSchema($pdo, $schemaName);

		if ($schema) {
			$this->addSchemaToCache($schema);
		}

		return $schema;
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