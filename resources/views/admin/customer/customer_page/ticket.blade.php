<div class="row" id="table-bordered">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Aufzeichnungen zur Kundenhistorie</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                  
                                    <th>Ticket#</th>
                                    <th>Error Code</th>
                                    <th>Produkt</th>
                                    <th>Erster Kontakt</th>
                                    <th>Report</th>
                                    <th>Link</th>
                                    <th>Status</th>
    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($problems as $prob)
                                <tr>
                                <td><a href="{{ url('problem_view?search='.$prob->ticket_no) }}">{{ $prob->ticket_no }}</a></td>
                                  
                                    <td>
                                        @foreach ($error as $erro)
                                            @if($erro->problem_id==$prob->id)
                                            {{ $erro->problem_types }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $prob->product }}</td>
                                    <td>{{ $prob->fname}} {{ $prob->flastname}}</td>
                                    <td>
                                        <!-- Solution Modal -->
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#solution{{$prob->id}}">
                                            <i class="feather icon-file-text"></i>
                                            </button>
    
                                            <!-- Modal -->
                                            <div class="modal fade text-left" id="solution{{$prob->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h5>{{$prob->name}} {{$prob->lastname}}</h5>
                                                            <p><code>{{ $prob->ticket_no}}</code> - <code>Begonnen von: {{ $prob->start_user}} - {{ $prob->date}} </code> -<p><code>Fortgeschritten von: {{ $prob->progress_user}} - {{ $prob->progress_date}}</code> </p><p><code>Beendet von: {{ $prob->end_user}} - {{ $prob->end_date}}</code></p>
                                                            <h4>Problem</h4>
                                                            <p>{!! $prob->problem !!}</p>
                                  
                                                            <h4>Solution</h4>
                                                            @if($prob->solution)
                                                           <p>{!! $prob->solution !!}</p>
                                                            @else
                                                            <code><p>Das Problem ist noch nicht gelöst</p></code>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- End Solution Modal -->
                                    </td>
                                    <td><a href="{{ url('problem_view?search='.$prob->ticket_no) }}">Show</a></td>
                                    <td>
                                        @if($prob->status=="offen")
                                        <div class="badge badge-pill badge-glow badge-danger mr-1 mb-1">Offen</div>
                                        @elseif($prob->status=="in Klärung")
                                        <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1">in Klärung</div>
                                        <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1">{{ $prob->progress_user }}</div>
                                        @elseif($prob->status=="beendet")
                                        <div class="badge badge-pill badge-glow badge-success mr-1 mb-1">beendet</div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
</div>