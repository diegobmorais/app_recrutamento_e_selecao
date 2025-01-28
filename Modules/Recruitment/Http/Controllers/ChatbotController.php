<?php

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Job;
use Modules\Recruitment\Entities\JobApplication;
use Illuminate\Support\Facades\Http;
use Modules\Recruitment\Entities\JobInterviewCandidate;

class ChatbotController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try {
            $jobId = $request->get('jobId');
            $jobApplicationId = $request->get('jobApplicationId');
            $testType = $request->get('testType');

            if (!$jobId || !$jobApplicationId || !$testType) {
                return redirect()->back()->with('error', __('Missing required parameters.'));
            }

            $job = Job::find($jobId);
            if (!$job) {
                return redirect()->back()->with('error', __('Job not found.'));
            }

            $assistantId = null;
            if ($testType === 'pre-selection') {
                $assistantId = $job->id_assistant_openai_pre_selection;
            } elseif ($testType === 'behavioral-test') {
                $assistantId = $job->id_assistant_openai_behavioral_test;
            }

            if (!$assistantId) {
                return redirect()->back()->with('error', __('Assistant not found for the given test type.'));
            }

            $jobApplication = JobApplication::find($jobApplicationId);
            if (!$jobApplication) {
                return redirect()->back()->with('error', __('Candidate application not found.'));
            }

            return view('recruitment::chatBot.index', compact('jobId', 'jobApplicationId', 'testType', 'assistantId'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('An error occurred while preparing the chatbot.'));
        }
    }


    public function getAssistant($jobId, $testType)
    {
        $job = Job::find($jobId);

        if (!$jobId) {
            return response()->json([
                'error' => 'Job not found'
            ], 404);
        }
        if ($testType == 'pre-selection') {
            $id_assistant_openai = $job->id_assistant_openai_pre_selection;
        } elseif ($testType == 'behavioral-test') {
            $id_assistant_openai = $job->id_assistant_openai_behavioral_test;
        } else {
            return response()->json([
                'error' => 'Invalid test typee'
            ], 400);
        }
        return response()->json([
            'assistantId' => $id_assistant_openai
        ]);
    }

    public function createThread()
    {
        $openAiUrl = "https://api.openai.com/v1/threads";
        $apiKey = config('services.openai.api_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
            'OpenAI-Beta' => 'assistants=v2'
        ])
            ->withoutVerifying()
            ->post($openAiUrl);

        $responseData = $response->json();

        return response()->json(['threadId' => $responseData['id'] ?? null]);
    }

    public function sendMessage(Request $request)
    {
        try {
            $data = $request->validate([
                'message' => 'required|string',
                'threadId' => 'required|string',
                'candidateId' => 'nullable|integer',
                'testType' => 'nullable|string'
            ]);

            $openAiUrl = "https://api.openai.com/v1/threads/{$data['threadId']}/messages";
            $apiKey = config('services.openai.api_key');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $apiKey",
                'OpenAI-Beta' => 'assistants=v2'
            ])
                ->withoutVerifying()
                ->post($openAiUrl, [
                    'role' => 'user',
                    'content' => $data['message']
                ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to communicate with OpenAI'], 500);
            }

            $responseData = $response->json();

            if (isset($data['candidateId'])) {
                JobInterviewCandidate::create([
                    'job_application_id' => $data['candidateId'],
                    'sender' => 'candidate',
                    'content' => $data['message'],
                    'test_type' => $data['testType']
                ]);
            }

            return response()->json(['response' => $responseData]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function runAssistant(Request $request, $threadId)
    {
        $data = $request->validate([
            'assistantId' => 'required|string'
        ]);

        $openAiUrl = "https://api.openai.com/v1/threads/{$threadId}/runs";
        $apiKey = config('services.openai.api_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
            'OpenAI-Beta' => 'assistants=v2'
        ])
            ->withoutVerifying()
            ->post($openAiUrl, [
                'assistant_id' => $data['assistantId']
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to communicate with OpenAI', 'details' => $response->body()], 500);
        }

        $responseData = $response->json();

        if (!isset($responseData['id'])) {
            return response()->json(['error' => 'Run ID not found in the response'], 500);
        }

        return response()->json(['runId' => $responseData['id']]);
    }

    public function recoverThread($threadId, $runId)
    {
        $openAiUrl = "https://api.openai.com/v1/threads/{$threadId}/runs/{$runId}";
        $apiKey = config('services.openai.api_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
            'OpenAI-Beta' => 'assistants=v2'
        ])
            ->withoutVerifying()
            ->get($openAiUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'API error: ' . $response->reason()], $response->status());
        }

        return response()->json(['thread' => $response->json()]);
    }

    public function getResponse($threadId)
    {
        $openAiUrl = "https://api.openai.com/v1/threads/{$threadId}/messages";
        $apiKey = config('services.openai.api_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
            'OpenAI-Beta' => 'assistants=v2'
        ])
            ->withoutVerifying()
            ->get($openAiUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'API error: ' . $response->reason()], $response->status());
        }

        $responseData = $response->json();

        if (isset($responseData['data'][0]['content'][0]['text']['value'])) {
            $message = $responseData['data'][0]['content'][0]['text']['value'];
            return response()->json(['response' => $message]);
        }

        return response()->json(['error' => 'No message found in response'], 404);
    }

    public function saveSummary(Request $request, $candidateId)
    {
        $data = $request->validate([
            'mensagemIA' => 'required|string',
            'testType' => 'required|string'
        ]);

        $candidate = JobApplication::find($candidateId);

        if (!$candidate) {
            return response()->json(['error' => 'Candidate not found'], 404);
        }

        $aiResponse = $this->parseAIResponse($data['mensagemIA']);

        if (!isset($aiResponse['score'], $aiResponse['summary'])) {
            return response()->json(['error' => 'Invalid AI response format'], 400);
        }

        $candidate->update([
            'final_score' => $aiResponse['score'],
            'final_summary' => $aiResponse['summary']
        ]);

        $testAvailable = json_decode($candidate->test_available, true);
        
        if (is_array($testAvailable)) {
            $testAvailable['pre_selection'] = 1; 
            $candidate->test_available = json_encode($testAvailable);
            $candidate->save();
        }

        $job = Job::find($candidate->job);

        if ($job->activete_behavioral_test == 1 && $aiResponse['score'] >= $job->average) {
            $newChatRoute = route('recruitment.chatbot', [
                'jobApplicationId' => $candidateId,
                'jobId' => $job->id,
                'name' => $candidate->name,
                'testType' => 'behavioral-test',
                'assistantId' => $job->id_assistant_openai_behavioral_test
            ]);
            return response()->json(['redirect' => $newChatRoute]);
        }
        return response()->json(['message' => 'Summary saved successfully']);
    }

    private function parseAIResponse($aiResponse)
    {
        preg_match('/```json(.*?)```/s', $aiResponse, $jsonMatch);

        if (!isset($jsonMatch[1])) {
            return [
                'score' => null,
                'summary' => null,
            ];
        }

        $parsedJson = json_decode(trim($jsonMatch[1]), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'score' => null,
                'summary' => null,
            ];
        }

        return [
            'score' => $parsedJson['pontuacao'] ?? null,
            'summary' => $parsedJson['resumo'] ?? null,
        ];
    }
}
