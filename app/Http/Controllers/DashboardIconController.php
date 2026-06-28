<?php

namespace App\Http\Controllers;

use App\Models\DashboardIcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardIconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }


    private $defaultOrder = [
        'my_inquiries',
        'my_leads',
        'my_tasks',
        'my_plannings',
        'my_offers',
        'my_orders',
        'my_projects',
        'my_tickets',
        'my_chats'
    ];

     public function index()
        {
            $user = Auth::user();

            // Check if the user has a saved order
            $savedOrder = DB::table('dashboard_icons')
                ->where('user_id', $user->id)
                ->value('card_order');

            // Use default order if no saved order exists
            $cardOrder = $savedOrder ? json_decode($savedOrder, true) : $this->defaultOrder;

            return view('dashboard', compact('cardOrder'));
        }

        public function saveOrder(Request $request)
        {
            $user = Auth::user();
            $newOrder = $request->input('order');

            DB::table('dashboard_icons')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'card_order' => json_encode($newOrder),
                    'updated_at' => now()
                ]
            );

            return response()->json(['success' => true]);
        }
    /**
     * Display the specified resource.
     */
    public function show(DashboardIcon $dashboardIcon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DashboardIcon $dashboardIcon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DashboardIcon $dashboardIcon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DashboardIcon $dashboardIcon)
    {
        //
    }
}
