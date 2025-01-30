<?php

namespace Modules\Recruitment\Services;

use Modules\Recruitment\Services\SendWhatsapp;
use Exception;

class SendPreSelectionTest
{
  public function sendTest(string $candidateName, string $url, string $toNumber)
  {  
    if (!$url) {
      throw new Exception("URL não encontrada.");
    }
 
    $message = "Olá, {$candidateName}! 🎯\n\n";
    $message .= "Aqui está seu *Teste de Pré-seleção*:\n\n";
    $message .= "📌Para concluir sua candidatura, *Acesse o link*: \n$url\n\n";
    $message .= "Boa sorte! 🚀";

    $whatsappService = new SendWhatsapp();
  
    return $whatsappService->sendText($message, $toNumber);
  }
}
