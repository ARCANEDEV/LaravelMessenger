<?php namespace Arcanedev\LaravelMessenger\Tests;

use Arcanedev\LaravelMessenger\LaravelMessengerServiceProvider;

/**
 * Class     LaravelMessengerServiceProviderTest
 *
 * @package  Arcanedev\LaravelMessenger\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LaravelMessengerServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelMessenger\LaravelMessengerServiceProvider */
    private $provider;

    /* -----------------------------------------------------------------
     |  Main Functions
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(LaravelMessengerServiceProvider::class);
    }

    public function tearDown()
    {
        unset($this->provider);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Arcanedev\Support\ServiceProvider::class,
            \Arcanedev\Support\PackageServiceProvider::class,
            \Arcanedev\LaravelMessenger\LaravelMessengerServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            \Arcanedev\LaravelMessenger\Contracts\Discussion::class,
            \Arcanedev\LaravelMessenger\Contracts\Message::class,
            \Arcanedev\LaravelMessenger\Contracts\Participant::class,
        ];

        $this->assertSame($expected, $this->provider->provides());
    }
}
