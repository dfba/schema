<?php

namespace Dfba\Schema;

use PDO;

class SQLiteSchemaFactory extends SchemaFactory {

	public function fetchSchema(PDO $pdo, $schemaName) {
		$schemaAttributes = $this->querySchemas($pdo, [$schemaName]);
		
		if (count($schemaAttributes)) {
			$schema = $this->newSchema($schemaAttributes[0]);

			foreach ($this->queryTables($pdo, $schema) as $tableAttributes) {
				$table = $this->newTable($schema, $tableAttributes);

				foreach ($this->queryColumns($pdo, $table) as $columnAttributes) {
					$column = $this->newColumn($table, $columnAttributes);

					$table->addColumn($column);
				}

				$schema->addTable($table);
			}

			return $schema;

		} else {
			return null;
		}
	}

	protected function executeSelectQuery(PDO $pdo, $query, array $parameters=[]) {

		$statement = $pdo->prepare($query);
		$statement->execute($parameters);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
		
	}

	protected function sqlIn(&$parameters, $column, $values) {
		if (is_array($values)) {

			if (count($values)) {
				$parameters = array_merge($parameters, $values);
				return "$column IN (". implode(',', array_fill(0, count($values), '?')) .")";

			} else {
				return "(0=1)";
			}

		} else {
			return "(1=1)";
		}
	}

	protected function querySchemas(PDO $pdo, $schemas=null) {

		$databases = $this->executeSelectQuery($pdo, "PRAGMA database_list");
		$encoding = $this->executeSelectQuery($pdo, "PRAGMA encoding")[0]['encoding'];

		$results = [];
		foreach ($databases as $database) {
			
			if ($schemas === null || in_array($database['name'], $schemas)) {
				$results[] = [
					'name' => $database['name'],
					'file' => $database['file'],
					'characterSet' => $encoding,
				];
			}
		}

		return $results;
	}

	protected function queryTables(PDO $pdo, Schema $schema, $tables=null) {

		$parameters = [];

		$conditions = $this->sqlIn($parameters, 'name', $tables);

		$results = $this->executeSelectQuery($pdo, 
			"SELECT 
				name
			FROM ".$pdo->quote($schema->getName()).".sqlite_master 
			WHERE 
				type='table' AND 
				name NOT LIKE 'sqlite\\_%' ESCAPE '\\' AND
				$conditions
			ORDER BY name ASC", $parameters);

		return array_map(function($result) use($schema) {
			return array_merge($result, [
				'characterSet' => $schema->getCharacterSet(),
			]);
		}, $results);
	}

	protected function queryColumns(PDO $pdo, Table $table, $columns=null) {

		$schemaName = $table->getSchema()->getName();
		$tableName = $table->getName();

		$columnResults = $this->executeSelectQuery($pdo, 
			"PRAGMA ".$pdo->quote($schemaName).".table_info(".$pdo->quote($tableName).")"
		);


		$columnAttributes = [];
		foreach ($columnResults as $result) {
			
			$name = $result['name'];
			$dataType = strtolower($result['type']);

			if ($columns === null || in_array($name, $columns)) {
				$columnAttributes[] = [
					'name' => $name,
					'dataType' => $dataType,
					'nullable' => !$result['notnull'],
					'defaultValue' => $result['dflt_value'],
					'maximumLength' => $this->getMaximumLength($dataType),
					'minimumValue' => $this->getMinimumValue($dataType),
					'maximumValue' => $this->getMaximumValue($dataType),
					'unsigned' => false,
					'zerofill' => false,
					'autoIncrement' => null,
					'characterSet' => $table->getSchema()->getCharacterSet(),
				];
			}
		}

		return $columnAttributes;

	}

	protected function getMaximumLength($dataType) {

		switch ($dataType) {
			case 'text':
			case 'blob':
				return 1000000000; /* Default SQLITE_MAX_LENGTH */
		}

		return null;

	}

	protected function getMaximumValue($dataType) {

		switch ($dataType) {
			case 'integer':
				return '9223372036854775807';
		}

		return null;
	}

	protected function getMinimumValue($dataType) {

		switch ($dataType) {
			case 'integer':
				return '-9223372036854775808';
		}

		return null;
	}

}