<?php

declare (strict_types=1);
namespace Stevebauman\Purify\Cache;

use Html_Purifier_definition_Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Filesystem_Definition_Cache extends Html_Purifier_definition_Cache
{
    /**
     * The filesystem disk.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $disk;
    /**
     * Constructor.
     *
     * @param string $type
     */
    public function __construct($type)
    {
        parent::__construct($type);
        $this->disk = Storage::disk(config('purify.serializer.disk'));
    }
    /**
     * Adds a definition object to the cache.
     *
     * @param \HTMLPurifier_Definition $def
     * @param \HTMLPurifier_Config     $config
     *
     * @return bool|void
     */
    public function add($def, $config)
    {
        if (!$this->check_def_type($def)) {
            return;
        }
        $file = $this->generate_file_path($config);
        if ($this->disk->exists($file)) {
            return false;
        }
        return $this->disk->put($file, serialize($def));
    }
    /**
     * Unconditionally saves a definition object to the cache.
     *
     * @param \HTMLPurifier_Definition $def
     * @param \HTMLPurifier_Config     $config
     *
     * @return bool|void
     */
    public function set($def, $config)
    {
        if (!$this->check_def_type($def)) {
            return;
        }
        $file = $this->generate_file_path($config);
        return $this->disk->put($file, serialize($def));
    }
    /**
     * Replace an object in the cache.
     *
     * @param \HTMLPurifier_Definition $def
     * @param \HTMLPurifier_Config     $config
     *
     * @return bool|void
     */
    public function replace($def, $config)
    {
        if (!$this->check_def_type($def)) {
            return;
        }
        $file = $this->generate_file_path($config);
        if (!$this->disk->exists($file)) {
            return false;
        }
        return $this->disk->put($file, serialize($def));
    }
    /**
     * Retrieves a definition object from the cache.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return bool|\HTMLPurifier_Config
     */
    public function get($config)
    {
        $file = $this->generate_file_path($config);
        if (!$this->disk->exists($file)) {
            return false;
        }
        return unserialize($this->disk->get($file));
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
        $file = $this->generate_file_path($config);
        if (!$this->disk->exists($file)) {
            return false;
        }
        return $this->disk->delete($file);
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
        $dir = $this->generate_directory_path($config);
        foreach ($this->disk->files($dir) as $filename) {
            if (Str::starts_with($filename, '.')) {
                continue;
            }
            $this->disk->delete(implode(DIRECTORY_SEPARATOR, [$dir, $filename]));
        }
        return true;
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
        $dir = $this->generate_directory_path($config);
        foreach ($this->disk->files($dir) as $filename) {
            if (Str::starts_with($filename, '.')) {
                continue;
            }
            $key = substr($filename, 0, strlen($filename) - 4);
            if ($this->is_old($key, $config)) {
                $this->disk->delete(implode(DIRECTORY_SEPARATOR, [$dir, $filename]));
            }
        }
        return true;
    }
    /**
     * Generates the file path.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return string
     */
    public function generate_file_path($config)
    {
        $key = $this->generate_key($config);
        return $this->generate_directory_path($config) . 'DefinitionCache.php/' . $key . '.ser';
    }
    /**
     * Generates the path to the directory contain this cache's serial files.
     *
     * @note No trailing slash
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return string
     */
    public function generate_directory_path($config)
    {
        $base = $this->generate_base_directory_path($config);
        return $base . '/' . $this->type;
    }
    /**
     * Generates path to base directory that contains all definition type
     * serials.
     *
     * @param \HTMLPurifier_Config $config
     *
     * @return string
     */
    public function generate_base_directory_path($config)
    {
        $base = $config->get('Cache.SerializerPath');
        return is_null($base) ? '/' : $base;
    }
}