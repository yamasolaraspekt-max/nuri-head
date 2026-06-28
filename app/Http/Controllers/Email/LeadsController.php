<?php

namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;

use App\Models\Leads;
use Illuminate\Http\Request;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use App\Models\EmailConfiguration;
use Illuminate\Support\Facades\File;
use DB;
use App\Models\Card;
use App\Models\Customer;


class LeadsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function fetchAndDisplayEmails()
{

    $data = EmailConfiguration::where('status', '=', 'Published')->where('test', '=', 'tested')->first();

$cm = new ClientManager(config_path('imap.php'));

// Fill the data for the $client variable
$client = $cm->make([
    'host' => $data->host,
    'port' => $data->port,
    'encryption' => $data->encryption,
    'validate_cert' => true,
    'username' => $data->username,
    'password' => $data->password,
    'protocol' => $data->protocol,
]);

// Connect to the IMAP Server
if ($client->connect()) {
    // Get the "Inbox" folder
    $inboxFolder = $client->getFolder('INBOX');

    // Check if the "Inbox" folder exists
    if ($inboxFolder) {
        // Get all Messages of the "Inbox" folder
        /** @var \Webklex\PHPIMAP\Support\MessageCollection $inboxMessages */
        $inboxMessages = $inboxFolder->messages()->all()->limit(200)->get();

        // Loop through each message in the "Inbox"
        foreach ($inboxMessages as $message) {
            // Check if the email already exists in the Leads table
            $existingEmail = Leads::where('subject', $message->getSubject())->first();
            //dd($message);
            // If the email does not exist, save it in the database
            if (!$existingEmail) {
                // Get "from" attribute
                $fromAttribute = $message->getAttributes()['from'];
                //dd($fromAttribute);
                // Check if "from" attribute is available and not empty
                if (!empty($fromAttribute)) {
                    // Extract individual values from Address object
                    $senderName = $fromAttribute[0]->personal ?? null;
                    $senderEmail = $fromAttribute[0]->mail ?? null;
                }

                // Get "to" attribute
                $toAttribute = $message->getAttributes()['to'];
                //dd($toAttribute);
                // Check if "to" attribute is available and not empty
                if (!empty($toAttribute)) {
                    // Extract individual values from Address object
                    $recipientName = $toAttribute[0]->mail ?? null;
                    
                }

                // Save the individual values in the database
                $savedEmail = new Leads([
                    'subject' => $message->getSubject(),
                    'body' => $message->getHTMLBody(),
                    'attachments_count' => $message->getAttachments()->count(),
                    'sender_name' => $senderName,
                    'sender_email' => $senderEmail,
                    'recipient_name' => $recipientName,
                    'status' => "Unpublished"
                    // Add more columns as needed
                ]);

                $savedEmail->save();
            }
        }
    } else {
        // Log or handle the case when the "Inbox" folder is not found
        echo "Inbox folder not found.";
    }

    // Disconnect from the IMAP server
    $client->disconnect();
} else {
    // Log or handle the connection error
    echo "IMAP connection failed. Errors: ";
}

$data['data'] = Leads::orderBy('id', 'desc')->get();
$data['leads'] = Leads::select('recipient_name')->distinct()->get(['recipient_name']);

return redirect()->to('email_view')->with(['data' => $data]);


     }

     public function index(){

        $search=request()->query('search');
        if($search){
            $data['data'] = Leads::where('subject', 'LIKE', "%$search%")
            ->orWhere('sender_email', 'LIKE', "%$search%")
            ->orWhere('sender_name', 'LIKE', "%$search%")
            ->orWhere('recipient_name', 'LIKE', "%$search%")
            ->orderBy('id', 'desc')->get();
            $data['leads'] = Leads::select('recipient_name')->distinct()->get(['recipient_name']);
            $data['emails']=EmailConfiguration::where('status', '=', 'Published')->where('test', '=', 'tested')->get();
        }
        else{
            $data['data'] = Leads::orderBy('id', 'desc')->get();
            $data['leads'] = Leads::select('recipient_name')->distinct()->get(['recipient_name']);
            $data['emails']=EmailConfiguration::where('status', '=', 'Published')->where('test', '=', 'tested')->get(); 
        }

      
        return view('admin.leads.email.email_view', $data);
     }

     public function customer($id, $name, $email)
{
    $check = DB::table('customers')
        ->where('email', '=', $email)
        ->first();

    if ($check) {
        return redirect()->back()->with('save_msg', 'The customer is already saved');
    } else {
       
        $data= session(['customer' => $name, 'customer_email'    => $email]);
        return redirect()->to('customer_create')->with('data', $data);
    }
}

}



     

