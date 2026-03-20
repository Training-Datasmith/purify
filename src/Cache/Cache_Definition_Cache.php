<?php

declare (strict_types=1);
namespace Stevebauman\Purify\Cache;

use Html_Purifier_definition;
use Html_Purifier_definition_Cache;
use Illuminate\Support\Facades\Cache;
class Cache_Definition_Cache extends Html_Purifier_definition_Cache
{
    /**
     * The cache repository.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;
    /**
     * Constructor.
     *
     * @param string $type
     */
    public function __construct($type)
    {
        parent::__construct($type);
        $this->cache = Cache::driver(config('purify.serializer.driver'));
    }
    /**
     * Adds a definition object to the cache.
     *
     * @param HTMLPurifier_Definition $def
     * @param \HTMLPurifier_Config    $config
     *
     * @return bool|void
     */
    public function add($def, $config)
    {
        if (!$this->check_def_type($def)) {
            return;
        }
        $key = $this->generate_key($config);
        if ($this->cache->has($key)) {
            return false;
        }
        return $this->cache->put($key, $this->encode($def));
    }
    /**
     * Unconditionally saves a definition object to the cache.
     *
     * @param HTMLPurifier_Definition $def
     * @param \HTMLPurifier_Config    $config
     *
     * @return bool|void
     */
    public function set($def, $config)
    {
        if (!$this->check_def_type($def)) {
            return;
        }
        $key = $this->generate_key($config);
        return $this->cache->put($key, $this->encode($def));
    }
    /**
     * Replace an object in the cache.
     *
     * @param HTMLPurifier_Definition $def
     * @param \HTMLPurifier_Config    $config
     *
     * @return bool|void
     */
    public function replace($def, $config)
    {
        if (!$this->check_def_type($def)) {
            return;
        }
        $key = $this->generate_key($config);
        if (!$this->cache->has($key)) {
            return false;
        }
        return $this->cache->put($key, $this->encode($def));
    }
    /**
     * Retrieves a definition object from the cache.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return bool|HTMLPurifier_Definition
     */
    public function get($config)
    {
        $key = $this->generate_key($config);
        if (!$this->cache->has($key)) {
            return false;
        }
        return $this->decode($this->cache->get($key));
    }
    /**
     * Removes a definition object to the cache.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function remove($config)
    {
        $key = $this->generate_key($config);
        if (!$this->cache->has($key)) {
            return false;
        }
        return $this->cache->delete($key);
    }
    /**
     * Clears all objects from cache.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function flush($config)
    {
        return $this->cache->clear();
    }
    /**
     * Clears all expired (older version or revision) objects from cache.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function cleanup($config)
    {
        $key = $this->generate_key($config);
        if ($this->is_old($key, $config)) {
            return $this->cache->delete($key);
        }
        return true;
    }
    /**
     * Encode the definition for storage.
     *
     * @param HTMLPurifier_Definition $def
     *
     * @return string
     */
    protected function encode($def)
    {
        return base64_encode(serialize($def));
    }
    /**
     * Decode the definition from storage.
     *
     * @param string $def
     *
     * @return HTMLPurifier_Definition
     */
    protected function decode($def)
    {
        // Backwards compatibility with old cache definitions.
        $instance = @unserialize($def);
        if ($instance instanceof Html_Purifier_definition) {
            return $instance;
        }
        return unserialize(base64_decode($def));
    }
}