@extends('admin.layouts.app')

@section('title', 'IDS Schnittstelle - Test')

@section('styles')
    <style>
        body {
            background-color:rgb(248, 248, 246);
        }
    </style>
@endsection

@section('content')


    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-left mb-0">IDS Schnittstelle</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="#">Verlinkt</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
                          
            <div class="content-body">
            <div class="container mt-4">
        <form target="_blank" name="IDS_Test" action="https://gconlineplus.de/ids.aspx" method="post">
            <table class="table table-bordered bg-white">
                <tr>
                    <td>Shopauswahl</td>
                    <td>
                        <select id="shop_url" onchange="this.form.action=this.value" class="form-control">
                            <option value="https://gconlineplus.de/ids.aspx">GC Online Plus</option>
                            <option value="https://gutonlineplus.de/ids.aspx">GUT Online Plus</option>
                            <option value="https://htionlineplus.de/ids.aspx">HTI Online Plus</option>
                            <option value="https://efgonlineplus.de/ids.aspx">EFG Online Plus</option>
                            <option value="https://fkronlineplus.de/ids.aspx">FKR Online Plus</option>
                            <option value="https://itgonlineplus.de/ids.aspx">ITG Online Plus</option>
                            <option value="https://dtgonlineplus.de/ids.aspx">DTG Online Plus</option>
                            <option value="https://nfgonlineplus.de/ids.aspx">NFG Online Plus</option>
                            <option value="https://TFGonlineplus.de/ids.aspx">TFG Online Plus</option>
                        </select>
                        <input type="submit" name="Abschicken3" value="ausführen" class="btn btn-primary mt-2">
                    </td>
                </tr>
                <tr>
                    <td>Aktion</td>
                    <td>
                        <select name="action" id="Methode" class="form-control">
                            <option value="AS">AS - Artikelsuche</option>
                            <option value="ADL">ADL - Artikel Deeplink</option>
                            <option value="WKE">WKE - Warenkorb Empfang</option>
                            <option value="WKS">WKS - Warenkorb Senden</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Kundennummer</td>
                    <td><input type="text" name="kndnr" value="004444" class="form-control"></td>
                </tr>
                <tr>
                    <td>Benutzername</td>
                    <td><input type="text" name="name_kunde" value="idsinterntest" class="form-control"></td>
                </tr>
                <tr>
                    <td>Passwort</td>
                    <td><input type="password" name="pw_kunde" value="testintern" class="form-control"></td>
                </tr>
                <tr>
                    <td>Artikelnummer <br><small>(Nur ADL)</small></td>
                    <td><input type="text" name="ghnummer" value="DE55k" class="form-control"></td>
                </tr>
                <tr>
                    <td>Sucheingabe <br><small>(Nur AS)</small></td>
                    <td><input type="text" name="searchterm" value="Waschtisch derby 55" class="form-control"></td>
                </tr>
                <tr>
                    <td>Warenkorb <br><small>(Nur WKS)</small></td>
                    <td>
                        <textarea class="form-control" rows="25" name="warenkorb">@xml   encoding="UTF-8"?>
                            <Warenkorb xmlns="http://www.itek.de/Shop-Anbindung/Warenkorb/"
                                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                    xsi:schemaLocation="http://www.itek.de/Shop-Anbindung/Warenkorb/ warenkorbaustausch.xsd">
                                <!-- Your existing XML block here -->
                            </Warenkorb>
                                                    </textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>hookurl</td>
                                                <td><input type="text" name="hookurl" value="Dummy" class="form-control"></td>
                                            </tr>

                                            <tr>
                                                <td><strong>AS</strong></td>
                                                <td>Artikelsuche führt direkt zur Ergebnisdarstellung von ONLINE PLUS.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>ADL</strong></td>
                                                <td>Ruft die Artikeldetails eines Produkts auf.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>WKE</strong></td>
                                                <td>Warenkorb vom Shop in die Software übernehmen. Auch leer möglich.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>WKS</strong></td>
                                                <td>Warenkorb an Großhandel senden mit Rückgabe und Auftragsdaten.</td>
                                            </tr>
                                            <tr>
                                                <td style="color:red;">Debugging</td>
                                                <td>
                                                    <a href="https://web.microsoftstream.com/video/2dc63093-e3cf-436b-83aa-8be93d5b3bce" target="_blank">
                                                        Auslesen der Rückgabewerte
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>

            </div>
        </div>
    </div>
   
@endsection
