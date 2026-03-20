<?php

declare (strict_types=1);
namespace Stevebauman\Purify;

use Html_Purifier_config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use InvalidArgumentException;
use Stevebauman\Purify\Definitions\Css_Definition;
use Stevebauman\Purify\Definitions\Definition;
class Purify_Manager extends Manager
{
    /**
     * The filesystem manager instance.
     *
     * @var \Illuminate\Filesystem\FilesystemManager
     */
    protected $filesystem;
    /**
     * Constructor.
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->filesystem = $container->make('filesystem');
    }
    /**
     * Convenience alias for driver().
     *
     * @param string|array|null $config
     *
     * @return Purify
     *
     * @throws \InvalidArgumentException
     */
    public function config($config = null)
    {
        return $this->driver($config);
    }
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function get_default_driver()
    {
        return $this->config->get('purify.default');
    }
    /**
     * Get a driver instance.
     *
     * @param string|array|null $driver
     *
     * @return Purify
     *
     * @throws \InvalidArgumentException
     */
    public function driver($driver = null)
    {
        // First, we will check if the provided "driver" is an array. If so,
        // we're dealing with an inline defined config. We'll serialize it
        // into a string to dynamically define and set its configuration.
        if (is_array($driver)) {
            $config = $driver;
            $driver = md5(serialize($driver));
            $this->config->set("purify.configs.{$driver}", $config);
        }
        return parent::driver($driver);
    }
    /**
     * Create a new driver instance.
     *
     * @param string|array $driver
     *
     * @return Purify
     *
     * @throws \InvalidArgumentException
     */
    protected function create_driver($driver)
    {
        // First, we will determine if a custom driver creator exists for the given driver and
        // if it does not we will check for a creator method for the driver. Custom creator
        // callbacks allow developers to build their own "drivers" easily using Closures.
        if (isset($this->custom_creators[$driver])) {
            return $this->call_custom_creator($driver);
        }
        if ($config = $this->resolve_config($driver)) {
            return $this->create_instance($driver, $config);
        }
        throw new InvalidArgumentException("Purify config [{$driver}] not defined.");
    }
    /**
     * Resolve the configuration for the given config name.
     *
     * @param string $name
     *
     * @return array
     */
    protected function resolve_config($name)
    {
        return $this->config->get("purify.configs.{$name}");
    }
    /**
     * Resolve the serializer filepath the given config name.
     *
     * @param string $name
     *
     * @return string|false
     */
    protected function resolve_serializer_path($name)
    {
        $path = $this->config->get('purify.serializer.path');
        if (empty($path)) {
            return false;
        }
        return implode(DIRECTORY_SEPARATOR, [$path, $name]);
    }
    /**
     * Create a new Purify instance with the given config.
     *
     *
     * @return Purify
     */
    protected function create_instance(string $name, array $config)
    {
        $serializer_path = $this->resolve_serializer_path($name);
        if (!empty($serializer_path)) {
            $this->prepare_filesystem_storage($serializer_path);
        }
        return new Purify($this->create_html_config(array_merge(array_filter(['Cache.SerializerPath' => $serializer_path]), $config)));
    }
    /**
     * Prepare the serializer path in the filesystem storage.
     *
     *
     * @return void
     */
    protected function prepare_filesystem_storage(string $serializer_path)
    {
        $disk = $this->config->get('purify.serializer.disk');
        if (empty($disk)) {
            return;
        }
        $storage = $this->filesystem->disk($disk);
        if (!$storage->exists($serializer_path)) {
            $storage->make_directory($serializer_path);
        }
    }
    /**
     * Create an HTML purifier configuration instance.
     *
     * @param array $config
     *
     * @return HTMLPurifier_Config
     */
    protected function create_html_config($config)
    {
        $html_config = Html_Purifier_config::create($config);
        $html_config->set('HTML.DefinitionID', 'HTML-purify');
        $html_config->set('HTML.DefinitionRev', 1);
        $html_config->set('Cache.DefinitionImpl', config('purify.serializer.cache'));
        if ($definition = $html_config->maybe_get_raw_html_definition()) {
            $definitions_class = $this->config->get('purify.definitions');
            if ($definitions_class && is_a($definitions_class, Definition::class, true)) {
                $definitions_class::apply($definition);
            }
        }
        if ($definition = $html_config->get_css_definition()) {
            $definitions_class = $this->config->get('purify.css-definitions');
            if ($definitions_class && is_a($definitions_class, Css_Definition::class, true)) {
                $definitions_class::apply($definition);
            }
        }
        return $html_config;
    }
}