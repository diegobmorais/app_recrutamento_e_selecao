<?php

namespace Modules\Recruitment\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Modules\Recruitment\Entities\JobCustomQuestion;

class AssistantOpenAI
{ 
  protected $prompts;
  public function __construct(OpenAIPromptGenerator $prompts)
  {
    $this->prompts = $prompts;
  }
    public function createAssistant($typeAssistant, $jobs)
    {   
        Auth::user()->isAbleTo('job manage');
        
        $questions = JobCustomQuestion::where('job_id', $jobs->id)->get();    
        $prompts = new OpenAIPromptGenerator();     

        if ($questions && $jobs) { 
            $instructions = $prompts->generateInstructions($jobs, $questions, $typeAssistant);

            $data = [
                'instructions' => $instructions,
                'name' => $this->createNameAssistant($jobs, $typeAssistant),
                'tools' => [['type' => 'code_interpreter']],
                'model' => 'gpt-4o'
            ];

            return $this->callApiOpenAI($data);
        }

        return ['error' => 'Vaga ou perguntas não encontradas'];
    }

    private function createNameAssistant($jobs, $typeAssistant)
    {
        if ($typeAssistant == 'pre-selection') {
            return 'Assistant pre-selection' . $jobs->title;
        } elseif ($typeAssistant == 'behavioral-test') {
            return 'behavioral-test' . $jobs->title;
        }
    }

    private function callApiOpenAI($data)
    {
        $url = "https://api.openai.com/v1/assistants";
        $apiKey = config('services.openai.api_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
            'OpenAI-Beta' => 'assistants=v2'
        ])
        ->withoutVerifying()
        ->post($url, $data);

        return $response->json();
    }
}
