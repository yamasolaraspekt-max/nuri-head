@section('style')
  <!-- BEGIN: Vendor CSS-->
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/calendars/fullcalendar.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/calendars/extensions/daygrid.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/calendars/extensions/timegrid.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
  <!-- END: Vendor CSS-->

      <!-- BEGIN: Page CSS-->
      <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/horizontal-menu.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/calendars/fullcalendar.css') }}">
      <!-- END: Page CSS-->
@endsection


                <section id="basic-examples">
                    <div class="row">
                        <div class="col-12 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="cal-category-bullets d-none">
                                            <div class="bullets-group-1 mt-2">
                                                <div class="category-business mr-1">
                                                    <span class="bullet bullet-success bullet-sm mr-25"></span>
                                                   Termin
                                                </div>
                                                <div class="category-work mr-1">
                                                    <span class="bullet bullet-warning bullet-sm mr-25"></span>
                                                    Task
                                                </div>
                                                <div class="category-personal mr-1">
                                                    <span class="bullet bullet-danger bullet-sm mr-25"></span>
                                                    Project
                                                </div>
                                                <div class="category-others">
                                                    <span class="bullet bullet-primary bullet-sm mr-25"></span>
                                                    Others
                                                </div>
                                            </div>
                                        </div>
                                        <div id='fc-default'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- calendar Modal starts-->
                    <div class="modal fade text-left modal-calendar" tabindex="-1" role="dialog" aria-labelledby="cal-modal" aria-modal="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-text-bold-600" id="cal-modal">Add Event</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <form action="#">
                                    <div class="modal-body">
                                        <div class="d-flex justify-content-between align-items-center add-category">
                                            <div class="chip-wrapper"></div>
                                            <div class="label-icon pt-1 pb-2 dropdown calendar-dropdown">
                                                <i class="feather icon-tag dropdown-toggle" id="cal-event-category" data-toggle="dropdown"></i>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="cal-event-category">
                                                    <span class="dropdown-item business" data-color="success">
                                                        <span class="bullet bullet-success bullet-sm mr-25"></span>
                                                      Termin
                                                    </span>
                                                    <span class="dropdown-item work" data-color="warning">
                                                        <span class="bullet bullet-warning bullet-sm mr-25"></span>
                                                      Task
                                                    </span>
                                                    <span class="dropdown-item personal" data-color="danger">
                                                        <span class="bullet bullet-danger bullet-sm mr-25"></span>
                                                        Project
                                                    </span>
                                                    <span class="dropdown-item others" data-color="primary">
                                                        <span class="bullet bullet-primary bullet-sm mr-25"></span>
                                                        Others
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <fieldset class="form-label-group">
                                            <input type="text" class="form-control" id="cal-event-title" placeholder="Event Title">
                                            <label for="cal-event-title">Event Title</label>
                                        </fieldset>
                                        <fieldset class="form-label-group">
                                            <input type="text" class="form-control pickadate" id="cal-start-date" placeholder="Start Date">
                                            <label for="cal-start-date">Start Date</label>
                                        </fieldset>
                                        <fieldset class="form-label-group">
                                            <input type="text" class="form-control pickadate" id="cal-end-date" placeholder="End Date">
                                            <label for="cal-end-date">End Date</label>
                                        </fieldset>
                                        <fieldset class="form-label-group">
                                            <textarea class="form-control" id="cal-description" rows="5" placeholder="Description"></textarea>
                                            <label for="cal-description">Description</label>
                                        </fieldset>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary cal-add-event waves-effect waves-light" disabled>
                                            Add Event</button>
                                        <button type="button" class="btn btn-primary d-none cal-submit-event waves-effect waves-light" disabled>submit</button>
                                        <button type="button" class="btn btn-flat-danger cancel-event waves-effect waves-light" data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-flat-danger remove-event d-none waves-effect waves-light" data-dismiss="modal">Remove</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- calendar Modal ends-->
                </section>
          



@section('script')
    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/ui/jquery.sticky.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/moment.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/calendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/calendar/extensions/daygrid.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/calendar/extensions/timegrid.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/calendar/extensions/interactions.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('app-assets/js/scripts/extensions/fullcalendar.js') }}"></script>
    <!-- END: Page JS-->
@endsection