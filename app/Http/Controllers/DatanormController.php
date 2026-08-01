<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatanormController extends Controller
{
    public function showForm()
    {
        return view('admin.datanorm.upload');
    }

    public function parseFile(Request $request)
    {
        $request->validate([
            'datanorm_file' => 'required|file|mimes:txt,001',
        ]);

        $file = $request->file('datanorm_file');
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $articles = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, 'T;A;')) {
                $parts = explode(';', $line);

                $articleId = $parts[2] ?? null;
                $text = trim($parts[8] ?? '');

                if ($articleId) {
                    $articles[$articleId][] = $text;
                }
            }
        }

        $parsedData = [];

        foreach ($articles as $id => $texts) {
            $parsedData[] = [
                'article_no' => $id,
                'description' => implode(' ', $texts),
            ];
        }

        return view('admin.datanorm.upload', compact('parsedData'));
    }
}
