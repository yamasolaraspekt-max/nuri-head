@foreach($data as $item)  
<tr>
    <th scope="row">{{ $item->id }}</th>
    <td><a href="{{ url('customer_product_create/'.$item->id.'/'.$item->postcode.'/'.$item->address_no)}}">
         {{ $item->name }} </a></td>
    <td>{{ $item->lastname }}</td>
    <td>{{ $item->city }}</td> 
    <td>{{ $item->postcode }}</td>
    <td>
        {{ \Carbon\Carbon::parse($item->request_date)->isoFormat('DD.MM.YY') }} <br>
  <code> <strong> 
     {{ \Carbon\Carbon::parse($item->request_date)->diffForHumans() }}                                   
  </strong></code> 
    </td>
    <td>
        <!-- Button to open modal -->
        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#products{{$item->id}}">
            <i class="feather icon-menu"></i>
        </button>
        <!-- Modal -->
        <div class="modal fade" id="products{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel120">Gewerke</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-10"> 
                            <h1>{{ $item->title }} {{$item->name}} {{ $item->lastname}}</h1> 
                            <p>{{ $item->street}}<br>{{ $item->postcode }}
                                @if($item->main == 0)
                                <small><code>Die Adresse des Kunden stimmt nicht mit seiner Hauptwohnadresse überein</code></small>
                                @endif
                            </p>
                        </div>
                        <hr>
                        <h1 class="mb-2">Gewerke</h1>
                        <div class="col-md-12">
                            @foreach ($product_list->where('customer_id', $item->id) as $product)
                                @if ($product->status == "active")
                                    <a type="button" class="btn btn-icon btn-icon rounded-circle mr-1 mb-1" style="height: 40px; width: 40px; background:#92b532 !important;">
                                        <span style="font-size: 10px; font-weight: bold; color:white; margin:0; font-family: sans-serif !important;">{{ $product->initial }}</span>
                                    </a>
                                @elseif ($product->status == "inactive")
                                    <a type="button" class="btn btn-icon btn-icon rounded-circle mr-1 mb-1" style="height: 40px; width: 40px; background:#78a7cc !important;">
                                        <span style="font-size: 10px; font-weight: bold; color:white; margin:0; font-family: sans-serif !important;">{{ $product->initial }}</span>
                                    </a>
                                @else
                                    <a type="button" class="btn btn-icon btn-icon rounded-circle mr-1 mb-1" style="height: 40px; width: 40px; background:#a0a0a0 !important;">
                                        <span style="font-size: 10px; font-weight: bold; color:white; margin:0; font-family: sans-serif !important;">{{ $product->initial }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Modal footer (optional) -->
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td>
        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->id)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
        <!-- Delete Button -->
        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
            <i class="feather icon-trash"></i>
        </button>
        @endif
        <!-- Delete Modal -->
        <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger white">
                        <h5 class="modal-title" id="myModalLabel120">Danger Modal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Aufzeichnung löschen</h5>
                        <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                        <p>Die Datensatznummer lautet:{{$item->id}} </p>
                    </div>
                    <div class="modal-footer">
                        <a type="button" href="{{url('/customer_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Button -->
        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->id)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
        <a type="button" href="{{ url('/customer_edit/'.$item->id)}}" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
            <i class="feather icon-edit"></i>
        </a>
        @endif
    </td>
</tr>
@endforeach
