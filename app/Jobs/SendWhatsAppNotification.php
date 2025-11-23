<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsAppService;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $templateName;
    protected $params;

    public function __construct($phone, $templateName, $params = [])
    {
        $this->phone = $phone;
        $this->templateName = $templateName;
        $this->params = $params;
    }

    public function handle(WhatsAppService $whatsappService)
    {
        $whatsappService->sendTemplate($this->phone, $this->templateName, $this->params);
    }
}