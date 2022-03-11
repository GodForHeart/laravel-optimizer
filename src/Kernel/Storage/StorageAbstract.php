<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

abstract class StorageAbstract
{
    protected $config;

    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
}