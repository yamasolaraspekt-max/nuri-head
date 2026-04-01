@section('style')
 <style>
.phase_list:hover {
    color: red;
    border-bottom: 2px solid red;
}

</style>

@endsection

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="nav-vertical">
                <ul class="nav nav-tabs nav-left flex-column" role="tablist" > 
                    @foreach ($phases as $list) 
                        <li class="nav-item">
                            <a class="nav-link @if($loop->first) active @endif" style="font-size: 24px;"
                            id="baseVerticalLeft-tab{{$loop->index}}" 
                            data-toggle="tab" 
                            aria-controls="tabVerticalLeft{{$loop->index}}" 
                            href="#tabVerticalLeft{{$loop->index}}" 
                            role="tab" 
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                              <i class="feather icon-arrow-right"></i>  {{$list->phase_name}}
                            </a>
                        </li> 
                    @endforeach 
                </ul>

                <div class="tab-content">
                    @foreach ($phases as $list)
                        <div class="tab-pane fade @if($loop->first) show active @endif" 
                            id="tabVerticalLeft{{$loop->index}}" 
                            role="tabpanel" 
                            aria-labelledby="baseVerticalLeft-tab{{$loop->index}}">
                            <p>Content for the {{$list->phase_name}} phase. Customize this content as needed.</p>
                        </div> 
                    @endforeach
                </div>
            </div>
        </div>
    </div>