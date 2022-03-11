<?php

namespace Godforheart\LaravelOptimizer\Kernel\Strategy;

use Godforheart\LaravelOptimizer\Contracts\Strategy;

class Request implements Strategy
{
    public function getKey(\Illuminate\Http\Request $request): string
    {
        return 'request';
    }
}