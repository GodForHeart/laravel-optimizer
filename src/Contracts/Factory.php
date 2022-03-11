<?php

namespace Godforheart\LaravelOptimizer\Contracts;

interface Factory
{
    public function resolve($name);

    public function configurationFor($name);
}