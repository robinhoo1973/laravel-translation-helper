<?php

namespace TopviewDigital\TranslationHelper\Service;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AsyncBroker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function handle()
    {
        if (is_object($this->service) && method_exists($this->service, 'handle')) {
            $this->service->handle();
        }
    }
}
