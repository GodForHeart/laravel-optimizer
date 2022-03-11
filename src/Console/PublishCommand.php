<?php

namespace Godforheart\LaravelOptimizer\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'optimizer:publish {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "资源发布";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = $this->option('force');
        $options = ['--provider' => 'Godforheart\LaravelOptimizer\OptimizerServiceProvider'];
        if ($force == true) {
            $options['--force'] = true;
        }
        $this->call('vendor:publish', $options);
    }
}