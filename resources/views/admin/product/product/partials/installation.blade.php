<div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">geschätzte Montagezeit</h4>
                                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <ul class="activity-timeline timeline-left list-unstyled">
                                                @foreach ($installation as $install )
                                                <li>
                                                    @if ($install->rate >= 5)
                                                        <div class="timeline-icon bg-danger">
                                                            <i class="fa fa-battery-quarter font-medium-2"></i>
                                                        </div>
                                                    @elseif ($install->rate >= 3 && $install->rate <= 4)
                                                        <div class="timeline-icon bg-warning">
                                                            <i class="fa fa-battery-half font-medium-2"></i>
                                                        </div>
                                                    @else
                                                        <div class="timeline-icon bg-success">
                                                            <i class="fa fa-battery-full font-medium-2"></i>
                                                        </div>
                                                    @endif
                                                    <div class="timeline-info">
                                                        <p class="font-weight-bold">{{ $install->case }}</p>
                                                        <span>{{ Str::limit($install->description, 50) }} </span>
                                                    </div>
                                                    <small class="text-muted">{{ $install->rate }}</small>
                                                </li>
                                            @endforeach                                        
                                                
                                        
                                    
                                            </ul>
                                            <p>
                                                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
                                                
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        
                                                    <i class="feather icon-edit"></i></button>
                                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -7px, 0px);">
                                    
                                                        <span class="dropdown-item">
                                                        <a href="{{ url('/product_installation/'.$data->id) }}"><i class="feather icon-edit"></i> Bearbeiten</a> 
                                                        </span>
                                                    
                                                        
                                                    </div>
                                                </div>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                   