<?php

namespace App\Http\Controllers\Wordpress;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FusionFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('throttle:10,1'); // 10 requests per minute
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
        {
            $token = $request->header('X-Fusion-Form-Token');
            $expectedToken = config('services.fusion_forms.token');

            // Validate token
            if (!$token || $token !== $expectedToken) {
                Log::warning('Unauthorized Fusion Form submission attempt.');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data = $request->all();

            // ✅ Log or Save
            Log::info('Fusion Form Data Received', $data);

            // 🔧 Example: Save to DB (if you have a model)
            // FormSubmission::create($data);

            return response()->json(['message' => 'Form data received securely']);
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
