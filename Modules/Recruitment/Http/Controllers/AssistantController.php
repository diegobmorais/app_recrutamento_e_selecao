<?php

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Job;
use Modules\Recruitment\Services\AssistantOpenAI;

class AssistantController extends Controller
{   
    protected $assistantService;
    public function __construct(AssistantOpenAI $assistantService)
    {
        $this->assistantService = $assistantService;
    }
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
        $validaded = $request->validate([
            'job_id' => 'required|exists:jobs,id',
        ]);

        $job = Job::findOrFail($request->job_id);
    
        $preSelectionResponse = $this->assistantService->createAssistant('pre-selection', $job);
        if (isset($preSelectionResponse['error'])) {
            return response()->json(['error' => $preSelectionResponse['error']], 500);
        }

        $behavioralTestResponse = $this->assistantService->createAssistant('behavioral-test', $job);
        if (isset($behavioralTestResponse['error'])) {
            return response()->json(['error' => $behavioralTestResponse['error']], 500);
        }

        $job->update([
            'id_assistant_openai_pre_selection' => $preSelectionResponse['id'] ?? null,
            'id_assistant_openai_behavioral_test' => $behavioralTestResponse['id'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Assistant created successfully',
            'data' => [
                'pre_selection_id' => $preSelectionResponse,
                'behavioral_test_id' => $behavioralTestResponse,
            ],
        ]);
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
}
