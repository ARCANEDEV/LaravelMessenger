<?php namespace Arcanedev\LaravelMessenger;

use Arcanedev\Support\PackageServiceProvider;

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
    protected $package = 'laravel-messenger';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();

        $this->registerConfig();
        $this->bindModels();
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();

        $this->publishConfig();

        Messenger::$runsMigrations ? $this->loadMigrations() : $this->publishMigrations();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
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
    private function bindModels()
    {
        $config   = $this->config();
        $bindings = [
            'discussions'    => Contracts\Discussion::class,
            'messages'       => Contracts\Message::class,
            'participations' => Contracts\Participation::class,
        ];

        foreach ($bindings as $key => $contract) {
            $this->bind($contract, $config->get("{$this->package}.{$key}.model"));
        }
    }
}
