<?php

namespace IamLab\Core\Cache;

use Phalcon\Cache\Adapter\AdapterInterface;
use Phalcon\Cache\Adapter\Apcu;
use Phalcon\Cache\Adapter\Memory;
use Phalcon\Cache\Adapter\Redis;
use Phalcon\Cache\Adapter\Stream;
use Phalcon\Cache\Cache;
use Phalcon\Storage\SerializerFactory;
use RuntimeException;

class CacheFlush
{
    protected SerializerFactory $serializerFactory;
    protected array $config;
    protected array $instances = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->serializerFactory = new SerializerFactory();
    }

    /**
     * Get a cache layer instance.
     *
     * @param string|null $layerName
     * @return Cache
     */
    public function getLayer(?string $layerName = null): Cache
    {
        $layerName = $layerName ?: ($this->config['default'] ?? 'file');

        if (isset($this->instances[$layerName])) {
            return $this->instances[$layerName];
        }

        if (!isset($this->config['layers'][$layerName])) {
            throw new RuntimeException("Cache layer '{$layerName}' is not defined in configuration.");
        }

        $layerConfig = $this->config['layers'][$layerName];
        $adapter = $this->createAdapter($layerConfig);

        return $this->instances[$layerName] = new Cache($adapter);
    }

    /**
     * Flush cache.
     *
     * @param string|null $layer The layer to flush. If null, behavior depends on config and force flag.
     * @param bool $force If true, flushes all layers regardless of config.
     * @return void
     */
    public function flush(?string $layer = null, bool $force = false): void
    {
        $flushAllByDefault = $this->config['flush_all_by_default'] ?? true;

        if ($layer === null) {
            $layers = $this->config['layers'] ?? [];
            if ($layers instanceof \Phalcon\Config\Config) {
                $layers = $layers->toArray();
            }

            if ($flushAllByDefault || $force) {
                foreach (array_keys($layers) as $name) {
                    $this->getLayer($name)->clear();
                }
            } else {
                $this->getLayer()->clear();
            }
        } else {
            $this->getLayer($layer)->clear();
        }
    }

    /**
     * Create an adapter instance.
     *
     * @param array|\Phalcon\Config\Config $config
     * @return AdapterInterface
     */
    protected function createAdapter($config): AdapterInterface
    {
        if (is_object($config) && method_exists($config, 'toArray')) {
            $config = $config->toArray();
        }

        $adapterName = strtolower($config['adapter'] ?? 'stream');
        $adapterConfig = $config;
        unset($adapterConfig['adapter']);

        return match ($adapterName) {
            'redis' => new Redis($this->serializerFactory, $adapterConfig),
            'apcu' => new Apcu($this->serializerFactory, $adapterConfig),
            'stream', 'file' => $this->createStreamAdapter($adapterConfig),
            'memory' => new Memory($this->serializerFactory, $adapterConfig),
            default => throw new RuntimeException("Unsupported cache adapter: {$adapterName}"),
        };
    }

    /**
     * Create a stream adapter.
     *
     * @param array $config
     * @return Stream
     */
    protected function createStreamAdapter(array $config): Stream
    {
        if (isset($config['cacheDir']) && !is_dir($config['cacheDir'])) {
            if (!@mkdir($config['cacheDir'], 0775, true) && !is_dir($config['cacheDir'])) {
                throw new RuntimeException("Could not create cache directory: {$config['cacheDir']}");
            }
        }

        return new Stream($this->serializerFactory, $config);
    }
}
