<?php namespace Arcanedev\LaravelMessenger;

use Arcanedev\Support\Providers\PackageServiceProvider;

/**
 * Class     LaravelMessengerServiceProvider
 *
 * @package  Arcanedev\LaravelMessenger
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LaravelMessengerServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'messenger';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->bindModels();
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->publishConfig();

        Messenger::$runsMigrations ? $this->loadMigrations() : $this->publishMigrations();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            Contracts\Discussion::class,
            Contracts\Message::class,
            Contracts\Participation::class,
        ];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Bind the models.
     */
    private function bindModels(): void
    {
        $config   = $this->app['config'];
        $bindings = [
            Contracts\Discussion::class    => 'discussions',
            Contracts\Message::class       => 'messages',
            Contracts\Participation::class => 'participations',
        ];

        foreach ($bindings as $contract => $key) {
            $this->bind($contract, $config->get("{$this->package}.{$key}.model"));
        }
    }
}
