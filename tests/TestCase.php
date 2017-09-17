<?php namespace Arcanedev\LaravelMessenger\Tests;

use Arcanedev\LaravelMessenger\Tests\Stubs\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Database\Eloquent\Factory as ModelFactory;

/**
 * Class     TestCase
 *
 * @package  Arcanedev\LaravelMessenger\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Database\Eloquent\Factory */
    protected $factory;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->migrate();
        $this->loadFactories();
        $this->seedTables();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class,
            \Arcanedev\LaravelMessenger\LaravelMessengerServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            //
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Laravel App Configs
        $app['config']->set('database.default', 'testing');
        $app['config']->set('auth.model', Stubs\Models\User::class);

        // Laravel Messenger Configs
        $app['config']->set('laravel-messenger.database.connection', 'testing');
        $app['config']->set('laravel-messenger.users.model', Stubs\Models\User::class);
    }

    /**
     * Load Model Factories.
     */
    private function loadFactories()
    {
        $this->factory = $this->app->make(ModelFactory::class);
        $this->factory->load(__DIR__.'/fixtures/factories');
    }

    /**
     * Migrate the tables.
     */
    protected function migrate()
    {
        $this->artisan('migrate', [
            '--database' => 'testing',
            '--realpath' => realpath(__DIR__.'/../database/migrations'),
        ]);

        $this->artisan('migrate', [
            '--database' => 'testing',
            '--realpath' => realpath(__DIR__.'/fixtures/migrations'),
        ]);
    }

    /**
     * Seed the tables.
     */
    private function seedTables()
    {
        $this->factory->of(User::class)->times(3)->create();
    }
}
