<style>
        body {
            font-family: Arial, sans-serif;
            background: white;
            padding: 20px;
            font-size: 12px;
            line-height: 1.5;
        }

        .a4-page {
            width: 358mm;
            height: 409mm;
            margin: 0 auto;
            padding: 20mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* 🔥 push footer to bottom */
            background: white;
            border: 2px solid white !important;
        }



        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h6 {
            color: #add33e;
            font-weight: bold;
            margin: 0;
        }

        .hr-green {
            background: #add33e;
            height: 2px;
            border: none;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .title-primary h2,
        .title-primary h3,
        .title-primary h4 {
            margin: 0;
        }

        .title-primary h2 {
            font-size: 44px;
            font-weight: bold;
            color:#add33d;
        }

        .title-primary h3 {
            font-size: 44px;
            font-weight: 900;
            color: #a2cbf0;
        }

        .title-primary h4 {
            font-size: 39px;
            font-weight: 300;
            color: #73b1d4;
        }

        .sub-description {
            font-size: 16px;
            width: 100%;
            max-width: 95%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            text-align: center;
        }

        table td {
            padding: 1px;
            vertical-align: middle;
        }

        .title-header {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            color: #a39f9f;
        }

        .co2-box {
            font-size: 10px;
            display: flex;
            align-items: center;
            height: 47px;
        }

        .footer-co2 p {
            margin: 0;
            font-size: 10px;
        }

        

        .summary-box {
            padding: 13px;
            font-weight: bold;
        }

        .summary-left {
            background: #f2f2f2;
            color: #bdbcbc;
            font-size:23px;
        }

        .summary-mid {
            background: #e3e3e3;
            color:rgb(46, 46, 45);
        }

        .summary-right {
            background: #f3f7fb;
            color: #afd4f2;
            font-size:23px;
        }

        .summary-right-price {
            background: #dae8f4;
            color: #73b2d4;
        }
        .savings-box {
            background: #f0f6e3;
            color: #97c22c;
            padding: 7px;
            font-size: 19px
        }

     
        .price {
            font-weight: bold;
            font-size: 23px;
            margin: auto;
        }
        .active {
            color:#76b3d5 !important;
        }

        .arrow {
            width:60px;
        }
       
        .co2-box p {
            font-size:12px !important;
        }


        .footer-co2 {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 30px;
        }

        .footer-co2 .co2-column {
            flex: 1 1 50%;
            min-width: 300px;
        }

        .footer-co2 {
            display: flex;
            flex-wrap: nowrap !important;
            gap: 30px;
        }

        .footer-co2 .co2-column {
            width: 50%;
            flex: 0 0 50%;
            box-sizing: border-box;
        }

        .footer-co2 .co2-column {
            background: #fdfdfd;
        }


        .co2-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px; 
            padding: 12px;
            border-bottom: 1px solid gray;
        }

        .co2-icon-text {
            display: flex;
            gap: 10px;
        }

        .co2-icon-text img {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
        }

        .co2-icon-text p {
            margin: 0;
            font-size: 15px;
            line-height: 1.5;
        }

        .co2-total {
            min-width: 130px;
            text-align: right;
            font-size: 12px;
        }

        .co2-total strong {
            font-size: 23px;
            color: #616161;
        }

        .co2-p {
            margin-top: 10px;
        }

        .co2-p p {
            font-size: 15px;
            margin: 0 0 5px;
        }

        @media screen and (max-width: 768px) {
            .footer-co2 {
                flex-direction: column;
            }
            .co2-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .co2-total {
                text-align: left;
                margin-top: 10px;
            }
        }


        img {
            width: 85px;
            margin-bottom: 12px;
            padding: 0;
            margin: 0;
        }

        #infoPic {
            width: 112px;
            position: relative;
            left: 88%;
            top: 20px;
        }

        .td-border {
            border-bottom: 2px solid #cdcdcd;
        }

        .final-gap {
            border-bottom: 20px solid #ffffff;
        }


    </style>


<style>
    table td {
        padding: 1px;
        vertical-align: middle;
        text-align: center;
        font-size: 12px;
    }

    .fixed-col-icon {
        width: 60px;
    }

    .fixed-col-before {
        width: 160px;
    }

    .fixed-col-arrow {
        width: 50px;
    }

    .fixed-col-after {
        width: 160px;
    }

</style>




 

