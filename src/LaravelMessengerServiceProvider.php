<?php namespace Arcanedev\LaravelMessenger;

use Arcanedev\LaravelMessenger\Contracts as MessengerContracts;
use Arcanedev\Support\PackageServiceProvider as ServiceProvider;

/**
 * Class     LaravelMessengerServiceProvider
 *
 * @package  Arcanedev\LaravelMessenger
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LaravelMessengerServiceProvider extends ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'laravel-messenger';

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname(__DIR__);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
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
        $this->publishMigrations();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            MessengerContracts\Discussion::class,
            MessengerContracts\Message::class,
            MessengerContracts\Participant::class,
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Bind the models.
     */
    private function bindModels()
    {
        $config   = $this->config();
        $bindings = [
            'discussions'  => MessengerContracts\Discussion::class,
            'messages'     => MessengerContracts\Message::class,
            'participants' => MessengerContracts\Participant::class,
        ];

        foreach ($bindings as $key => $contract) {
            $this->bind($contract, $config->get("{$this->package}.{$key}.model"));
        }
    }
}
