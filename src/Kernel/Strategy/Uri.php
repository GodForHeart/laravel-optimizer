<?php

namespace Godforheart\LaravelOptimizer\Kernel\Strategy;

use Godforheart\LaravelOptimizer\Contracts\Strategy;
use Illuminate\Http\Request;

class Uri implements Strategy
{
    public function getKey(Request $request): string
    {
        $domain = '';
        if ($request->route()) {
            $domain = $request->route()->getDomain() ?? '';
        }
        return sha1($domain . '|' . $request->getUri());
    }
}