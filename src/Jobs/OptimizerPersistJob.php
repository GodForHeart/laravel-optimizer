<?php

namespace Godforheart\LaravelOptimizer\Jobs;

use Godforheart\LaravelOptimizer\Contracts\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Closure;

class OptimizerPersistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var string
     */
    private $method;
    /**
     * @var array|string
     */
    private $persistArgs;

    public function __construct(Storage $storage, string $method, $persistArgs)
    {
        $this->storage = $storage;
        $this->method = $method;
        $this->persistArgs = $persistArgs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->storage->{$this->method}($this->persistArgs);
    }

}
