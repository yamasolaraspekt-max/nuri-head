<?php

namespace App\Http\Controllers\Customer\Offer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Events\ClipboardUpdated;

class ClipboardController extends Controller
{
    private function getCacheKey($userId) {
        return 'clipboard_user_' . $userId;
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $items = Cache::get($this->getCacheKey($userId), []);
        return response()->json(['success' => true, 'items' => $items]);
    }

   public function copy(Request $request)
    {
        $userId = auth()->id();
        $newItem = $request->input('item');
        
        $key = $this->getCacheKey($userId);
        $items = Cache::get($key, []);

        array_unshift($items, $newItem);
        $items = array_slice($items, 0, 20); // Keep max 20

        // In production (Hetzner), Cache limits might also be hit if using Redis/Memcached with small limits, 
        // but it's usually fine up to a few MBs.
        Cache::put($key, $items, now()->addHours(24));

        // ✅ ONLY send the Ping, no data!
        broadcast(new ClipboardUpdated($userId))->toOthers();

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function clear(Request $request)
    {
        $userId = auth()->id();
        Cache::forget($this->getCacheKey($userId));
        
        // ✅ ONLY send the Ping
        broadcast(new ClipboardUpdated($userId))->toOthers();

        return response()->json(['success' => true]);
    }
}