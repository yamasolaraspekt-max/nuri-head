                                              
                                                   
                                                <div class="col-12">
                                                    <div class="row">
                                                        <button type="button" class="btn btn-outline-primary float-right waves-effect waves-light" data-toggle="modal" data-target="#newCloth">
                                                            erstellen
                                                        </button>
                                                        <div class="modal fade text-left" id="newCloth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary white">
                                                                        <h5 class="modal-title" id="myModalLabel140">Kleidung</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>
                                                                    <form novalidate action="{{ action('App\Http\Controllers\EmployeeClothController@store')}}" method="post"  class="custom-file-upload" enctype="multipart/form-data" >
                                                                        @csrf
                                                                        <input type="hidden" name="active_tab" id="active_tab" value="cloth"> 
                                                                          <input type="hidden" name="emp_id" value="{{ request()->id }}" >  

                                                                        <div class="modal-body">
                                                                            <div class="table-responsive">  
                                                                                <table class="table" id="cloth_table" > 
                                                                                    <thead>
                                                                                        <tr> 
                                                                                            <th>Kleidung Typ</th>
                                                                                            <th>Größe</th>
                                                                                            <th>Action</th>
                                                                                        </tr>
                                                                                    </thead> 
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <select name="cloth[0][type]" class="form-control">
                                                                                                    <option value="T-Shirt">T-Shirt</option>
                                                                                                    <option value="Jeans">Jeans</option>
                                                                                                    <option value="Pullover">Pullover</option>
                                                                                                    <option value="Hemd">Hemd</option>
                                                                                                    <option value="Rock">Rock</option>
                                                                                                    <option value="Shorts">Shorts</option>
                                                                                                    <option value="Jacke">Jacke</option>
                                                                                                    <option value="Bluse">Bluse</option>
                                                                                                    <option value="Kapuzenpullover">Kapuzenpullover</option>
                                                                                                    <option value="Hose">Hose</option>
                                                                                                    <option value="Kleid">Kleid</option>
                                                                                                    <option value="Mantel">Mantel</option>
                                                                                                    <option value="Sweatshirt">Sweatshirt</option>
                                                                                                    <option value="Strickjacke">Strickjacke</option>
                                                                                                    <option value="Leggings">Leggings</option>
                                                                                                    <option value="Anzug">Anzug</option>
                                                                                                    <option value="Hosen">Hosen</option>
                                                                                                    <option value="Blazer">Blazer</option>
                                                                                                    <option value="Poloshirt">Poloshirt</option>
                                                                                                </select>
                                                                                                
                                                                                            </td>
                                                                                        
                                                                                            <td>
                                                                                                <select name="cloth[0][size]" class="form-control">
                                                                                                    <option value="XS">XS</option>
                                                                                                    <option value="S">S</option>
                                                                                                    <option value="M">M</option>
                                                                                                    <option value="L">L</option>
                                                                                                    <option value="XL">XL</option>
                                                                                                    <option value="XXL">XXL</option>
                                                                                                    <option value="XXXL">XXXL</option>
                                                                                                    <option value="32">32</option>
                                                                                                    <option value="34">34</option>
                                                                                                    <option value="36">36</option>
                                                                                                    <option value="38">38</option>
                                                                                                    <option value="40">40</option>
                                                                                                    <option value="42">42</option>
                                                                                                    <option value="44">44</option>
                                                                                                    <option value="46">46</option>
                                                                                                    <option value="48">48</option>
                                                                                                    <option value="50">50</option>
                                                                                                    <option value="52">52</option>
                                                                                                </select>
                                                                                                
                                                                                            </td>
                                                                            
                                                                                    
                                                                                        
                                                                                        <td>
                                                                                        <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_cloth"><i class="feather icon-plus"></i></button>
                                                                                        </td>
                                                                                        </tr>
                                                                                    </tbody>  
                                                                                </table> 
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                                <button type="submit" class="btn btn-outline-primary mr-1 mb-1"><i class="feather icon-save"></i>speichern</button> 
                                                                        </div> 
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                               
                                                    <div class="row"> 
                                                        @if ($errors->clothForm->any())
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->clothForm->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif 
                                                        <div class="table-responsive">
                                                            <table class="table" id="a">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Mitarbeitername</th>
                                                                        <th>Kleidung Typ</th>
                                                                        <th>Größe</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($cloths as $cloth)
                                                                    <tr>
                                                                        
                                                                        <td>{{ $cloth->name }} {{ $cloth->lastname }}</td>
                                                                        <td>{{ $cloth->type }}</td>
                                                                        <td>{{ $cloth->size }}</td>
                                                                    
                                                                        <td>
                                                                            <form action="{{ route('cloth.destroy', $cloth->id) }}" method="POST" style="display:inline;">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1">
                                                                                    <i class="feather icon-trash-2"></i>
                                                                                </button>
                                                                            </form>                                                                                  
                                                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#cloth_edit{{$cloth->id}}"><i class="feather icon-edit"></i></button>
                                                                                
                                                                            <div class="modal fade text-left" id="cloth_edit{{$cloth->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-warning white">
                                                                                            <h5 class="modal-title" id="myModalLabel140">Kleidung Bearbeiten</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                            <form class="form-horizontal" novalidate method="post" action="{{ action('App\Http\Controllers\EmployeeClothController@update') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                            @csrf
                                                                                            <div class="modal-body"> 
                                                                                                <input type="hidden" name="active_tab" id="active_tab" value="cloth"> 
                                                                                                <input type="hidden" name="id" value="{{ $cloth->id }}"> 
                                                                                                <input type="hidden" name="emp_id" value="{{ request()->id }}">  
                                                                                                    <div class="row">
                                                                                                        <div class="col-lg-12 col-md-4 col-sm-12">
                                                                                                            <div class="form-group">
                                                                                                                <label for="account-name">Kleidung Typ</label>
                                                                                                                <select name="type" class="form-control">
                                                                                                                    <option value="T-Shirt" @if(isset($cloth) && $cloth->type == "T-Shirt") selected @endif>T-Shirt</option>
                                                                                                                    <option value="Jeans" @if(isset($cloth) && $cloth->type == "Jeans") selected @endif>Jeans</option>
                                                                                                                    <option value="Pullover" @if(isset($cloth) && $cloth->type == "Pullover") selected @endif>Pullover</option>
                                                                                                                    <option value="Hemd" @if(isset($cloth) && $cloth->type == "Hemd") selected @endif>Hemd</option>
                                                                                                                    <option value="Rock" @if(isset($cloth) && $cloth->type == "Rock") selected @endif>Rock</option>
                                                                                                                    <option value="Shorts" @if(isset($cloth) && $cloth->type == "Shorts") selected @endif>Shorts</option>
                                                                                                                    <option value="Jacke" @if(isset($cloth) && $cloth->type == "Jacke") selected @endif>Jacke</option>
                                                                                                                    <option value="Bluse" @if(isset($cloth) && $cloth->type == "Bluse") selected @endif>Bluse</option>
                                                                                                                    <option value="Kapuzenpullover" @if(isset($cloth) && $cloth->type == "Kapuzenpullover") selected @endif>Kapuzenpullover</option>
                                                                                                                    <option value="Hose" @if(isset($cloth) && $cloth->type == "Hose") selected @endif>Hose</option>
                                                                                                                    <option value="Kleid" @if(isset($cloth) && $cloth->type == "Kleid") selected @endif>Kleid</option>
                                                                                                                    <option value="Mantel" @if(isset($cloth) && $cloth->type == "Mantel") selected @endif>Mantel</option>
                                                                                                                    <option value="Sweatshirt" @if(isset($cloth) && $cloth->type == "Sweatshirt") selected @endif>Sweatshirt</option>
                                                                                                                    <option value="Strickjacke" @if(isset($cloth) && $cloth->type == "Strickjacke") selected @endif>Strickjacke</option>
                                                                                                                    <option value="Leggings" @if(isset($cloth) && $cloth->type == "Leggings") selected @endif>Leggings</option>
                                                                                                                    <option value="Anzug" @if(isset($cloth) && $cloth->type == "Anzug") selected @endif>Anzug</option>
                                                                                                                    <option value="Hosen" @if(isset($cloth) && $cloth->type == "Hosen") selected @endif>Hosen</option>
                                                                                                                    <option value="Blazer" @if(isset($cloth) && $cloth->type == "Blazer") selected @endif>Blazer</option>
                                                                                                                    <option value="Poloshirt" @if(isset($cloth) && $cloth->type == "Poloshirt") selected @endif>Poloshirt</option>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-lg-12 col-md-4 col-sm-12">
                                                                                                            <div class="form-group">
                                                                                                                <label for="account-name">Kleidung Größe</label>
                                                                                                                <select class="form-control" name="size">
                                                                                                                    <option value="XS" @if(isset($cloth) && $cloth->size == "XS") selected @endif>XS</option>
                                                                                                                    <option value="S" @if(isset($cloth) && $cloth->size == "S") selected @endif>S</option>
                                                                                                                    <option value="M" @if(isset($cloth) && $cloth->size == "M") selected @endif>M</option>
                                                                                                                    <option value="L" @if(isset($cloth) && $cloth->size == "L") selected @endif>L</option>
                                                                                                                    <option value="XL" @if(isset($cloth) && $cloth->size == "XL") selected @endif>XL</option>
                                                                                                                    <option value="XXL" @if(isset($cloth) && $cloth->size == "XXL") selected @endif>XXL</option>
                                                                                                                    <option value="XXXL" @if(isset($cloth) && $cloth->size == "XXXL") selected @endif>XXXL</option>
                                                                                                                    <option value="32" @if(isset($cloth) && $cloth->size == "32") selected @endif>32</option>
                                                                                                                    <option value="34" @if(isset($cloth) && $cloth->size == "34") selected @endif>34</option>
                                                                                                                    <option value="36" @if(isset($cloth) && $cloth->size == "36") selected @endif>36</option>
                                                                                                                    <option value="38" @if(isset($cloth) && $cloth->size == "38") selected @endif>38</option>
                                                                                                                    <option value="40" @if(isset($cloth) && $cloth->size == "40") selected @endif>40</option>
                                                                                                                    <option value="42" @if(isset($cloth) && $cloth->size == "42") selected @endif>42</option>
                                                                                                                    <option value="44" @if(isset($cloth) && $cloth->size == "44") selected @endif>44</option>
                                                                                                                    <option value="46" @if(isset($cloth) && $cloth->size == "46") selected @endif>46</option>
                                                                                                                    <option value="48" @if(isset($cloth) && $cloth->size == "48") selected @endif>48</option>
                                                                                                                    <option value="50" @if(isset($cloth) && $cloth->size == "50") selected @endif>50</option>
                                                                                                                    <option value="52" @if(isset($cloth) && $cloth->size == "52") selected @endif>52</option>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                            </div>  
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal" >abbrechen</button> 
                                                                                                <button type="submit" class="btn btn-primary">spiechern</button> 
                                                                                            </div> 
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr> 
                                                                    @endforeach
                                                                </tbody>
                                                            </table> 
                                                        </div>      
                                                    </div> 
                                                </div> 

                                
                       
 <script>
   document.addEventListener("DOMContentLoaded", function () {
    var i = 0; // Row index counter

    document.getElementById("add_cloth").addEventListener("click", function () {
        i++;
        var clothTable = document.getElementById("cloth_table").getElementsByTagName("tbody")[0];

        // ✅ Fetch employee ID safely
        var empIdElement = document.querySelector("[data-emp-id]"); // Look for an element with data-emp-id
        var empId = empIdElement ? empIdElement.value : ""; // Get value safely

        var newRow = document.createElement("tr");
        newRow.innerHTML = `
            <input type="hidden" name="cloth[${i}][emp_id]" value="${empId}">
            <td>
                <select name="cloth[${i}][type]" class="form-control">
                    <option value="T-Shirt">T-Shirt</option>
                    <option value="Jeans">Jeans</option>
                    <option value="Pullover">Pullover</option>
                    <option value="Hemd">Hemd</option>
                    <option value="Rock">Rock</option>
                    <option value="Shorts">Shorts</option>
                    <option value="Jacke">Jacke</option>
                    <option value="Bluse">Bluse</option>
                    <option value="Kapuzenpullover">Kapuzenpullover</option>
                    <option value="Hose">Hose</option>
                    <option value="Kleid">Kleid</option>
                    <option value="Mantel">Mantel</option>
                    <option value="Sweatshirt">Sweatshirt</option>
                    <option value="Strickjacke">Strickjacke</option>
                    <option value="Leggings">Leggings</option>
                    <option value="Anzug">Anzug</option>
                    <option value="Hosen">Hosen</option>
                    <option value="Blazer">Blazer</option>
                    <option value="Poloshirt">Poloshirt</option>
                </select>
            </td>
            <td>
                <select name="cloth[${i}][size]" class="form-control">
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="XXXL">XXXL</option>
                    <option value="32">32</option>
                    <option value="34">34</option>
                    <option value="36">36</option>
                    <option value="38">38</option>
                    <option value="40">40</option>
                    <option value="42">42</option>
                    <option value="44">44</option>
                    <option value="46">46</option>
                    <option value="48">48</option>
                    <option value="50">50</option>
                    <option value="52">52</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-icon rounded-circle btn-outline-danger remove_cloth">
                    <i class="feather icon-trash"></i>
                </button>
            </td>
        `;

        clothTable.appendChild(newRow);
    });

    // ✅ Fixing Remove Row Functionality
    document.addEventListener("click", function (event) {
        if (event.target.closest(".remove_cloth")) {
            var row = event.target.closest("tr");
            if (row) {
                row.remove();
            }
        }
    });
});

 </script>
                    
                             


