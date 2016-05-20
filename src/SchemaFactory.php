<?php

namespace Dfba\Schema;

use PDO;

abstract class SchemaFactory {
	abstract public function fetchSchema(PDO $pdo, $schemaName);

	protected function newSchema(array $attributes=[]) {
		return new Schema($attributes);
	}

	protected function newTable(Schema $schema, array $attributes=[]) {
		return $schema->newTable($attributes);
	}

	protected function newColumn(Table $table, array $attributes=[]) {
		return $table->newColumn($attributes);
	}

}