<div class="card">
    
    <div class="card-header">
        <h4 class="card-title"> {{ $data->product }} - {{ $data->model }}  </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            <style>
                .white{
                    background: white !important;
                    color: black !important;
                }
            </style>
            <div class="table-responsive">
                <table class=" table table-dark mb-0" >
                
                    <tbody>
                        <tr>
                            <th>Article#</th>
                            <th>EAN#</th>
                        </tr>
                    <tr>
                        <td class="white">{{ $data->article_no }}</td>
                        <td  class="white">{{ $data->ean }}</td>
                    </tr>
                
                    </tbody>
                </table>

            
            </div>
            <hr>
            <p>
                Produktbeschreibung
            </p>
            <div class="table-responsive">
                <table class="table">
                
                    <tbody>
                        <tr>
                            <th>Herstellername</th>
                            <td>{{ $data->brandname }}</td>
                        </tr>
                        <tr>
                            <th>Lieferant</th>
                            <td>
                                @foreach ($distributors as $dist)
                                @if($dist->product_id==request()->id)
                                <div class="badge badge-primary mr-1 mb-1">
                                    <i class="fa fa-truck "></i>
                                    <span>{{ $dist->distributor_name }}</span>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>

                        <tr>
                            <th>Farbe</th>
                            <td>{{ $data->color }}</td>
                        </tr>
                        <tr>
                            <th>Artikel-Gruppe</th>
                            <td>{{ $data->article_group }}</td>
                        </tr>
                        
                        <tr>
                            <th>Mengeneinheit</th>
                            <td>{{ $data->measure }}</td>
                        </tr>
                
                        <tr>
                            <th>Preiseinheit</th>
                            <td>{{ $data->price_unit }}</td>
                        </tr> 
                        <tr>
                            <th>Packungseinheit</th>
                            <td>{{ $data->package_unit }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div> 


<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i id="icon" class="fa fa-folder" ></i>  Technisches Beschreibung</h6>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            
            <div class="table-responsive">
            
                <table class="table">
                    
                    <tbody>
                        @foreach ($descriptions as $descript )
                        <tr>
                        <th>{{ $descript->field }}</th>
                        <td>
                            {{ $descript->description }}
                            <small>{{ $descript->remark }}</small>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <p>
                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
                    
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        
                    <i class="feather icon-edit"></i></button>
                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -7px, 0px);">
        
                        <span class="dropdown-item">
                            <a href="{{ url('/product_create_description/'.$data->id) }}"><i class="feather icon-edit"></i> Bearbeiten</a> 
                        </span>
                    
                        
                    </div>
                </div>
            </p>

        </div>
    </div>
</div>