<div class="a4-page">
    <div class="header">
        <h6 style="font-size: 21px;">ALT GEGEN NEU: RECHNET SICH DER UMBAU?</h6>
        <img src="{{ asset('logo/logo.png') }}" alt="Logo" style="width: 194px;">
    </div>
    <div class="green-line" style="border-bottom:2px solid #add33d; margin-top:20px; margin-bottom:20px;"> </div>

    <div class="title-primary">
        <h2>DIE ZAHLEN ZEIGEN:</h2>
        <h3>IHRE ENERGIE-ZUKUNFT</h3>
        <h4>RECHNET SICH.</h4>
    </div>

    <h4 class="mt-3" style="font-size: 22px; font-weight: bold; color:#add33d;">DER START IN IHRE ENERGIEFREIHEIT</h4>
    <p class="sub-description">
        Der direkte Kosten-Nutzen-Vergleich zwischen aktuellem Zustand und autarker Lösung mit Photovoltaik, Wärmepumpe und E-Mobilität.
    </p>
    <p class="sub-description">Klar. Rechenbar. Zukunftsfähig.</p>

    <table class="mt-4">
        <colgroup>
            <col style="width: 148px;">
            <col style="width: 220px;">
            <col style="width: 179px;">
            <col style="width: 223px;">
            <col style="width: 150px;">
            <col style="width: 0;">
        </colgroup>
        <!-- Headline Row -->
        <tr>
            <td></td>
            <td  >
                <p class="title-header" >WENN ALLES SO BLEIBT:<br>DIESE KOSTEN ERWARTEN SIE</p>
            </td>
            <td></td>
            <td  >
                <p class="title-header" style="font-weight: 100;">Umstellung auf</p>
                <img src="{{ asset('images/checklist/komplett-paket.svg') }}" alt="Sektorkopplung" style="width: 297px;margin-bottom: -38px;margin-top: -27px;z-index: -1;">
            </td>
            <td></td>
        </tr>

        <!-- Vergleichstitel -->
        <tr class="td-border">
            <td><img src="{{ asset('images/checklist/alles-alt-icon.svg') }}" alt="Alt"></td>
            <td>
                <div style="background: #c4c4c4; color: #6c6c6c; padding: 4px 0; margin-top:20px;">
                    <p class="pt-1 mb-0" style="font-size: 20px; font-weight: bold">VORHER</p>
                    <p class="m-0">pro Jahr</p>
                </div>
            </td>
            <td></td>
            <td>
                <div style="background: #b0d5f2; color: #76b3d5; padding: 4px 0;margin-top:20px;"> 
                    <p class="pt-1 mb-0" style="font-size: 20px; font-weight: bold">NACHHER</p>
                    <p class="m-0">pro Jahr</p>
                </div>
            </td>
            <td><img src="{{ asset('images/checklist/alles-neu-icon.svg') }}" alt="Neu"></td>
            <td></td>
        </tr>

        <!-- EVU -->
        <tr class="td-border">
            <td><img src="{{ asset('images/checklist/evu-icon.svg') }}"></td>
            <td><p class="price" id="evu_price_before">1.200 €</p><p id="evu_houshalt">Haushalt 4.000 kWh <span id="current_price">(0.30 Euro/kWh)</span></p></td>
            <td><img src="{{ asset('images/checklist/green-double-arrows-icon.svg') }}" class="arrow"></td>
            <td><p class="price active" id="evu_after_price">240 €</p><p id="evu_after_houshalt">Haushalt 4.000 kWh</p></td>
            <td><img src="{{ asset('images/checklist/pv-haus-batterie-icon.svg') }}"></td>
            <td></td>
        </tr>

        <!-- Heizung -->
        <tr class="td-border">
            <td><img src="{{ asset('images/checklist/gas-heizung-icon.svg') }}"></td>
            <td><p class="price" id="heizung_price">3.000 €</p><p id="heizung_energy">Heizenergie 25.000 kWh</p></td>
            <td><img src="{{ asset('images/checklist/green-double-arrows-icon.svg') }}" class="arrow"></td>
            <td><p class="price active" id="heizung_after_price">500 €</p><p id="wp_strom" >WP-Strom 6.000 kWh</p></td>
            <td><img src="{{ asset('images/checklist/waermepumpe-icon.svg') }}"></td>
            <td></td>
        </tr>

        <!-- Autos -->
        <tr class="td-border">
            <td><img src="{{ asset('images/checklist/verbrenner-auto-icon.svg') }}" class="mb-2"></td>
            <td><p class="price" id="fuel_price">3.570 €</p><p>Kraftstoff 2x PKW</p></td>
            <td><img src="{{ asset('images/checklist/green-double-arrows-icon.svg') }}" class="arrow"></td>
            <td><p class="price active" id="feul_price_after">500 €</p><p id="number_car">2x E-Autos 5.000 kWh</p></td>
            <td><img src="{{ asset('images/checklist/e-auto-icon.svg') }}"></td>
            <td></td>
        </tr>

        <!-- Pro Jahr -->
        <tr class="final-gap">
            <td class="summary-box summary-left">PRO JAHR</td>
            <td class="summary-box summary-mid price" id="total_per_year">7.770 €</td>
            <td class="savings-box"><strong>ERSPARNIS</strong><br><small>0000 €</small></td>
            <td class="summary-box summary-right-price price" id="total_price_after">1.240 €</td>
            <td class="summary-box summary-right">PRO JAHR</td>
            <td></td>
        </tr>

        <!-- 25 Jahre -->
        <tr>
            <td class="summary-box summary-left" >25 JAHRE</td>
            <td class="summary-box summary-mid price" id="total_price_25">155.400 €</td>
            <td class="savings-box"><strong>ERSPARNIS</strong><br><small>0000 €</small></td>
            <td class="summary-box summary-right-price price" id="total_price_25_after">24.800 €</td>
            <td class="summary-box summary-right">25 JAHRE</td>
            <td></td>
        </tr>
    </table>

    <img src="{{asset('images/checklist/wussten-sie-stoerer.svg') }}" alt="" id="infoPic">

    <div class="footer-co2">
        <div class="co2-column">
            <h5 style="color: gray; font-weight: bold;">IHR JETZIGER CO₂-AUSSTOSS</h5>
            <div class="co2-row">
                <div class="co2-icon-text">
                    <img src="{{ asset('images/checklist/co2-cloud-icon.svg') }}">
                    <div>
                        <p id="haushalt_strom">Haushaltsstrom: 1.464 kg</p>
                        <p id="heiz_energy">Heizenergie: 5.050 kg</p>
                        <p id="autos">Verbrenner-Autos: 4.872 kg</p>
                    </div>
                </div>
                <div class="co2-total">
                    <p>Gesamt CO₂ Ausstoss:<br><strong id="co2_total">11.386 kg</strong></p>
                </div>
            </div>
            <div class="co2-p">
                <p>Um den CO₂-Ausstoß auszugleichen müssten Sie <strong>760 Bäume</strong> pflanzen.</p>
                <p>Das entspricht einer Fläche von <strong>11.400 qm = 1,45 Fußballfeldern</strong>.</p>
            </div>
        </div>

        <div class="co2-column">
            <h5 style="color: #a6cb50; font-weight: bold;">IHR KÜNFTIGER CO₂-AUSSTOSS</h5>
            <div class="co2-row mr-2">
                <div class="co2-icon-text">
                    <img src="{{ asset('images/checklist/co2-green-leaf-icon.svg') }}">
                    <div>
                        <p>Haushaltsstrom: 0 kg</p>
                        <p>Heizenergie: 0 kg</p>
                        <p>Verbrenner-Autos: 0 kg</p>
                    </div>
                </div>
                <div class="co2-total">
                    <p>Gesamt CO₂ Ausstoss:<br><strong class="primary">0 kg</strong></p>
                </div>
            </div>
            <div class="co2-p">
                <p>Warum nicht trotzdem ein paar Bäume pflanzen?</p>
                <p>Die Umwelt bedankt sich doppelt!</p>
            </div>
        </div>
    </div>

    <div class="green-line" style="border-bottom:2px solid #add33d; margin-top:20px; margin-bottom:20px;"> </div>
