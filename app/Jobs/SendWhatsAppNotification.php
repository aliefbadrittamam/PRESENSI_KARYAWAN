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
    protected $template;
    protected $params;

    public function __construct($phone, $template, $params = [])
    {
        $this->phone = $phone;
        $this->template = $template;
        $this->params = $params;
    }

    public function handle(WhatsAppService $whatsapp)
    {
        $whatsapp->sendTemplate($this->phone, $this->template, $this->params);
    }
}