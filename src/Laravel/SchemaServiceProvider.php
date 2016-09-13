<?php

namespace Dfba\Schema\Laravel;

use Dfba\Schema\Manager;
use Dfba\Schema\Schema;
use Illuminate\Database\Connection;

class SchemaServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager();
        });

        $this->app->singleton(Schema::class, function ($app) {
            $manager = app(Manager::class);
            $connection = app(Connection::class);

            return $manager->getSchema(
                $connection->getReadPdo(),
                $connection->getDatabaseName()
            );
        });
    }

    public function boot(Manager $manager, Connection $connection)
    {
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Manager::class, Schema::class];
    }

}