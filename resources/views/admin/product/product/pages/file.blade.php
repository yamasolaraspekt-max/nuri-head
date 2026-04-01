<div class="card-content">
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">

            <li class="nav-item">
                <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true"><i class="feather icon-file"></i> Beschreibung</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="home-tab-fill" data-toggle="tab" href="#home-fill" role="tab" aria-controls="home-fill" aria-selected="true"><i class="fa fa-file-pdf-o"></i> Dokument</a>
            </li>
        
        </ul>

        <!-- Tab panes -->
        <div class="tab-content pt-1">

            <div class="tab-pane active" id="description" role="tabpanel" aria-labelledby="description">
               <p> @if($data) {!! $data->short_description !!} @endif</p>
            </div>

            <div class="tab-pane" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill">
                <ul class="list-group">
                    @foreach ($documents as $proDoc)
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="fa fa-file-pdf-o"> </i>
                        </p>
                        <a href="{{ url('images/products/document/'.$proDoc->document)}}" target="_blank"><span  style="margin-left: 10px !important">{{ $proDoc->title }}</span></a>
                    </li>
                    @endforeach
                    
                    
                </ul>
            </div> 
        </div>
    </div>
</div>