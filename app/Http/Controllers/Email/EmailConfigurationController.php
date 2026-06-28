<?php

namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;

use App\Mail\LeadEmail;
use App\Models\EmailConfiguration;
use App\Models\Leads;
use Illuminate\Http\Request;
use DB;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Illuminate\Support\Facades\Mail;
use App\Models\SendEmail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;  


class EmailConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        $search=request()->query('search');

        if($search){
            
            $data=DB::table('email_configurations')
                        ->where('name', 'LIKE', "%$search%")
                        ->orWhere('host', 'LIKE', "%$search%")
                        ->orWhere('user', 'LIKE', "%$search%")
                        ->paginate(10);
                return view('admin.leads.configuration.configuration')->with('data', $data);
        }
        else{
            
            $data=DB::table('email_configurations')
            ->paginate(10);
                return view('admin.leads.configuration.configuration')->with('data', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
     
        $this->validate($request, [
            'name' =>  'required',
            'host' =>  'required',
            'port' =>  'required',
            'username' =>  'required',
            'password' =>  'required',
        ]);

        EmailConfiguration::create($request->all());

        return redirect()->to('/email_configuration')->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert.');
    }

   
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:email_configurations,id', 
            'name' => 'required',
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);
    
        $id = $request->input('id');
        EmailConfiguration::where('id', $id)->update($request->except(['_token', '_method']));
    
        return redirect()->to('/email_configuration')->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=EmailConfiguration::find($id);
        $data->delete();
        return redirect()->to('/email_configuration')->with('save_msg', 'Der Datensatz wurde erfolgreich gelöscht');

    }

    public function publish($id){
        $duplicate=DB::table('email_configurations')->where('id', $id)->where('status', '=', 'Published')->first();

        if($duplicate){
            return redirect()->back()->with('delete_msg', 'You have already connected to one email');

        }
        else{
            $data=EmailConfiguration::find($id);
            $data->status="Published";
            $data->save();
            return redirect()->to('/email_configuration')->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert.');
        }
    }

    public function unpublish($id){
     
            $data=EmailConfiguration::find($id);
            $data->status="Unpublished";
            $data->save();
            return redirect()->to('/email_configuration')->with('delete_msg', 'Der Datensatz wurde erfolgreich gespeichert.');
       
    }

    public function test($id){
     
        $data=EmailConfiguration::find($id);
       
        $cm = new ClientManager(config_path('imap.php'));
        $client = $cm->make([
            'host'          => $data->host,
            'port'          => $data->port,
            'encryption'    => $data->encryption,
            'validate_cert' => true,
            'username'      => $data->username,
            'password'      => $data->password,
            'protocol'      => $data->protocol
        ]);
        
       
        try {
            $client->connect();
            $data->test = "tested";
            $data->save();
            return redirect()->back()->with('save_msg', 'IMAP test is successful');
        } catch (\Webklex\PHPIMAP\Exceptions\ConnectionFailedException $e) {
            $data->test = "not tested";
            $data->save();
            return redirect()->back()->with('delete_msg', 'IMAP connection failed. Errors: ' . $e->getMessage());
        }


      
}

public function send(Request $request)
{
    $config = DB::table('email_configurations')->where('username', $request->from)->first();

    if (!$config) {
        return response()->json(['error' => 'No email config found.']);
    }

    // Build transport
    $transport = new EsmtpTransport(
        $config->host,
        $config->port,
        $config->encryption === 'ssl' ? true : false
    );
    $transport->setUsername($config->username);
    $transport->setPassword($config->password);

    $mailer = new Mailer($transport);

    // Prepare data for your Mailable
    $data = [
        'from' => $config->username,
        'to' => $request->to,
        'subject' => $request->subject,
        'body' => $request->message
    ];

    // Send using custom mailer instance
    $leadEmail = new LeadEmail($data);

    $mailer->send($leadEmail->toSymfonyEmail());

    return back()->with('success', 'Email sent successfully!');
}

 
}
