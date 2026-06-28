<?php
namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiChat;
use App\Models\NewLeads;
use Illuminate\Http\Request;

class ChatPageController extends Controller
{
    public function index(Request $req)
    {
        $chats = AiChat::query()
            ->where('user_id', $req->user()->id)
            ->latest('last_activity_at')->paginate(20);

        return view('ai.chat', compact('chats'));
    }

    public function show(Request $req, \App\Models\AiChat $chat)
    {
        $this->authorize('view', $chat);

        $chat->load([
            'messages' => fn($q) => $q->orderBy('id') // or created_at
        ]);

        return view('ai.chat_show', compact('chat'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
     
        \Log::info('requested chat', [$request->all()]);
        $results = NewLeads::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('lastname', 'like', "%{$q}%")
                      ->orWhere('full_address', 'like', "%{$q}%")
                      ->orWhere('street', 'like', "%{$q}%")
                      ->orWhere('city', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'lastname', 'full_address', 'city']);

        return response()->json($results);
    }

   public function byCustomerIds(Request $r)
{
    $ids = collect(explode(',', (string) $r->query('ids')))
        ->filter()->map('intval')->all();

    $rows = \App\Models\AiChat::query()
        ->select('id','customer_id','title','last_activity_at')
        ->where('user_id', $r->user()->id)    
        ->whereIn('customer_id', $ids)
        ->latest('last_activity_at')
        ->get();

    return response()->json($rows);
}


public function indexApi(\Illuminate\Http\Request $request)
{
    $chats = \App\Models\AiChat::query()
        ->where('user_id', $request->user()->id)
        ->with(['customer:id,name,lastname,city'])
        ->orderByDesc('last_activity_at')
        ->limit(100)
        ->get(['id','customer_id','title','last_activity_at']);

    return response()->json(
        $chats->map(function ($c) {
            return [
                'id'               => $c->id,
                'customer_id'      => $c->customer_id,
                'title'            => $c->title ?? ('Chat '.$c->id),
                'last_activity_at' => optional($c->last_activity_at)->toDateTimeString(),
                'customer'         => [
                    'id'       => $c->customer?->id,
                    'name'     => $c->customer?->name,
                    'lastname' => $c->customer?->lastname,
                    'city'     => $c->customer?->city,
                ],
            ];
        })
    );
}

}