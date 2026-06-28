<?php
// MAIN APP: Customers endpoint (new_leads search)
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileCustomersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->input('q',''));

        $rows = DB::table('new_leads')
            ->whereNull('deleted_at')
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('firma','like',"%{$q}%")
                      ->orWhere('name','like',"%{$q}%")
                      ->orWhere('lastname','like',"%{$q}%")
                      ->orWhere('full_address','like',"%{$q}%")
                      ->orWhere('city','like',"%{$q}%")
                      ->orWhere('phone','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->limit((int)$request->input('limit', 20))
            ->get(['id','firma','name','lastname','full_address','phone','city','latitude','longitude']);

        $data = $rows->map(fn($c) => [
            'id' => (int)$c->id,
            'label' => trim(($c->firma ?: '').' '.($c->name ?: '').' '.($c->lastname ?: '')),
            'full_address' => $c->full_address,
            'phone' => $c->phone,
            'city' => $c->city,
            'lat' => $c->latitude,
            'lon' => $c->longitude,
        ])->values();

        return response()->json(['ok' => true, 'data' => $data]);
    }
}
