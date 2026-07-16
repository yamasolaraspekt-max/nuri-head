
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/invoice.css') }}">
                                                       <!--  Card Details -->
            @foreach ($data as $item)
                <div class="row" style="    display: flex" >
                    
                            <div class="col-4" style="text-align: -webkit-center; margin-top:-16px; margin-left: 10px;">
                                <p style="margin-bottom: 0px;" > {!! DNS2D::getBarcodeHTML("$item->qrcode",'QRCODE',3,3, 'black') !!}</p>
                            
                            </div>
                        <div class="col-7" style="">
                            <h1 style="text-align: center; margin:0;font-family: system-ui;">{{ $item->branch }}</h1>
                            <div class="invoice-details"style="text-align: -webkit-center; ">
                                <p style="color:#1f2937;font-family: sans-serif; font-size:10px;line-height: 1; margin:0" > Vermögenswerte </p>
                            </div>
                            <div class="invoice-details" style="top: 14px;margin-left: 14px;position: relative;" >
                                {!! DNS1D::getBarcodeHTML("$item->qrcode", 'C128') !!}
                                <p style="margin: 0px;color:#1f2937;font-family: sans-serif; font-size:10px;line-height: 1; margin-left: 13px; text-align: center;" > {{$item->qrcode }} </p>

                            </div>
                        </div>
                    </div>
                    
                </div>      
                @endforeach                                     
      
    <!-- END: Content-->
<script src="{{ asset('app-assets/js/scripts/pages/invoice.js') }}"></script>

<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
<script>
    $(document).ready(function(){
        function printAndClose() {
            window.print();
            setTimeout(function() {
            window.close();
            }, 100);
        }
        
        printAndClose();
    });
</script>
                       