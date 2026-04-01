<div class="card-content">
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
            @if($product_pv)
            <li class="nav-item">
                <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#pv" role="tab" aria-controls="description" aria-selected="true">PHOTOVOLTAIK</a>
            </li>
            @endif
            @if($product_radiator)
            <li class="nav-item">
                <a class="nav-link" id="home-tab-fill" data-toggle="tab" href="#radiator" role="tab" aria-controls="home-fill" aria-selected="true">WICHSELRICHTER</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#line" role="tab" aria-controls="line" aria-selected="false">LINE CHART</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="messages-tab-fill" data-toggle="tab" href="#bar" role="tab" aria-controls="bar" aria-selected="false">BAR CHART</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content pt-1">
     
            <div class="tab-pane active" id="pv" role="tabpanel" aria-labelledby="description">
                  @include('admin.product.product.pages.pv')  
            </div>
            <div class="tab-pane" id="radiator" role="tabpanel" aria-labelledby="home-tab-fill">
                @include('admin.product.product.pages.radiator')  
            </div>

            <div class="tab-pane" id="line" role="tabpanel" aria-labelledby="profile-tab-fill">
                <div class="row match-height">
                   
                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card">
                            <div>
                                <canvas id="line-chart"></canvas>
                              </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-content">
                                <div>
                                    <canvas id="myChart"></canvas>
                                  </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
            <div class="tab-pane" id="bar" role="tabpanel" aria-labelledby="messages-tab-fill">
                <ul class="list-group">
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-video"> </i>
                        </p>
                        <span  style="margin-left: 10px !important">Cupcake sesame snaps dessert marzipan.</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-video"> </i>
                        </p>
                        <span  style="margin-left: 10px !important">Jelly beans jelly-o gummi bears chupa chups marshmallow.</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-video"> </i>
                        </p>
                        <span style="margin-left: 10px !important">Bonbon macaroon gummies pie jelly</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var x = new Chart(document.getElementById("line-chart"), {
   type: 'line',
   data: {
        labels: [0, 50, 60, 70, 80, 90, 100],
        datasets: [{
            data: [0, 50, 60, 70, 80, 90, 100],
            label: "Wirkungsgrad",
            borderColor: "#8fc73e",
            fill: false
        }]
    },
   options: {
      responsive: true
   }
});

</script>

<script>
    const ctx = document.getElementById('myChart');
  
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['0', '5', '10', '20', '30', '50', '75', '100'],
        datasets: [{
            label: "Wirkungsgrad",
            borderColor: "#8fc73e",
            backgroundColor: "#8fc73e",
          data: [0, 77.2, 87.6, 93.4, 95.4, 96.8, 97.2, 97.3],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
@endsection