<?php

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Job;
use Modules\Recruitment\Entities\JobApplication;
use Modules\Recruitment\Entities\JobStage;
use Modules\Recruitment\Services\SendPreSelectionTest;
use Modules\Recruitment\Services\SendTestBehavior;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('recruitment::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('recruitment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('recruitment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('recruitment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    public function sendTest($id)
    {
        try {
            $application = JobApplication::findOrFail($id);
            $job = Job::where('id', $application->job)->first();
            $stage = JobStage::where('id', $application->stage)->first();
           
            $testParams = [
                'pré-seleção' => [
                    'testType' => 'pre-selection',
                    'tokenKey' => 'pre_selection',
                    'assistantId' => $job->id_assistant_openai_pre_selection,
                    'whatsappService' => new SendPreSelectionTest(),
                    'successMessage' => 'Teste de pré-seleção enviado com sucesso.'
                ],
                'teste comportamental' => [
                    'testType' => 'behavioral-test',
                    'tokenKey' => 'behavioral_test',
                    'assistantId' => $job->id_assistant_openai_behavioral_test,
                    'whatsappService' => new SendTestBehavior(),
                    'successMessage' => 'Teste comportamental enviado com sucesso.'
                ]
            ];

            $stageName = strtolower(trim($stage->title));
         
            if (!isset($testParams[$stageName])) {
                return response()->json([
                    'success' => false,
                    'message' => 'O teste só pode ser enviado quando o card estiver na coluna correta.'
                ], 400);
            }

            $params = $testParams[$stageName];

            $testData = json_decode($application->test_tokens, true);
            if (!isset($testData[$params['tokenKey']]['token'])) {
                return response()->json(['message' => 'Token do teste não encontrado.'], 400);
            }
      
            $url = route('recruitment.chatbot', [
                'jobId' => $application->job,
                'jobApplicationId' => $application->id,
                'name' => $application->name,
                'testType' => $params['testType'],
                'assistantId' => $params['assistantId']
            ], true);
     
            $params['whatsappService']->sendTest($application->name, $url, $application->phone);

            return response()->json(['message' => $params['successMessage']]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro: ' . $e->getMessage()], 500);
        }
    }
}
