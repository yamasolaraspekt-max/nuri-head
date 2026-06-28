<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\UserPreference; 
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{

    public function load()
    {
        $user = Auth::user();
        // Wir suchen die Präferenz oder geben Standardwerte zurück, falls keine existiert
        $prefs = UserPreference::where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'data'    => $prefs
        ]);
    }
    public function save(Request $request)
    {
        $data = $request->validate([
            'default_tab' => 'nullable|string',
            'show_thumbnails' => 'nullable|boolean',
            'show_calc_sidebar' => 'nullable|boolean',
            'list_columns' => 'nullable|array',
        ]);

        $preference = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return response()->json(['success' => true, 'preferences' => $preference]);
    }
}