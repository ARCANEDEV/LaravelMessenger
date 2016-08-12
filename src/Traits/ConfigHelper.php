<?php namespace Arcanedev\LaravelMessenger\Traits;

/**
 * Trait     ConfigHelper
 *
 * @package  Arcanedev\LaravelMessenger\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait ConfigHelper
{
    /* ------------------------------------------------------------------------------------------------
     |  Helper Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get table from config.
     *
     * @param  string       $key
     * @param  string|null  $default
     *
     * @return string
     */
    protected function getTableFromConfig($key, $default = null)
    {
        return $this->getFromConfig("{$key}.table", $default);
    }

    /**
     * Get model from config.
     *
     * @param  string       $key
     * @param  string|null  $default
     *
     * @return string
     */
    protected function getModelFromConfig($key, $default = null)
    {
        return $this->getFromConfig("{$key}.model", $default);
    }

    /**
     * Get the value from the laravel-messenger config file.
     *
     * @param  string       $key
     * @param  string|null  $default
     *
     * @return string
     */
    protected function getFromConfig($key, $default = null)
    {
        return config("laravel-messenger.{$key}", $default);
    }
}
