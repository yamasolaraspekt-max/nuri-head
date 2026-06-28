<?php
namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;

use App\Models\LeadEmailAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;  
use Webklex\IMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;
use App\Models\LeadEmail;
use Illuminate\Http\JsonResponse;

class LeadEmailAccountsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function unreadRealtimeData(): JsonResponse
    {
        $count = LeadEmail::query()
            ->where('is_read', false)
            ->count();

        $latest = LeadEmail::query()
            ->latest('date')
            ->take(8)
            ->get([
                'id',
                'message_id',
                'from',
                'domain',
                'subject',
                'date',
                'status',
                'is_read',
            ]);

        return response()->json([
            'count' => $count,
            'latest' => $latest,
        ]);
    }

    public function markEmailAsRead($id): JsonResponse
    {
        $email = LeadEmail::findOrFail($id);
        $email->is_read = true;
        $email->status = 'read';
        $email->save();

        return response()->json([
            'success' => true,
        ]);
    }
    public function index(Request $request)
    {
        $query = LeadEmailAccounts::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            });
        }

        $accounts = $query->latest()->get();

        return view('admin.lead_email.email_config.account', compact('accounts'));
    }


    public function create()
    {
        return view('admin.lead_email.email_config.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'email' => 'required|email|unique:lead_email_accounts,email',
            'password' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'required|string',
            'status' => 'required|string|in:Published,Unpublished',
        ]);

        // Explizite Whitelist statt $request->all() (kein Mass-Assignment-Risiko); Cast verschluesselt das Passwort.
        LeadEmailAccounts::create($request->only(['label', 'email', 'password', 'host', 'port', 'encryption', 'status', 'test']));

        return redirect()->route('lead-email-accounts.index')->with('save_msg', 'E-Mail-Konto wurde erfolgreich erstellt.');
    }

   public function edit($id)
    {
        $account = LeadEmailAccounts::findOrFail($id);
        return view('admin.lead_email.email_config.edit', compact('account'));
    }

  public function update(Request $request, $id)
    {
        $account = LeadEmailAccounts::findOrFail($id);

        $request->validate([
            'label' => 'required|string|max:255',
            'email' => 'required|email|unique:lead_email_accounts,email,' . $account->id,
            'password' => 'nullable|string', // leer lassen = Passwort unveraendert
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'required|string',
            'status' => 'required|string|in:Published,Unpublished',
        ]);

        $data = [
            'label' => $request->label,
            'email' => $request->email,
            'host' => $request->host,
            'port' => $request->port,
            'encryption' => $request->encryption,
            'status' => $request->status,
        ];
        if (filled($request->password)) {
            $data['password'] = $request->password; // nur bei Eingabe -> Eloquent verschluesselt via Cast
        }
        $account->update($data);

        return redirect()
            ->route('lead-email-accounts.index')
            ->with('updated_msg', 'E-Mail-Konto wurde erfolgreich aktualisiert.');
    }
   public function destroy($id)
    {
        $account = LeadEmailAccounts::find($id);

        if (!$account) {
            return redirect()->route('lead-email-accounts.index')->with('delete_msg', 'Konto nicht gefunden.');
        }

        $account->delete();

        return redirect()->route('lead-email-accounts.index')->with('delete_msg', 'E-Mail-Konto wurde gelöscht.');
    }



    public function toggleStatus($id)
    {
        $account = LeadEmailAccounts::findOrFail($id);
        $account->status = $account->status === 'Published' ? 'Unpublished' : 'Published';
        $account->save();

        return response()->json([
            'new_status' => $account->status
        ]);
    }

 
    public function testConnection($id)
{
    $account = LeadEmailAccounts::findOrFail($id);

    Log::info("🔍 IMAP Test gestartet für: {$account->email}");

    try {
        $client = Client::make([
            'host'          => $account->host,
            'port'          => $account->port,
            'encryption'    => $account->encryption,
            'validate_cert' => true,
            'username'      => $account->email,
            'password'      => $account->password,
            'protocol'      => 'imap'
        ]);

        $client->connect();

        $account->test = '✅ Erfolgreich';
        $account->save();

        Log::info("✅ IMAP connection successful for: {$account->email}");

        return response()->json([
            'success' => true,
            'message' => 'IMAP connection successful for ' . $account->email
        ]);
    } catch (ConnectionFailedException $e) {
        $account->test = '❌ Fehlgeschlagen: ' . $e->getMessage();
        $account->save();

        Log::error("❌ IMAP connection failed for {$account->email}: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'IMAP connection failed: ' . $e->getMessage()
        ]);
    }
}

}
