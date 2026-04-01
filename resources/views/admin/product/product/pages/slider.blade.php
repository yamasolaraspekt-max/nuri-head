<div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
    <div class="carousel-inner">
   
        @foreach ($pro_images as $proImage)
            @if($loop->first)
                <div class="carousel-item active"> <!-- Remove active class from other items -->
                    <img src="{{ asset('images/products/'.$proImage->image)}}" class="img-fluid d-block w-50" alt="{{ $proImage->name }}">
                    <h5>{{ $proImage->name }}</h5>
                </div>
            @endif
        @endforeach

        @foreach ($pro_images as $proImage)
        <div class="carousel-item"> <!-- Remove active class from other items -->
            <img src="{{ asset('images/products/'.$proImage->image)}}" class="img-fluid d-block w-50" alt="{{ $proImage->name }}">
            <h5>{{ $proImage->name }}</h5>
        </div>
        @endforeach
   
        
    </div>
    <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>