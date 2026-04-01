    <article>
        <div class="col-md-12" style="display:flex;">
            <div class="col-md-1 ">
                <span>Intention</span>
            </div>
            <div class="col-md-7">
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="intention" id="intention_interest"
                                    value="Interesse">
                                <label class="custom-control-label" for="intention_interest">Interesse</label>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="intention" id="intention_available"
                                    value="vorhanden">
                                <label class="custom-control-label" for="intention_available">vorhanden</label>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="intention" id="intention_extension"
                                    value="Erweiterung">
                                <label class="custom-control-label" for="intention_extension">Erweiterung</label>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="intention" id="intention_spater"
                                    value="später">
                                <label class="custom-control-label" for="intention_spater">später</label>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input danger" name="intention" id="intention_absage" value="Absage">
                                <label class="custom-control-label" for="intention_absage">Absage</label>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>

            <div class="col-md-4 flex_me">
                <div class="col-md-4">
                    <label for="">Vollständigkeit für AN &nbsp;</label>
                </div>
                <div class="col-md-8">
                    <div class="progress progress-bar-primary progress-xl">
                        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="20"
                            aria-valuemax="100" style="width:20%;">
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </article>
    <hr>
    <div class="col-md-12" style="display: flex !important;  flex-wrap: wrap; align-items: center;">
        <section class="col-md-8 right-border">
            <div class="cards">
                <div class="card-title">
                    <h4 class=" " style="color: #73b1d4;font-size: 24px !important;  font-weight: bold; ">KURZ-CHECKLISTE <a href="{{ url('customer_product_details_edit/'.request()->customer_id.'/'.request()->product_id.'/'.request()->address_no) }}"><i class="feather icon-edit"></i></a></h4>
                </div>
            </div>
        </section>
    </div>
    <article class="d-flex">
        <div class="col-md-8" style="display: flex !important; flex-wrap: wrap; align-items: center;">
            <section class="col-md-12 right-border d-flex">
                <!-- First Column -->
                <div class="col-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4><i class="feather icon-home primary" style="font-size:20px"></i> Objektart</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="object_art" value="{{ $pv_checklist->property_type }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4>Anzahl WE</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="anzahl_we" value="{{ $pv_checklist->number_of_units }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4 class="highlight">Stromverbrauch</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="stromverbrauch" value="{{ $pv_checklist->electricity_consumption }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4>Anzahl Zähler</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="anzahl_zaehler" value="{{ $pv_checklist->number_of_meters }}">
                        </div>
                    </div>
                </div>
                <!-- Second Column -->
                <div class="col-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4>E-Auto</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="e_auto" value="{{ $pv_checklist->electric_car }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4>Wallbox gewünscht</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="wallbox_gewuenscht" value="{{ $pv_checklist->wallbox_desired }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4>Wärmepumpe</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="input-control" name="waermepumpe" value="Interessiert">
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <div class="col-6">
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class=""></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2" class="active"></li>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                        <div class="carousel-item">
                            <img class="img-fluid" src="../../../app-assets/images/slider/02.jpg" alt="First slide">
                        </div>
                        <div class="carousel-item active carousel-item-left">
                            <img class="img-fluid" src="../../../app-assets/images/slider/03.jpg" alt="Second slide">
                        </div>
                        <div class="carousel-item carousel-item-next carousel-item-left">
                            <img class="img-fluid" src="../../../app-assets/images/slider/01.jpg" alt="Third slide">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </article>

<hr class="normal">

@foreach ($pv_roof as $roof)
<article>
    <div class="col-md-8 right-border d-flex">
        <section class="col-12 right-border d-flex">
            <!-- First Column -->
            <div class="col-6">
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dach {{ $loop->iteration }}</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="dach_{{ $loop->iteration }}" value="{{ $roof->designation }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dacheindeckung</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="dacheindeckung" value="{{ $roof->roof_covering }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4 class="highlight">Aufdachdämmung</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="aufdachdaemmung" value="{{ $roof->roof_insulation }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Zwischensparrendämmung</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="zwischensparrendaemmung" value="{{ $roof->between_rafter_insulation }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Solarhalteziegel gewünscht</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="solarhalteziegel" value="ja">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>geliefert durch</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="geliefert_durch" value="Dachdecker">
                    </div>
                </div>
            </div>
            <!-- Second Column -->
            <div class="col-6">
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Maße Dachfläche</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="masse_dachflaeche" value="15x10">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dachüberstand Sparren links</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="dachueberstand_links" value="45">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dachüberstand Sparren rechts</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="dachueberstand_rechts" value="50">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Sparrenstärke</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="sparrenstaerke" value="65">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Sparrenverstärkung notwendig</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="sparrenverstaerkung" value="nein">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Eindeckmaß in cm</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="eindeckmass" value="30x34">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Statik vorhanden</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="statik_vorhanden" value="nein">
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dachsanierung notwendig</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="dachsanierung" value="{{ $roof->roof_renovation }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dachdecker</h4>
                    </div>
                    <div class="col-md8">
                        <input type="text" class="input-control" name="dachdecker" value="Feustel">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Ort</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="ort" value="Usingen">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Ansprechpartner</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="ansprechpartner" value="Herr Mustermann">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>geplanter Termin</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="geplanter_termin" value="Mai 2024">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Dauer</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="dauer" value="3 Wochen">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Gerüstnutzung</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="geruestnutzung" value="ja">
                    </div>
                </div>
            </div>
        </section>
    </div>
</article>
<hr class="normal">
@endforeach

<article>
    <div class="col-md-8 right-border d-flex">
        <section class="col-12 right-border d-flex">
            <!-- First Column -->
            <div class="col-6">
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Strom</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="strom" value="">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>gewünschte Größe</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="groesse" value="ca. 10 kWp">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Einspeisezusage EVU Netzverträglichkeit</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="evu_netzvertraeglichkeit" value="nein">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Notiz</h4>
                    </div>
                    <div class="col-md-8">
                        <textarea class="input-control" name="notiz">Kunde möchte Exceariore nis sequati tem veles sita est fuga. Non rero coritat fuga.</textarea>
                    </div>
                </div>
            </div>
            <!-- Second Column -->
            <div class="col-6">
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Leerrohr vorhanden</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="leerrohr" value="nein">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Kabelführung durch</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="kabelfuehrung" value="Fassada">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <h4>Blitzschutz auf dem Dach vorhanden</h4>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="input-control" name="blitzschutz" value="nein">
                    </div>
                </div>
            </div>
        </section>
    </div>
</article>

<!-- accordion Details -->
    <div class="col-md-12">
        <i class="fa fa-chevron-right" id="accordion"></i>
    </div>
    <hr>
<!-- resources/views/checklist.blade.php -->
    <article id="longList" style="display: none">
        <section class="col-md-12 dynamic-section flex_me" id="section_3">
            <div class="col-md-12">
                <div class="cards">
                    <div class="card-title"><h3 class="titles">LANG-CHECKLISTE</h3></div>
                    <div class="card-body" style="display: flex !important;flex-wrap: wrap;margin-bottom: -52px !important;">
                        <div class="col-12">
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <h4 class="bold">Strom</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <div class="col-md-10">
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mr-1" style="width:454px !important">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <h4 class="bold">gewünschte Größe</h4>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="insulation_strength[0]" required>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <h3 class="bold">Einspeisezusage EVU Netzverträglichkeit</h3>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="pv_rafter[0]" id="rafter_ja_0" value="ja">
                                                                        <label class="custom-control-label" for="rafter_ja_0">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="pv_rafter[0]" id="rafter_nein_0" value="nein">
                                                                        <label class="custom-control-label" for="rafter_nein_0">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1" style="width:542px !important;">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <h4 class="bold">EVU max. Größe</h4>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control" name="rafter_strength[0]" required>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <div class="col-md-1">
                                            <h4 class="bold">Notiz</h4>
                                        </div>
                                        <div class="col-md-11">
                                            <textarea name="" id="" class="form-control" cols="30" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="normal">
                    <div class="card-body" style="display: flex !important;flex-wrap: wrap;margin-bottom: -52px !important;">
                        <form>
                            <div class="form-section">
                                <div class="form-header">Dach 1</div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="roofDimensions">Maße Dachfläche</label>
                                        <textarea type="text" class="form-control" cols="30" rows="5" id="roofDimensions"></textarea>
                                    </div>
                                    <div class="form-group col-md-6" style="display: flex !important; flex-wrap: wrap; align-items: center; margin-bottom: 1px;">
                                        <div class="form-gorup col-md-12 d-flex">
                                            <div class="col-md-2">
                                                <label for="sparrenLinks">Dachüberstand Sparren links</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" id="sparrenLinks">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="sparrenLinks">Eindeckmaß in cm</label>
                                            </div>
                                            <div class="col-md-3 flex_me">
                                                <input type="text" class="form-control" id="sparrenLinks"> &nbsp; <label for="b">B</label>
                                            </div>
                                            <div class="col-md-3 flex_me">
                                                <input type="text" class="form-control" id="sparrenLinks">&nbsp; <label for="b">H</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 d-flex" style="  margin-bottom: 1px;">
                                            <div class="col-md-2">
                                                <label for="sparrenRechts">Dachüberstand Sparren rechts</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" id="sparrenRechts">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 d-flex">
                                            <div class="col-md-2">
                                                <label for="sparrenStaerke">Sparrenstärke</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" id="sparrenStaerke">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 d-flex">
                                            <div class="col-md-2">
                                                <label for="sparrenVerstaerkung">Sparrenverstärkung notwendig</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="sparrenVerstaerkung" id="sparrenVerstaerkungJa" value="ja">
                                                    <label class="form-check-label" for="sparrenVerstaerkungJa">ja</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="sparrenVerstaerkung" id="sparrenVerstaerkungNein" value="nein">
                                                    <label class="form-check-label" for="sparrenVerstaerkungNein">nein</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="statik">Statik vorhanden</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="statik" id="statikJa" value="ja">
                                                    <label class="form-check-label" for="statikJa">ja</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="statik" id="statikNein" value="nein">
                                                    <label class="form-check-label" for="statikNein">nein</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-section">
                                <div class="form-row col-md-6">
                                    <div class="col-md-2">
                                        <label for="statik">Leerohr vorhanden</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="leerohr" id="leerohr" value="ja">
                                            <label class="form-check-label" for="leerohrJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="leerohr" id="leerohrNein" value="nein">
                                            <label class="form-check-label" for="leerohrNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="kabelfuhrung_durch">Kabelführung durch</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="kabelfuhrung_durch" class="form-control">
                                            <option>Kamin</option>
                                            <option>Leerrohr</option>
                                            <option>Fallrohr</option>
                                            <option>sonstiges</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row col-md-6">
                                    <div class="col-md-2">
                                        <label for="kabelfuhrung_durch">Blitzschutz auf dem Dach vorhanden</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="kabelfuhrung_durch" class="form-control">
                                            <option>ja</option>
                                            <option>nein</option>
                                            <option>geplant</option>
                                            <option>entfernt</option>
                                            <option>versetzt</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-section">
                                <div class="form-row col-md-12">
                                    <div class="col-md-1">
                                        <label for="statik">Dachsanierung notwendig</label>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="dachsanierung_notwendig" id="dachsanierung_notwendigJa" value="ja">
                                            <label class="form-check-label" for="dachsanierung_notwendigJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="dachsanierung_notwendigJa" id="dachsanierung_notwendigJaNein" value="nein">
                                            <label class="form-check-label" for="dachsanierung_notwendigJaNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="dachdecker">geplanter Termin</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="dachdecker" name="dachdecker">
                                    </div>
                                    <div class="col-md-1">
                                        <label for="gerustnutzung">Gerüstnutzung</label>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gerustnutzung" id="gerustnutzungJa" value="ja">
                                            <label class="form-check-label" for="gerustnutzungJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gerustnutzung" id="gerustnutzungtNein" value="nein">
                                            <label class="form-check-label" for="gerustnutzungNein">nein</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row col-md-6">
                                    <div class="col-md-2">
                                        <label for="dachdecker">Dachdecker</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="dachdecker" name="dachdecker">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="dauer">Dauer</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="dauer" name="dauer">
                                    </div>
                                </div>
                                <div class="form-row col-md-6">
                                    <div class="col-md-2">
                                        <label for="dachdecker">Ort</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="ort" name="ort">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="solarhalteziegel_gewünscht">Solarhalteziegel gewünscht</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="solarhalteziegel_gewünscht" id="solarhalteziegel_gewünschtJa" value="ja">
                                            <label class="form-check-label" for="solarhalteziegel_gewünschtJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="solarhalteziegel_gewünscht" id="solarhalteziegel_gewünschtNein" value="nein">
                                            <label class="form-check-label" for="solarhalteziegel_gewünschtNein">nein</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row col-md-6">
                                    <div class="col-md-2">
                                        <label for="dachdecker">Ansprechpartner</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="ort" name="ort">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="geliefert_durch">geliefert durch</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="geliefert_durch" class="form-control">
                                            <option>Dachdecker</option>
                                            <option>Kunde</option>
                                            <option>uns</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <br>
                            <div class="form-section">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-1">
                                        <label for="category">Dachaufbauten</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="kabelfuhrung_durch" class="form-control">
                                            <option>Dachluke</option>
                                            <option>Antenne</option>
                                            <option>Stromleitung</option>
                                            <option>Gaube</option>
                                            <option>SAT-Schüssel</option>
                                            <option>Kamin</option>
                                            <option>Lüfter groß</option>
                                            <option>Dachfenster</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="category">geplante Aktion</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="kabelfuhrung_durch" class="form-control">
                                            <option>erneuert</option>
                                            <option>entfernt</option>
                                            <option>versetzt</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="category">Notiz</label>
                                    </div>
                                    <div class="col-md-3">
                                        <textarea class="form-control" col="12" row="1" name="geplante_note"></textarea>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-icon rounded-circle" id="addRow"><i class="feather icon-plus-circle primary" style="font-size: 34px;"></i></button>
                                    </div>
                                </div>
                                <div id="rowsContainer"></div>
                            </div>
                        </form>
                    </div>
                    <br>
                    <div class="col-12">
                        <form>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group flex_me">
                                        <div class="col-md-2">
                                            <label for="baujahr">Baujahr</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="baujahr">
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <div class="col-md-2">
                                            <label for="anzahlModule">Anzahl Module</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="anzahlModule">
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <div class="col-md-2">
                                            <label for="modulhersteller">Modulhersteller</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="modulhersteller">
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <div class="col-md-2">
                                            <label for="typBezeichnung">Typ Bezeichnung</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="typBezeichnung">
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <div class="col-md-2">
                                            <label for="kwpGroße">kWp Größe</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="kwpGroße">
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <div class="col-md-2">
                                            <label for="wechselrichter">Wechselrichter</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="wechselrichter">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group flex_me">
                                        <label>Anlage umbauen</label><br>&nbsp;
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="anlageUmbauen" id="anlageUmbauenJa" value="ja">
                                            <label class="form-check-label" for="anlageUmbauenJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="anlageUmbauen" id="anlageUmbauenNein" value="nein">
                                            <label class="form-check-label" for="anlageUmbauenNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <label>Schaden/Defekt</label><br>&nbsp;
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="schadenDefekt" id="schadenDefektJa" value="ja">
                                            <label class="form-check-label" for="schadenDefektJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="schadenDefekt" id="schadenDefektNein" value="nein">
                                            <label class="form-check-label" for="schadenDefektNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <label>komplette Demontage</label><br>&nbsp;
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kompletteDemontage" id="kompletteDemontageJa" value="ja">
                                            <label class="form-check-label" for="kompletteDemontageJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kompletteDemontage" id="kompletteDemontageNein" value="nein">
                                            <label class="form-check-label" for="kompletteDemontageNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <label>Versicherungsschaden</label><br>&nbsp;
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="versicherungsschaden" id="versicherungsschadenJa" value="ja">
                                            <label class="form-check-label" for="versicherungsschadenJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="versicherungsschaden" id="versicherungsschadenNein" value="nein">
                                            <label class="form-check-label" for="versicherungsschadenNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <label>Kunde behält Module</label><br>&nbsp;
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kundeBehältModule" id="kundeBehältModuleJa" value="ja">
                                            <label class="form-check-label" for="kundeBehältModuleJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kundeBehältModule" id="kundeBehältModuleNein" value="nein">
                                            <label class="form-check-label" for="kundeBehältModuleNein">nein</label>
                                        </div>
                                    </div>
                                    <div class="form-group flex_me">
                                        <label>Kunde behält WR</label><br>&nbsp;
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kundeBehältWR" id="kundeBehältWRJa" value="ja">
                                            <label class="form-check-label" for="kundeBehältWRJa">ja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kundeBehältWR" id="kundeBehältWRNein" value="nein">
                                            <label class="form-check-label" for="kundeBehältWRNein">nein</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="notiz">Notiz</label>
                                        <textarea class="form-control" id="notiz" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </article>

    <!-- accordion Details: End -->
