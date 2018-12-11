<?php

namespace AppBundle\Service;


use AppBundle\Model\StorageQuery;
use AppBundle\Model\StorageResult;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StorageService implements \ArrayAccess
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function dumpKey($args)
    {
        $output = [];
        foreach ($args as $k => $arg) {
            $output[] = $k;
            if (is_array($arg)) {
                $output[] = $this->dumpKey($arg);
            } else {
                $output[] = $arg;
            }
        }

        return implode('|', $output);
    }


    /**
     * @param StorageResult $result
     * @return \AppBundle\Model\StorageValue[]|\Propel\Runtime\Collection\ObjectCollection
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getResultValues(StorageResult $result)
    {
        return $result->getStorageValues();
    }

    public function getStorage($storageCode)
    {
        $storage = StorageQuery::create()->findOneByCode($storageCode);
        return $this->container->get('serializer')->normalize($storage);
    }

    public function generateStorageCacheKey($storageCode)
    {
        return http_build_query([__METHOD__, $storageCode]);
    }

    public function getStorageFieldData($storageCode, $fieldCode)
    {
        $storageData = $this->getStorageData($storageCode);

        if (array_key_exists($fieldCode, $storageData)) {
            return $storageData[$fieldCode];
        }

        return null;
    }

    public function getStorageData($storageCode)
    {
        $cache = $this->container->get('cache.app');
        $cacheKey = $this->generateStorageCacheKey($storageCode);

        /** @var CacheItem $cacheItem */
        $cacheItem = $cache->getItem($cacheKey);
        $cacheItem->expiresAfter(3600 * 24);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $storage = $this->getStorage($storageCode);
        $cacheItem->set($storage);
        $cache->save($cacheItem);

        return $storage;
    }

    public function clearStorageDataCache($storageCode)
    {
        $cache = $this->container->get('cache.app');
        $cacheKey = $this->generateStorageCacheKey($storageCode);
        $cache->deleteItem($cacheKey);
    }

    public function offsetExists($offset) { return true; }

    public function offsetGet($offset)
    {
        if (preg_match('/^([a-z\d_-]+)\.([a-z\d_-]+)$/usi', $offset, $match)) {
            return $this->getStorageFieldData($match[1], $match[2]);
        }

        return $this->getStorageData($offset);
    }

    public function offsetSet($offset, $value) { }

    public function offsetUnset($offset) { }
}