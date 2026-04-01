<div class="card">

<div class="card-body">
    <h6 class="card-title"><i id="icon" class="fa fa-folder" ></i>  Technische Person</h6>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-selected="true">Liste</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-selected="false">nach Dienst auflisten</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- HOME TAB -->
        <div class="tab-pane" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Beratung</th>
                            <th>Planung</th>
                            <th>Kalkulation</th>
                            <th>Montage</th>
                            <th>Projektierung</th>
                            <th>Bauleitung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($technical_person as $tech1)
                            <tr>
                                <td>
                                    <a href="{{ url('next_employee/'.$tech1->empid) }}">
                                        <div class="avatar mr-1 avatar-xl">
                                            <img src="{{ asset('images/employee/'.$tech1->image) }}" alt="Avatar">
                                        </div>
                                    </a>
                                </td>
                                @foreach (['advice', 'plan', 'calculation', 'montage', 'project_planing', 'site_management'] as $field)
                                    <td>
                                        <div class="fonticon-wrap">
                                            @for ($i = 1; $i <= $tech1->$field; $i++)
                                                <i class="fa fa-star" style="color:gold"></i>
                                            @endfor
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PROFILE TAB -->
        <div class="tab-pane active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        @php
                            $categories = [
                                'advice' => 'Beratung',
                                'plan' => 'Planung',
                                'calculation' => 'Kalkulation',
                                'montage' => 'Montage',
                                'project_planing' => 'Projektierung',
                                'site_management' => 'Bauleitung',
                            ];
                        @endphp

                        @foreach ($categories as $key => $label)
                            <tr>
                                <th>{{ $label }}</th>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="list-unstyled users-list d-flex align-items-center">
                                        @foreach ($technical_person as $tech2)
                                            @if($tech2->$key >= 1)
                                                <li data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $tech2->empname }} {{ $tech2->lastname }}" class="avatar pull-up">
                                                    <a href="{{ url('next_employee/'.$tech2->empid) }}">
                                                        <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$tech2->image) }}" alt="Avatar" height="30px" width="30px">
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
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
