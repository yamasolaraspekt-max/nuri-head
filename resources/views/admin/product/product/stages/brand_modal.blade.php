{{-- resources/views/admin/product/product/stages/brand_modal.blade.php --}}
<div class="modal fade text-left" id="new_brand" tabindex="-1" role="dialog"
     aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title" id="brandModalLabel">
                    Neue Marke hinzufügen
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form id="brandForm"
                  class="form-horizontal"
                  novalidate
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <fieldset>
                        <div class="row">

                            {{-- Hersteller + Initial --}}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="brand_name">Hersteller</label>
                                    <input type="text"
                                           class="form-control"
                                           id="brand_name"
                                           name="name"
                                           required>
                                    <p class="text-danger mb-0" id="name-error"></p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="brand_initial">Initial</label>
                                    <input type="text"
                                           class="form-control"
                                           id="brand_initial"
                                           name="initial">
                                    <p class="text-danger mb-0" id="initial-error"></p>
                                </div>
                            </div>

                            {{-- Zweckkategorie --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="brand_purpose">Zweckkategorie</label>
                                    <select name="purpose"
                                            id="brand_purpose"
                                            class="form-control">
                                        <option value="PHOTOVOLTAIK">PHOTOVOLTAIK</option>
                                        <option value="BATTERIESPEICHER">BATTERIESPEICHER</option>
                                        <option value="WÄRMEPUMPE">WÄRMEPUMPE</option>
                                        <option value="WALLBOX">WALLBOX</option>
                                        <option value="ELEKTRO">ELEKTRO</option>
                                        <option value="SANITÄR">SANITÄR</option>
                                        <option value="BAD">BAD</option>
                                        <option value="BAUELEMENTE">BAUELEMENTE</option>
                                        <option value="KÜCHE">KÜCHE</option>
                                        <option value="SOLAR CARPORT">SOLAR CARPORT</option>
                                        <option value="SOFTWARE">SOFTWARE</option>
                                        <option value="HARDWARE">HARDWARE</option>
                                    </select>
                                    <p class="text-danger mb-0" id="purpose-error"></p>
                                </div>
                            </div>

                            {{-- Abteilungen & Ansprechpartner --}}
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-50">
                                    <h6 class="mb-0">Abteilungen & Ansprechpartner</h6>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            id="add_brand">
                                        <i class="feather icon-plus"></i> Zeile hinzufügen
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm" id="add_department">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>Abteilung</th>
                                            <th>Ansprechpartner</th>
                                            <th>Position</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Festnetznummer</th>
                                            <th>Büro</th>
                                            <th class="text-right">Aktion</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="Abteilung"
                                                       name="brand[0][brand_department]">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="Gesprächspartner"
                                                       name="brand[0][name]">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="Position"
                                                       name="brand[0][position]">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="E-Mail"
                                                       name="brand[0][email]">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="Handynummer"
                                                       name="brand[0][phone]">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="Festnetznummer"
                                                       name="brand[0][home]">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       class="form-control required"
                                                       placeholder="Büro-Telefonnummer"
                                                       name="brand[0][office]">
                                            </td>
                                            <td class="text-right">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger brand-remove-row">
                                                    <i class="feather icon-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Logo --}}
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label for="brand_image">
                                        Logo <code>.PNG</code>
                                    </label>
                                    <input type="file"
                                           class="form-control"
                                           id="brand_image"
                                           name="image">
                                    <p class="text-danger mb-0" id="image-error"></p>
                                </div>
                            </div>

                        </div>
                    </fieldset>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-primary"
                            id="saveBrandBtn">
                        <i class="feather icon-save"></i> Speichern
                    </button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Schließen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
