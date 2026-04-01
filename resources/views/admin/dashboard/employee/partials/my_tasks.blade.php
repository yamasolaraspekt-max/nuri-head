@php
 $personal_task = DB::table('personal_tasks')
    ->join('employees', 'employees.id', '=', 'personal_tasks.assigned_by') 
    ->leftJoin('employees_personal_tasks', 'employees_personal_tasks.task_id', '=', 'personal_tasks.id') 
    ->select(
        'personal_tasks.task_title',
        'personal_tasks.id',
        'employees.name as cname',
        'employees.lastname as clastname',
        'employees.image as cimage',
        'personal_tasks.priority'
    )
    ->whereNull('personal_tasks.deleted_at')
    ->distinct();

    // Clone the base query for each specific count to avoid conflicts
    $my_tasks = (clone $personal_task)
        ->where('personal_tasks.assigned_by', auth()->user()->name)
        ->orWhere('employees_personal_tasks.employee_id', auth()->user()->name)
        ->count();

    $task_by_me = (clone $personal_task)
        ->where('personal_tasks.assigned_by', auth()->user()->name)
        ->count();

    $cancel_tasks = (clone $personal_task)
        ->whereNotIn('personal_tasks.task_status', ['start', 'new', 'Published'])
        ->count();

    $task_complete = (clone $personal_task)
        ->where('personal_tasks.task_status', 'completed')
        ->where('personal_tasks.assigned_by', auth()->user()->name)
        
        ->count();

    $nothing = (clone $personal_task)
        ->where('priority', 'normal')
        ->where('personal_tasks.assigned_by', auth()->user()->name)

        ->count();

    $midium = (clone $personal_task)
        ->where('priority', 'midium')
        ->where('personal_tasks.assigned_by', auth()->user()->name)

        ->count();

    $high = (clone $personal_task)
        ->where('priority', 'high')
        ->where('personal_tasks.assigned_by', auth()->user()->name)

        ->count();

    $very_high = (clone $personal_task)
        ->where('priority', 'very high')
        ->where('personal_tasks.assigned_by', auth()->user()->name)
        ->count();

@endphp

<div class="cards">
    <h3 class="active-title">MEINE AUFGABEN</h3>
    <img src="{{ asset('images/dashboard/icon_pen.svg') }}" alt="Task Icon" class="dashboard-image link-image-active">
    <hr>
     <table class="table table-borderless" style="text-align:left;" id="dashboard_table">
        <tr >
            <td>
               <a href="{{ url('personal/task/'.auth()->user()->name)}}" class="black">Neue</a>
            </td> 
             <td>
               <span class="dashboard-title">{{ $my_tasks ?? 0 }}</span> 
            </td>  
        </tr> 


        <tr >
            <td>
               <a href="{{ url('personal/task/'.auth()->user()->name)}}" class="black">Fällige</a>
            </td> 
             <td>
               <span class="dashboard-title">{{ $task_by_me ?? 0 }}</span> 
            </td>  
        </tr> 


        <tr >
            <td>
               <a href="{{ url('personal/task/'.auth()->user()->name)}}" class="black">Wichtige</a>
            </td> 
             <td>
               <span class="dashboard-title">{{ $task_by_me ?? 0 }}</span> 
            </td>  
        </tr> 

        <tr >
            <td>
               <a href="{{ url('personal/task/'.auth()->user()->name)}}" class="black">Erledigte</a>
            </td> 
             <td>
               <span class="dashboard-title">{{ $task_by_me ?? 0 }}</span> 
            </td>  
        </tr> 
        
    </table> 
    
    <div class="mt-2">
            <a href="{{ url('new_lead_view')}}" class="black mr-1"> <i class="fa fa-star-o"></i> {{ $nothing ?? 0 }}</a>
            <a href="{{ url('new_lead_view')}}" class="black mr-1"> <i class="fa fa-hourglass-start"></i> {{ $midium ?? 0 }}</a>
            <a href="{{ url('new_lead_view')}}" class="black mr-1"> <i class="fa fa-bolt"></i> {{ $high ?? 0 }}</a>
            <a href="{{ url('new_lead_view')}}" class="black mr-1"> <i class="fa fa-free-code-camp warning"></i> {{ $very_high ?? 0 }}</a>
    </div>
     
</div>
