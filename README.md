# A database schema inspector...
... a very lightweight one. Iterate through all your databases, tables and columns without having to lookup the correct query syntax for your specific database system.

```
composer install dfba/schema
```

## Examples

If you're using Laravel this will be _extra_ easy:

Add the following line to the `'providers'` section of your `app.php` config file:
```php
Dfba\Schema\Laravel\SchemaServiceProvider::class,
```

From this point on you can inject `Dfba\Schema\Schema` into your application. For example:
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Dfba\Schema\Schema; // Very crucial line

class ExampleController extends BaseController {
    
	public function test(Schema $schema) {
		// --------------^ HERE


		// Some demo code:
		echo "<b>". $schema->getName() ."</b><br>";

		foreach ($schema->getTables() as $table) {
			echo "__ <b>". $table->getName() ."</b><br>";

			foreach ($table->getColumns() as $column) {
				echo "__ __ <b>". 
					$column->getName() ."</b><br>";

				echo "__ __ __ <i>dataType:</i> ".
					$column->getDataType() ."<br>";
				echo "__ __ __ <i>unsigned:</i> ".
					$column->getUnsigned() ."<br>";
				echo "__ __ __ <i>zerofill:</i> ".
					$column->getZerofill() ."<br>";
				echo "__ __ __ <i>nullable:</i> ".
					$column->getNullable() ."<br>";
				echo "__ __ __ <i>defaultValue:</i> ".
					$column->getDefaultValue() ."<br>";
				echo "__ __ __ <i>options:</i> ".
					implode(', ', $column->getOptions() ?: []) ."<br>";
				echo "__ __ __ <i>autoIncrement:</i> ".
					$column->getAutoIncrement() ."<br>";
				echo "__ __ __ <i>maximumLength:</i> ".
					$column->getMaximumLength() ."<br>";
				echo "__ __ __ <i>minimumValue:</i> ".
					$column->getMinimumValue() ."<br>";
				echo "__ __ __ <i>maximumValue:</i> ".
					$column->getMaximumValue() ."<br>";
				echo "__ __ __ <i>precision:</i> ".
					$column->getPrecision() ."<br>";
				echo "__ __ __ <i>scale:</i> ".
					$column->getScale() ."<br>";
				echo "__ __ __ <i>characterSet:</i> ".
					$column->getCharacterSet() ."<br>";
				echo "__ __ __ <i>collation:</i> ".
					$column->getCollation() ."<br>";
				echo "__ __ __ <i>comment:</i> ".
					$column->getComment() ."<br>";
			}
		}

	}
}
```

Injecting `Dfba\Schema\Schema` in your code will fetch the schema for the currently [configured database](https://laravel.com/docs/master/database#introduction). That means you'll need to have a database set up and configured ;)

Note: it's possible to read the database metadata for _any_ open connection, not just the default Laravel one. The `Dfba\Schema\Manager` will happily burp out `Schema`s as long as you feed it PDO connections and database names:
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Dfba\Schema\Manager; // Very crucial line

class ExampleController extends BaseController {

	public function test(Manager $manager) {
		
		$anyRandomPdo = \DB::connection()->getReadPdo();
		$someSchemaName = 'example';

		$schema = $manager->getSchema($anyRandomPdo, $someSchemaName);

		var_dump($schema);
		
	}
}
```

### Good plain ol' PHP (or other frameworks)
I haven't forgotten about you:
```php
$schemaManager = new Dfba\Schema\Manager();
$anyRandomPdo = new PDO("mysql:host=localhost;dbname=example", "username", "password");
$someSchemaName = 'example';

$schema = $schemaManager->getSchema($anyRandomPdo, $someSchemaName);

var_dump($schema);
```

You should be aware of that the `Dfba\Schema\Manager` caches retrieved schemas to prevent fetching the same data multiple times. If you create new `Dfba\Schema\Manager` instances over and over again, you won't benefit from the cache.

## Postgres, SQLite, SQL Server, etc.?
Oh... yeah. MySQL is the only database currently implemented. I have extracted all the database specific code into it's own file, though. You want other databases implemented? Open an [issue](../../issues), or better yet: copy [src/MySqlSchemaFactory.php](../src/MySqlSchemaFactory.php) and do it yourself. It's not that hard! :)