</div>
<div style="text-align: right; margin: 20px;">
<button onclick="printA4Standalone()" class="btn btn-primary"> <i class="feather icon-printer"></i> Drucken</button>
</div>

 @push('scripts')
  
     <script>
        function printA4Standalone() {
    const a4 = document.querySelector('.a4-page');
    if (!a4) return alert("❌ A4 page not found");

    const baseURL = window.location.origin;
    const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
        .map(el => el.outerHTML)
        .join('\n');

    const clone = a4.cloneNode(true);

    clone.querySelectorAll('img').forEach(img => {
        if (img.src.startsWith('/')) img.src = baseURL + img.src;
    });

    const printWindow = window.open('', '_blank');
    if (!printWindow) return alert("❌ Pop-up blocked");

    const html = `
        <html>
        <head>
            <title>Druckvorschau</title>
            ${styles}
            <style>
                @page {
                    size: A4;
                    margin: 10mm;
                    border:0px;
                }

                html, body {
                    margin: 0;
                    padding: 0;
                    height: 100%;
                    font-family: Arial, sans-serif;
                    background: #fff;
                }

                .a4-page {
                    width: 210mm;
                    height: 297mm;
                    margin: 50mm;
                    padding:40mm;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    background: #fff;
                }

                img {
                    max-width: 100%;
                }
            </style>
        </head>
        <body>
            ${clone.outerHTML}
        </body>
        </html>
    `;

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();

    setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 800);
}

     </script>
 @endpush