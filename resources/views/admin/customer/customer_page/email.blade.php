<div class="table-responsive">
    <table class="table table-bordered mb-0">
        <thead>
            <tr>
                <th>Namen</th>
                <th>Email</th>
                <th>Datum</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($emails as $email)
            <tr>
            <td><a href="">{{ $email->sender_name}}</a> <br>
                <div class="chip chip-primary mr-1">
                    <div class="chip-body">
                        <span class="chip-text">{{ $email->recipient_name }}</span>
                    </div>
                </div>
            </td>
              
               
                <td>{{ $email->subject }}
                            
                            </br>
                            <div class="chip chip-primary mr-1">
                                <div class="chip-body">
                                    <span class="chip-text">
                                        {{ $email->sender_email }}
                                    </span>
                                </div>
                            </div>
                            @php
                                $duplicatedValue = DB::table('customers')->where('email', '=', $email->sender_email)->first();
                            @endphp
                        
                            @if($duplicatedValue)
                            <div class="chip chip-success mr-1">
                                <div class="chip-body">
                                    <span class="chip-text">
                                        Gespeichert als <strong>{{ $duplicatedValue->name }} {{ $duplicatedValue->lastname }}</strong>
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="chip chip-danger mr-1">
                                <div class="chip-body">
                                    <span class="chip-text">
                                        Nicht gespeichert
                                    </span>
                                </div>
                            </div>
                           
                            @endif
                       
                </td>
                <td>{{ \Carbon\Carbon::parse($email->created_at)->diffForHumans() }}</td>
                <td>
                    <!-- Solution Modal -->
                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#solution{{$email->id}}">
                        <i class="feather icon-file-text"></i>
                        </button>

                        <!-- Modal -->
                        <div class="modal fade text-left" id="solution{{$email->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>{{$email->sender_name}} </h5>
                                        <p><code>{{ $email->sender_email}}</code> </p>
                                        <h4>E-Mail</h4>
                                        <p>{!! strip_tags($email->body) !!}</p>
              
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Solution Modal -->
                </td>
                
               
            </tr>
            @endforeach
            
           
           
        </tbody>
    </table>
</div>