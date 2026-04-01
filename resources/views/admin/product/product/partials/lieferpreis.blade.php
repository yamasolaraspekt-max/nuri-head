<div class="card">
        <div class="card-header">
            <h6 class="card-title"><i id="icon" class="fa fa-folder" ></i>  Lieferpreis</h6>
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
                            <tr>
                                <th>#</th>
                                <th>Lieferant</th>
                                <th>UVP</th>
                                <th>Rabbat-preis</th>
                                <th>Rabbat-perzent</th>
                                <th>Einkaufspreis</th>
                                <th>Datum</th>
                                <th>Verfügbarkeit</th>

                                <th></th>
                    
                            @foreach ($distributor_price as $distributor)
                                    <tr>
                                        <th>{{ $distributor->article_no }}</th>
                                        <td>
                                        <a href="{{ url('distributor_price/'.$distributor->distributor_id.'/'.request()->id) }}">{{ $distributor->distributor_name }}</a> 
                                        </td>
                                        <td  class="white">{{ number_format( $distributor->price, 2, ',', '.') }}€</td>
                                        <td>{{ number_format( $distributor->discount_price, 2, ',', '.') }}€</td>
                                        <td>{{ $distributor->discount_percent }}%</td>
                                        <td  class="white">{{ number_format( $distributor->purchase_price, 2, ',', '.') }}€</td>
                                        <td>{{ $distributor->price_date}}</td>
                                        <td>{{ $distributor->availability}}</td>

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
                            <a href="{{ url('/distributor_price_create/'.$data->id) }}"><i class="feather icon-edit"></i></a> 
                            </span>
                        
                        </div>
                    </div>
                </p>
            
            </div>
        </div>
    </div>