                <div class="card-content">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span><h4>Elektrische Daten - DC</h4></span>
                                        </div>
                                    
                                    </div>
                                </div>
                
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Name</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="name" class="form-control" value="{{old('name')}}" name="name" >
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Beschreibung </span>
                                        </div>
                                        <div class="col-md-8">
                                            <textarea id="description" class="form-control" name="description" >{{old('description')}}</textarea>
                                        </div>
                                    </div>
                                </div>

                                        
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Erstellt am  </span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" id="created_on" class="form-control" value="{{old('created_on')}}" name="created_on" >
                                        </div>
                                    </div>
                                </div>

                                

                            </div>
                        </div>
                    </div>
                </div>   