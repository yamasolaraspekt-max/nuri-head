<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BitrixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function contact_list()
    {
       // Full JSON data
        $json = '{
            "result": [
                {"NAME":"YAMA","LAST_NAME":"Nuri","ID":"8","EMAIL":[{"ID":"14","VALUE_TYPE":"WORK","VALUE":"hallo@nuri-software.de","TYPE_ID":"EMAIL"}]},
                {"NAME":"John","LAST_NAME":"Due","ID":"10"},
                {"NAME":"Ballmaier","LAST_NAME":"Daniel","ID":"12","EMAIL":[{"ID":"18","VALUE_TYPE":"WORK","VALUE":"daniel.ballmaier@gmx.de","TYPE_ID":"EMAIL"}]},
                {"NAME":"Bors","LAST_NAME":"Joachim","ID":"14","EMAIL":[{"ID":"22","VALUE_TYPE":"WORK","VALUE":"j.bors@t-online.de","TYPE_ID":"EMAIL"}]},
                {"NAME":"Petersen","LAST_NAME":"Birgit und Dr. Wolfgang","ID":"16","EMAIL":[{"ID":"28","VALUE_TYPE":"HOME","VALUE":"wope55@hotmail.com","TYPE_ID":"EMAIL"}]},
                {"NAME":"Velten-Hadamik","LAST_NAME":"Barbara","ID":"18","EMAIL":[{"ID":"32","VALUE_TYPE":"WORK","VALUE":"Barbara_Velten@t-online.de","TYPE_ID":"EMAIL"}]},
                {"NAME":"Schmidt","LAST_NAME":"Kunibert","ID":"20","EMAIL":[{"ID":"38","VALUE_TYPE":"HOME","VALUE":"kunibert.schmidt@gmx.de","TYPE_ID":"EMAIL"}]},
                {"NAME":"Illgen","LAST_NAME":"Andreas","ID":"24","EMAIL":[{"ID":"44","VALUE_TYPE":"HOME","VALUE":"illgena66@gmail.com","TYPE_ID":"EMAIL"}]},
                {"NAME":"Nicolai","LAST_NAME":"Jürgen","ID":"26"},
                {"NAME":"van den Adel","LAST_NAME":"Cornelis","ID":"28","EMAIL":[{"ID":"46","VALUE_TYPE":"WORK","VALUE":"cornelis@van-den-adel.de","TYPE_ID":"EMAIL"}]},
                {"NAME":"Kleemann","LAST_NAME":"Michael","ID":"30","EMAIL":[{"ID":"50","VALUE_TYPE":"HOME","VALUE":"sportmk@gmx.de","TYPE_ID":"EMAIL"}]}
            ],
            "next": 50,
            "total": 539,
            "time": {
                "start": 1731982505.3013489,
                "finish": 1731982505.366744,
                "duration": 0.065395116806030273,
                "processing": 0.037578105926513672,
                "date_start": "2024-11-19T05:15:05+03:00",
                "date_finish": "2024-11-19T05:15:05+03:00",
                "operating_reset_at": 1731983105,
                "operating": 0
            }
        }';

        // Decode JSON data
        $data = json_decode($json, true);

        // Display JSON structure with `dd`
        dd($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function stage()
    {
         // Full JSON data
        $json = '{
            "result":[
                {"NAME":"Auftragsanahme","SORT":10,"STATUS_ID":"UC_6V8414"},
                {"NAME":"AB erstellen\\/1. Abschlagrechnung","SORT":20,"STATUS_ID":"NEW"},
                {"NAME":"Projektierung","SORT":30,"STATUS_ID":"PREPARATION"},
                {"NAME":"Montage \\/ in Arbeit \\/ 2. Rechnung","SORT":40,"STATUS_ID":"EXECUTING"},
                {"NAME":"Syna-Anmeldung","SORT":50,"STATUS_ID":"UC_SX3EAA"},
                {"NAME":"Schlussrechnung stellen","SORT":60,"STATUS_ID":"UC_6ZIFGR"},
                {"NAME":"Reklamationen","SORT":70,"STATUS_ID":"UC_T7RTDM"},
                {"NAME":"Warten auf Zählertausch Lackmann","SORT":80,"STATUS_ID":"UC_GVIT1Y"},
                {"NAME":"Warten auf Zählertausch Syna","SORT":90,"STATUS_ID":"UC_HQHXT2"},
                {"NAME":"Warten auf Zählertausch Simon","SORT":100,"STATUS_ID":"UC_3ZKH9D"},
                {"NAME":"§14a noch nicht gemeldet, auch nachrüsten","SORT":110,"STATUS_ID":"UC_LCVTQW"},
                {"NAME":"§14a gemeldet, noch nachrüsten","SORT":120,"STATUS_ID":"UC_QJZZTI"},
                {"NAME":"14a nicht gemeldet, aber Relais vorhanden","SORT":130,"STATUS_ID":"UC_522XTD"},
                {"NAME":"14a noch abrechnen","SORT":140,"STATUS_ID":"UC_8K1RPP"},
                {"NAME":"14a-unfertig (z. B. Kabel, etc.)","SORT":150,"STATUS_ID":"UC_Z7CFJ7"},
                {"NAME":"After Sales","SORT":160,"STATUS_ID":"WON"},
                {"NAME":"Abschlussrechnung","SORT":170,"STATUS_ID":"LOSE"}
            ],
            "time":{
                "start":1731983258.1660709,
                "finish":1731983258.188447,
                "duration":0.022376060485839844,
                "processing":0.0039091110229492188,
                "date_start":"2024-11-19T05:27:38+03:00",
                "date_finish":"2024-11-19T05:27:38+03:00",
                "operating_reset_at":1731983858,
                "operating":0
            }
        }';

        // Decode the JSON into an associative array
        $data = json_decode($json, true);

        // Use dd() to view the data
        dd($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
