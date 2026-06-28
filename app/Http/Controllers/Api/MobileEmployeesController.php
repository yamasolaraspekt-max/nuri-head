<?php
 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobileEmployeesController extends Controller
{
    private function resolvePhotoUrl($imageName)
    {
        if (!$imageName) return null;
        if (Str::startsWith($imageName, ['http://', 'https://'])) return $imageName;

        $clean = ltrim($imageName, '/');
        $domain = config('app.url');
        if (Str::contains($clean, 'images/employee')) return $domain.'/'.$clean;
        return $domain.'/images/employee/'.$clean;
    }

    public function index(Request $request)
    {
        $q = trim((string)$request->input('q',''));
        $status = $request->input('status','Active');

        $rows = DB::table('employees')
            ->whereNull('deleted_at')
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('name','like',"%{$q}%")
                      ->orWhere('lastname','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%");
                });
            })
            ->orderBy('name')
            ->limit((int)$request->input('limit', 50))
            ->get(['id','name','lastname','status','image']);

        $data = $rows->map(fn($e) => [
            'id'     => (int)$e->id,
            'name'   => trim($e->name.' '.$e->lastname),
            'status' => $e->status,
            'img'    => $this->resolvePhotoUrl($e->image),
        ])->values();

        return response()->json(['ok' => true, 'data' => $data]);
    }
}
