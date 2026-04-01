<form id="kanbanFilterForm" class="row align-items-end g-2 mb-3 mt-2">
  <div class="col-md-2">
    <label for="customerFilter" class="form-label d-flex align-items-center">
      Kunde
      <span class="badge badge-secondary ml-2 d-none" id="countCustomers">{{ $totalCustomers ?? 0 }}</span>
    </label>
    <select name="customer" id="customerFilter" class="form-control select2">
      <option value="">Alle</option>
      @foreach ($customers as $customer)
        <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <label for="stageFilter" class="form-label">Phase</label>
    <select name="stage" id="stageFilter" class="form-control select2">
      <option value="">Alle Phasen</option>
      @foreach($stageNames as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <label for="employeeFilter" class="form-label d-flex align-items-center">
      Mitarbeiter
      <span class="badge badge-secondary ml-2 d-none" id="countEmployees">{{ $totalEmployees ?? 0 }}</span>
    </label>
    <select name="employee" id="employeeFilter" class="form-control select2">
      <option value="">Alle</option>
      @foreach ($employees as $employee)
        <option value="{{ $employee->name }}">{{ $employee->name }} {{ $employee->lastname }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <label for="departmentFilter" class="form-label d-flex align-items-center">
      Abteilung
      <span class="badge badge-secondary ml-2 d-none" id="countDepartments">{{ $totalDepartments ?? 0 }}</span>
    </label>
    <select name="department" id="departmentFilter" class="form-control select2">
      <option value="">Alle</option>
      @foreach ($departments as $department)
        <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <label for="productFilter" class="form-label d-flex align-items-center">
      Produkt
      <span class="badge badge-secondary ml-2 d-none" id="countProducts">{{ $totalProducts ?? 0 }}</span>
    </label>
    <select name="product" id="productFilter" class="form-control select2">
      <option value="">Alle</option>
      @foreach ($products as $product)
        <option value="{{ $product->id }}">{{ $product->article_group }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-1">
    <label for="interestFilter" class="form-label">Interesse</label>
    <select name="interest" id="interestFilter" class="form-control select2">
      <option value="">Alle Interessen</option>
      <option value="interest">Kaufinteresse</option>
      <option value="intent">Kaufabsicht</option>
      <option value="option">Kaufoption</option>
    </select>
  </div>

  <div class="col-md-1">
    <button type="submit" class="btn btn-primary w-100">Filtern</button>
  </div>
</form>
