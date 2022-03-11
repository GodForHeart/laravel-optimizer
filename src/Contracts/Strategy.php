<?php

namespace Godforheart\LaravelOptimizer\Contracts;

use Illuminate\Http\Request;

interface Strategy
{
    public function getKey(Request $request): string;
}