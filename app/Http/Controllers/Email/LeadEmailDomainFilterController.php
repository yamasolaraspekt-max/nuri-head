<?php
namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;

use App\Models\LeadEmailDomainFilter;
use Illuminate\Http\Request;

class LeadEmailDomainFilterController extends Controller
{
    /**
     * Show the list of domain filters.
     */
    public function index()
    {
        $filters = LeadEmailDomainFilter::latest()->get();
        return view('admin.lead_email.email_config.filter', compact('filters'));
    }

    /**
     * Store one or multiple new domain filters.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domains' => 'required|array|min:1',
            'domains.*' => 'required|string|distinct'
        ]);

        $newCount = 0;

        foreach ($request->domains as $domain) {
            $domain = strtolower(trim($domain));
            if (!LeadEmailDomainFilter::where('domain', $domain)->exists()) {
                LeadEmailDomainFilter::create(['domain' => $domain]);
                $newCount++;
            }
        }

        return redirect()->back()->with('save_msg', "$newCount Domain-Filter erfolgreich gespeichert.");
    }

    /**
     * Delete a domain filter by ID.
     */
    public function destroy($id)
    {
        $filter = LeadEmailDomainFilter::findOrFail($id);
        $filter->delete();

        return redirect()->back()->with('delete_msg', "Domain-Filter „{$filter->domain}“ wurde gelöscht.");
    }
}
