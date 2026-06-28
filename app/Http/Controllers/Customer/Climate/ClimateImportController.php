<?php

namespace App\Http\Controllers\Customer\Climate;
use App\Http\Controllers\Controller;

use App\Jobs\ImportClimateWorkbookJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClimateImportController extends Controller
{
    public function create()
    {
        return view('admin.climate.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        try {
            $uploadedFile = $request->file('file');

            $storedPath = $uploadedFile->storeAs(
                'climate-imports',
                now()->format('Ymd_His') . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $uploadedFile->getClientOriginalName()),
                'local'
            );

            ImportClimateWorkbookJob::dispatch($storedPath, $uploadedFile->getClientOriginalName());

            return redirect()
                ->route('admin.climate.import.create')
                ->with('success', 'Climate import has been queued successfully. Please make sure the queue worker is running.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.climate.import.create')
                ->with('error', 'Could not queue import: ' . $e->getMessage());
        }
    }
}