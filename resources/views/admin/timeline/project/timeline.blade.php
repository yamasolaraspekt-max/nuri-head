@extends('admin.layouts.app')
@section('title') Project Timeline @endsection
@section('style')
<style>
   body {
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
}

.timeline {
    position: relative;
    padding: 40px 0;
    margin: 0 auto;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background-color: #ddd;
    z-index: -1;
}

/* Flexbox structure for rows */
.timeline-item {
    display: flex;
    justify-content: flex-end;
    position: relative;
    margin-bottom: 50px;
    width: 50%;
    min-height: 150px;
}

.timeline-item.left {
    justify-content: flex-end;
    padding-right: 20px;
}

.timeline-item.right {
    justify-content: flex-start;
    margin-left: 50%;
    padding-left: 20px;
}

/* Positioning the date to align with the circle */
.timeline-item .date {
    font-weight: bold;
    background-color: #004885;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 18px;
    position: absolute;
    top: 0;
    transform: translateY(-50%);
    z-index: 5;
}

.timeline-item.left .date {
    right: calc(87% + 10px);
}

.timeline-item.right .date {
    left: calc(87% + 10px);
    background-color: #ffa726; /* Set background color for seller */
}

.timeline-item .content {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 2;
    margin-top: 15px;
    width: 100%;
}

.timeline-item.left .content {
    margin-right: 20px;
}

.timeline-item.right .content {
    margin-left: 20px;
}

/* Circle element */
.timeline-item .circle {
    position: absolute;
    width: 20px;
    height: 20px;
    background-color: #004885;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    z-index: 3;
}

.timeline-item.left .circle {
    right: -10px;
}

.timeline-item.right .circle {
    left: -10px;
    background-color: #ffa726;
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
    .timeline {
        padding: 20px 0;
    }

    .timeline-item {
        width: 100%; /* Full width for both left and right */
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .timeline-item.left,
    .timeline-item.right {
        margin-left: 0;
        padding: 0;
        justify-content: center;
    }

    .timeline-item .date {
        position: relative;
        left: 0;
        right: 0;
        transform: translateY(0);
        margin-bottom: 10px;
    }

    .timeline-item.left .date,
    .timeline-item.right .date {
        right: 0;
        left: 0;
        background-color: #004885;
    }

    .timeline-item.right .date {
        background-color: #ffa726;
    }

    .timeline-item .circle {
        position: relative;
        margin-bottom: 15px;
        left: 0;
        right: 0;
    }

    .timeline::before {
        left: 50%; /* Keep the vertical line centered */
    }

    .timeline-item .content {
        text-align: center;
        margin: 0;
    }

   
}

 .timeline-item {
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.5s ease-out;
}

/* Animate in from the left */
.timeline-item.animate-left {
    opacity: 1;
    transform: translateX(0);
}

/* Animate in from the right */
.timeline-item.animate-right {
    opacity: 1;
    transform: translateX(0);
}

/* Animations on smaller screens */
@media screen and (max-width: 768px) {
    .timeline-item {
        transform: translateY(50px); /* Start with Y-offset on mobile */
    }

    .timeline-item.animate-left,
    .timeline-item.animate-right {
        transform: translateY(0); /* Slide in from the top */
    }
}

@media screen and (max-width:768px){
    .time_left {
    position: absolute;
    left: 235px;
    background: #004885;
    width: 80px;
    top: 245px;
    text-align: center;
    padding: 2px;
    border-radius: 20px;
    color: white;
    z-index: 10;
    }

     .time_right {
       position: absolute;
        left: 170px;
        background: #ffa725;
        width: 80px;
        top: 245px;
        text-align: center;
        padding: 2px;
        border-radius: 20px;
        color: white;
        z-index: 10;
    }
}
 .time_left {
       position: absolute;
    left: 532px;
    background: #004885;
    width: 80px;
    top: 187px;
    text-align: center;
    padding: 2px;
    border-radius: 20px;
    color: white;
    z-index: 10;
    }

     .time_right {
       position: absolute;
    left: -41px;
    background: #ffa725;
    width: 80px;
    top: 204px;
    text-align: center;
    padding: 2px;
    border-radius: 20px;
    color: white;
    z-index: 10;
    }


</style>
@endsection 

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Customer and Seller Timeline</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body"> 
            <div class="row d-flex">
                <div class="col-xl-9">
                      <div class="container timeline">
                            @if(count($tasks) > 0)
                                @foreach ($tasks as $task)
                                    <div class="timeline-item {{ $task->type == 'customer' ? 'left' : 'right' }}">
                                        <div class="date">{{ $task->date }}</div> 
                                        <div class="content">
                                            <h4>{{ $task->title }} </h4>
                                            <p>{{ $task->description }}</p>
                                            <div class="details">
                                                <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-file"></i></button>
                                                <button type="button" class="btn btn-icon btn-outline-warning mr-1 mb-1 waves-effect waves-light"><i class="feather icon-menu"></i></button>
                                                <button type="button" class="btn btn-icon btn-outline-danger mr-1 mb-1 waves-effect waves-light"><i class="feather icon-search"></i></button>
                                            </div>
                                        </div>
                                        <div class="time_frame">
                                            <span class="time_{{ $task->type == 'customer' ? 'left' : 'right'}}">2 Days</span>
                                        </div>
                                        <div class="circle"></div>
                                    </div>
                                @endforeach
                            @else
                                <p>No tasks available to display.</p>
                            @endif
                        </div>  
                </div>  
                <div class="col-xl-3" >
                    <div class="sider" style="position:fixed;">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">MONTAGEZEIT PROJEKT STATISTICS </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-25">
                                            <div class="browser-info">
                                                <p class="mb-25"><small>SOLL</small></p>
                                                <h4>73%</h4>
                                            </div>
                                            <div class="stastics-info text-right">
                                                <span>800 <i class="feather icon-arrow-up text-success"></i></span>
                                                <span class="text-muted d-block">13:16</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-bar-primary mb-2">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="73" aria-valuemin="73" aria-valuemax="100" style="width:73%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-25">
                                            <div class="browser-info">
                                                <p class="mb-25"><small>IST</small></p>
                                                <h4>8%</h4>
                                            </div>
                                            <div class="stastics-info text-right">
                                                <span>-200 <i class="feather icon-arrow-down text-danger"></i></span>
                                                <span class="text-muted d-block">13:16</span>
                                            </div>
                                        </div>

                                             <div class="d-flex justify-content-between mb-25">
                                            <div class="browser-info">
                                                <p class="mb-25"><small>DIFFRENCE</small></p>
                                                <h4>8%</h4>
                                            </div>
                                            <div class="stastics-info text-right">
                                                <span>-200 <i class="feather icon-arrow-down text-danger"></i></span>
                                                <span class="text-muted d-block">13:16</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-bar-primary mb-2">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="8" aria-valuemin="8" aria-valuemax="100" style="width:8%"></div>
                                        </div>
                                          
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

       
      
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const items = document.querySelectorAll(".timeline-item");

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains("left")) {
                    entry.target.classList.add("animate-left");
                } else {
                    entry.target.classList.add("animate-right");
                }
                observer.unobserve(entry.target); // Stop observing once animated
            }
        });
    }, {
        threshold: 0.2, // 20% of the item should be visible to trigger the animation
    });

    items.forEach((item) => {
        observer.observe(item);
    });
});
</script>

@endsection