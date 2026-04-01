<!-- Full code updated to support Advanced Condition (JS Expression) rendering and Dynamic Multi-Value Group subfield definition -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checklist Builder - Enterprise Version</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <style>
    .field-group { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
    .field-group label { font-weight: bold; }
    .tab-content > .tab-pane:not(.active) { display: none; }
    .json-output { white-space: pre-wrap; background: #f8f9fa; padding: 10px; border: 1px solid #ccc; margin-top: 20px; }
    #formPreview input, #formPreview select, #formPreview textarea { margin-bottom: 10px; }
    .hidden-field { display: none !important; }
    .multi-group-wrapper .d-flex input { flex: 1; }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2>Enterprise Checklist Builder</h2>

  <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" class="form-control" id="sectionName" placeholder="Section Name">
    </div>
    <div class="col-md-6 text-end">
      <button class="btn btn-success" onclick="exportToJson()">Export to JSON</button>
    </div>
  </div>

  <ul class="nav nav-tabs" id="sectionTabs" role="tablist"></ul>
  <div class="tab-content" id="tabContent"></div>

  <div class="field-group">
    <div class="mb-2">
      <label>Field Label</label>
      <input type="text" class="form-control" id="fieldLabel" placeholder="Label">
    </div>
    <div class="mb-2">
      <label>Field Name</label>
      <input type="text" class="form-control" id="fieldName">
    </div>
    <div class="mb-2">
      <label>Field Type</label>
      <select class="form-select" id="fieldType">
        <option value="text">Text</option>
        <option value="number">Number</option>
        <option value="select">Select</option>
        <option value="checkbox">Checkbox</option>
        <option value="formula">Formula</option>
        <option value="textarea">Textarea</option>
        <option value="date">Date</option>
        <option value="file">File</option>
        <option value="multi-group">Multi-Value Group</option>
      </select>
    </div>
    <div class="mb-2" id="selectOptions" style="display: none;">
      <label>Options (comma separated)</label>
      <input type="text" class="form-control" id="options">
    </div>
    <div class="mb-2" id="formulaField" style="display: none;">
      <label>Formula Expression</label>
      <input type="text" class="form-control" id="formula">
    </div>
    <div class="mb-2" id="multiGroupFields" style="display: none;">
      <label>Multi-Group Subfields (comma separated)</label>
      <input type="text" class="form-control" id="multiFields" placeholder="e.g. width,height,depth">
    </div>
    <div class="mb-2">
      <label>Default Value</label>
      <input type="text" class="form-control" id="defaultValue">
    </div>
    <div class="mb-2">
      <label>Min / Max (number only)</label>
      <input type="number" class="form-control mb-1" id="minValue" placeholder="Min">
      <input type="number" class="form-control" id="maxValue" placeholder="Max">
    </div>
    <div class="mb-2">
      <label>Regex Validation</label>
      <input type="text" class="form-control" id="pattern" placeholder="e.g. ^\\d{4}$ for 4-digit year">
    </div>
    <div class="mb-2">
      <label>Advanced Condition (JS Expression)</label>
      <input type="text" class="form-control" id="advancedCondition" placeholder="e.g. salary > 2000">
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="required">
      <label class="form-check-label" for="required">Required</label>
    </div>
    <button class="btn btn-primary" onclick="addFieldToSection()">Add Field</button>
  </div>

  <h4 class="mt-4">Live Form Preview</h4>
  <form id="formPreview" class="border rounded p-3"></form>

  <div class="json-output" id="jsonOutput"></div>
</div>

<script>
const fieldsBySection = {};
const previewForm = document.getElementById('formPreview');

function evaluateFormula(formula, values) {
  try {
    const keys = Object.keys(values);
    const vals = Object.values(values);
    const fn = new Function(...keys, `return ${formula}`);
    return fn(...vals);
  } catch (e) {
    return 'Error';
  }
}

function renderPreview() {
  previewForm.innerHTML = '';
  let formData = {};

  Object.keys(fieldsBySection).forEach(section => {
    fieldsBySection[section].forEach(field => {
      const wrapper = document.createElement('div');
      wrapper.className = 'mb-3';
      wrapper.dataset.name = field.name;

      let show = true;
      if (field.advancedCondition) {
        try {
          const cond = new Function(...Object.keys(formData), `return ${field.advancedCondition}`);
          show = cond(...Object.values(formData));
        } catch (e) {
          show = false;
        }
      }
      if (!show) return;

      const label = document.createElement('label');
      label.textContent = field.label;
      wrapper.appendChild(label);

      let input;
      if (field.type === 'select') {
        input = document.createElement('select');
        input.className = 'form-select';
        field.options.split(',').forEach(opt => {
          const option = document.createElement('option');
          option.value = opt.trim();
          option.textContent = opt.trim();
          input.appendChild(option);
        });
      } else if (field.type === 'textarea') {
        input = document.createElement('textarea');
        input.className = 'form-control';
      } else if (field.type === 'checkbox') {
        input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'form-check-input';
      } else if (field.type === 'multi-group') {
        const groupWrapper = document.createElement('div');
        groupWrapper.classList.add('multi-group-wrapper');

        const addBtn = document.createElement('button');
        addBtn.className = 'btn btn-sm btn-outline-primary mb-2';
        addBtn.type = 'button';
        addBtn.textContent = 'Add Row';

        const container = document.createElement('div');
        container.classList.add('multi-group-container');

        const subfields = field.subfields?.split(',').map(s => s.trim()) || ['width', 'height', 'depth'];

        addBtn.onclick = () => {
          const row = document.createElement('div');
          row.className = 'd-flex gap-2 mb-2';
          subfields.forEach(sub => {
            const subInput = document.createElement('input');
            subInput.className = 'form-control';
            subInput.placeholder = sub;
            subInput.name = `${field.name}[${sub}][]`;
            row.appendChild(subInput);
          });
          const del = document.createElement('button');
          del.className = 'btn btn-sm btn-danger';
          del.textContent = '✕';
          del.onclick = () => row.remove();
          row.appendChild(del);
          container.appendChild(row);
        };

        addBtn.click();
        groupWrapper.appendChild(addBtn);
        groupWrapper.appendChild(container);
        wrapper.appendChild(groupWrapper);
        previewForm.appendChild(wrapper);
        return;
      } else {
        input = document.createElement('input');
        input.type = field.type;
        input.className = 'form-control';
      }

      if (field.required) input.required = true;
      if (field.pattern) input.pattern = field.pattern;
      if (field.defaultValue) input.value = field.defaultValue;

      if (field.type === 'formula') {
        input.readOnly = true;
        input.value = evaluateFormula(field.formula, formData);
      } else {
        input.oninput = () => {
          formData[field.name] = input.type === 'checkbox' ? input.checked : input.value;
          renderPreview();
        };
      }

      input.name = field.name;
      wrapper.appendChild(input);
      previewForm.appendChild(wrapper);
      formData[field.name] = input.value;
    });
  });
}

function addFieldToSection() {
  const sectionName = document.getElementById('sectionName').value;
  if (!sectionName) return alert('Please enter a section name.');

  const newField = {
    label: document.getElementById('fieldLabel').value,
    name: document.getElementById('fieldName').value,
    type: document.getElementById('fieldType').value,
    defaultValue: document.getElementById('defaultValue').value,
    options: document.getElementById('options').value,
    formula: document.getElementById('formula').value,
    subfields: document.getElementById('multiFields').value,
    min: document.getElementById('minValue').value,
    max: document.getElementById('maxValue').value,
    pattern: document.getElementById('pattern').value,
    advancedCondition: document.getElementById('advancedCondition').value,
    required: document.getElementById('required').checked
  };

  if (!fieldsBySection[sectionName]) fieldsBySection[sectionName] = [];
  fieldsBySection[sectionName].push(newField);

  if (!document.getElementById(`tab-${sectionName}`)) {
    const tab = document.createElement('li');
    tab.className = 'nav-item';
    tab.innerHTML = `<button class="nav-link ${Object.keys(fieldsBySection).length === 1 ? 'active' : ''}" id="tab-${sectionName}" data-bs-toggle="tab" data-bs-target="#content-${sectionName}" type="button">${sectionName}</button>`;
    document.getElementById('sectionTabs').appendChild(tab);

    const content = document.createElement('div');
    content.className = `tab-pane fade ${Object.keys(fieldsBySection).length === 1 ? 'show active' : ''}`;
    content.id = `content-${sectionName}`;
    document.getElementById('tabContent').appendChild(content);
  }

  renderPreview();
}

function exportToJson() {
  document.getElementById('jsonOutput').textContent = JSON.stringify(fieldsBySection, null, 2);
}

// Field type change handlers
const fieldTypeSelect = document.getElementById('fieldType');
fieldTypeSelect.onchange = function () {
  document.getElementById('selectOptions').style.display = this.value === 'select' ? 'block' : 'none';
  document.getElementById('formulaField').style.display = this.value === 'formula' ? 'block' : 'none';
  document.getElementById('multiGroupFields').style.display = this.value === 'multi-group' ? 'block' : 'none';
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
