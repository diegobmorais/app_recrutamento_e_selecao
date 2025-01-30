<?php

namespace Modules\Recruitment\Services;

use Modules\Recruitment\Services\SendWhatsapp;
use Exception;

class SendTestBehavior
{
    public function sendTest(string $candidateName, string $url, string $toNumber)
    {    
        if (!$url) {
            throw new Exception("URl não encontrada.");
        }
        
        $message = "Olá, {$candidateName}! 🎯\n\n";
        $message .= "Para concluir seu processo de seleção, aqui está seu *Teste Comportamental*:\n\n";
        $message .= "📌 *Acesse pelo link*: \n$url\n\n";
        $message .= "Boa sorte! 🚀";

        $whatsappService = new SendWhatsapp();
        return $whatsappService->sendText($message, $toNumber);
    }
}
