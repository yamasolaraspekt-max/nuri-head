<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offer Configuration</title>
  <style>
    body {
      display: flex;
      margin: 0;
      font-family: sans-serif;
    }
    #left-sidebar, #right-sidebar {
      width: 200px;
      padding: 1rem;
      background: #f0f0f0;
      height: 100vh;
      overflow-y: auto;
      flex-shrink: 0;
    }
    #pages-container-wrapper {
      flex: 1;
      background: #eaeaea;
      overflow-y: auto;
      max-height: 100vh;
    }
    #pages-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
    }
    .a4-page {
      width: 210mm;
      height: 297mm;
      background: white;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
      position: relative;
      display: flex;
      flex-direction: column;
      padding: 1rem;
    }
    .page-header, .page-footer {
      text-align: center;
      font-weight: bold;
      padding: 5px;
      border-bottom: 1px solid #ccc;
    }
    .page-footer {
      border-top: 1px solid #ccc;
      margin-top: auto;
    }
    .page-body {
      flex: 1;
      display: flex;
      gap: 1rem;
      padding: 1rem 0;
    }
    .column {
      flex: 1;
      border: 1px dashed #ccc;
      min-height: 200px;
      padding: 0.5rem;
    }
    .tool {
      background: #fff;
      margin: 0.5rem 0;
      padding: 0.5rem;
      border: 1px solid #ccc;
      cursor: grab;
      text-align: center;
    }
    .draggable {
      background:rgb(255, 255, 255);
      padding: 5px;
      margin: 5px 0;
      border: 1px solid #ccc;
      cursor: move;
    }
    .page-number {
      position: absolute;
      bottom: 5px;
      right: 10px;
      font-size: 12px;
      color: #888;
    }
    .summary-box {
      background: #fff;
      padding: 1rem;
      border: 1px solid #ccc;
      margin-top: 2rem;
    }
  </style>
<style>
  #toolbar {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #ccc;
    padding: 0.5rem;
    display: none;
    z-index: 9999;
    gap: 10px;
  }
  #toolbar select, #toolbar input {
    padding: 2px 5px;
  }
</style>
</head>
<body>
<div id="toolbar">
  <select id="fontSelect">
    <option value="Arial">Arial</option>
    <option value="Georgia">Georgia</option>
    <option value="Courier New">Courier New</option>
    <option value="Times New Roman">Times New Roman</option>
  </select>
  <input type="color" id="fontColor">
  <select id="fontSize">
    <option value="12px">12px</option>
    <option value="14px">14px</option>
    <option value="16px">16px</option>
    <option value="18px">18px</option>
    <option value="24px">24px</option>
    <option value="32px">32px</option>
  </select>
  <button onclick="execCmd('bold')">Bold</button>
  <button onclick="execCmd('italic')">Italic</button>
</div>

<div id="left-sidebar">
  <button onclick="addCoverLetter()" style="margin-bottom: 1rem; background: #93c21c; color: white; border: none; padding: 0.5rem; width: 100%;">+ Cover Letter</button>
  <h4>Tools</h4>
  <div class="tool" draggable="true" data-type="product">Product</div>
  <div class="tool" draggable="true" data-type="product-set">Product Set</div>
  <div class="tool" draggable="true" data-type="product-tech">Technical Product</div>
  <div class="tool" draggable="true" data-type="note">Note</div>
  <div class="tool" draggable="true" data-type="image">Image</div>
  <div class="tool" draggable="true" data-type="line">Line</div>
  <div class="tool" draggable="true" data-type="calc">Calculation</div>
  <button onclick="addNewPage()">+ Add Page</button>
  <button onclick="addHeadingTitle()" style="margin-top: 0.5rem; background: #93c21c; color: white; border: none; padding: 0.5rem; width: 100%;">+ Heading Title</button>
  <button onclick="addCustomerNote()" style="margin-top: 0.5rem; background: #93c21c; color: white; border: none; padding: 0.5rem; width: 100%;">+ Customer Note</button>
  <div class="summary-box">
  <h5>Settings</h5>
  <label>Header Image: <input type="file" accept="image/*" id="headerImageInput" /></label><br>
  <label>Footer Image: <input type="file" accept="image/*" id="footerImageInput" /></label>
</div>
</div>

<div id="pages-container-wrapper">
  <div id="pages-container">
    <!-- Pages will be added here dynamically -->
  </div>
</div>

<div id="right-sidebar">
  <h4>Summary</h4>
  <div id="summary"></div>
  <div id="totalPrice">Total Price: 0 €</div>
</div>

<script>
let pageCount = 0;
function addNewPage() {
  pageCount++;
  const page = document.createElement('div');
  page.classList.add('a4-page');
  page.setAttribute('data-page', pageCount);
  page.innerHTML = `
    <div class="page-header">Header</div>
    <div class="page-body">
      <div class="column" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
    </div>
    <div class="page-footer">Footer</div>
    <div class="page-number">Page ${pageCount}</div>
  `;
  document.getElementById('pages-container').appendChild(page);
  updateHeaderFooter();
}

function updateHeaderFooter() {
  const headerInput = document.getElementById('headerImageInput');
  const footerInput = document.getElementById('footerImageInput');

  const updateImage = (input, selector) => {
    const file = input?.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
      document.querySelectorAll(selector).forEach(el => {
        el.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: auto;" />`;
      });
    };
    reader.readAsDataURL(file);
  };

  updateImage(headerInput, '.page-header');
  updateImage(footerInput, '.page-footer');
}

function allowDrop(ev) {
  ev.preventDefault();
}

document.querySelectorAll('.tool').forEach(el => {
  el.addEventListener('dragstart', function (e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.type);
  });
});

function drop(ev) {
  ev.preventDefault();
  const type = ev.dataTransfer.getData('text');
  const block = document.createElement('div');
  block.classList.add('draggable');
  block.setAttribute('contenteditable', 'true');
  let price = 0;
  switch(type) {
    case 'product':
      block.innerHTML = `
        <div style="display: flex; gap: 10px; align-items: start;">
          <img src='https://via.placeholder.com/100x150' alt='Product Image' style='width:100px; height:auto;'>
          <div>
            <h4 style='color:#0074c7;'>MODUL TRINA VERTEX S TSM-455 MIT 455 W</h4>
            <ul style='margin: 0; padding-left: 16px; font-size: 12px;'>
              <li>Glas-Glas Modul TOP CON Solarzelle</li>
              <li>Nennleistung: 455 WP</li>
              <li>Rahmen: Aluminium schwarz eloxiert</li>
              <li>Produktgarantie: 25 Jahre</li>
              <li>Lineare Leistungsgarantie: 87,40% nach 30 Jahren</li>
              <li>Abmessungen: 1.762 x 1.134 x 30 mm</li>
              <li>Gewicht: 21 kg</li>
              <li>Modulwirkungsgrad: 22.8%</li>
            </ul>
            <p style='font-weight:bold; color:#93c21c;'>LEISTUNG: 13,195 kWp</p>
          </div>
        </div>`;
      price = 100;
      break;
    case 'product-set':
      block.innerHTML = '<strong contenteditable="true">Product Set Block</strong>';
      price = 300;
      break;
    case 'product-tech':
      block.innerHTML = '<strong contenteditable="true">Technical Product Block</strong>';
      price = 200;
      break;
    case 'note':
      block.textContent = 'Note Block';
      break;
    case 'image':
      block.innerHTML = '<input type="file" onchange="loadImage(event, this)" />';
      break;
    case 'line':
      block.innerHTML = '<hr />';
      break;
    case 'calc':
      block.textContent = 'Calculation Block';
      break;
  }
  block.dataset.price = price;
  ev.target.appendChild(block);
  updateSummary();
}

function loadImage(event, input) {
  const file = input.files[0];
  const reader = new FileReader();
  reader.onload = function(e) {
    const img = document.createElement('img');
    img.src = e.target.result;
    img.style.maxWidth = '100%';
    input.parentElement.innerHTML = '';
    input.parentElement.appendChild(img);
  }
  reader.readAsDataURL(file);
}

function updateSummary() {
  let total = 0;
  const summary = {};
  document.querySelectorAll('.a4-page').forEach(page => {
    let pageTotal = 0;
    const pageNum = page.dataset.page;
    const blocks = page.querySelectorAll('.draggable');
    blocks.forEach(block => {
      const type = block.textContent;
      const price = parseFloat(block.dataset.price || 0);
      pageTotal += price;
    });
    summary[`Page ${pageNum}`] = pageTotal;
    total += pageTotal;
  });
  const summaryBox = document.getElementById('summary');
  summaryBox.innerHTML = Object.entries(summary).map(([k, v]) => `<div>${k}: ${v} €</div>`).join('');
  document.getElementById('totalPrice').textContent = `Total Price: ${total} €`;
}

function addCoverLetter() {
  const cover = document.createElement('div');
  cover.classList.add('a4-page');
  cover.setAttribute('data-page', 'cover');

  const inputId = 'coverImageInput_' + Date.now();
  cover.innerHTML = `
    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; position:relative;">
      <input type="file" accept="image/*" id="${inputId}" style="position:absolute; top:10px; left:10px; z-index:10;">
      <img src="" alt="Cover Image" style="width:100%; height:100%; object-fit:cover; display:none;">
    </div>`;

  document.getElementById('pages-container').appendChild(cover);

  const input = cover.querySelector(`#${inputId}`);
  const img = cover.querySelector('img');

  input.addEventListener('change', function (event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
      img.src = e.target.result;
      img.style.display = 'block';
      input.style.display = 'none';
    }
    reader.readAsDataURL(file);
  });
}

function addCustomerNote() {
  pageCount++;
  const notePage = document.createElement('div');
  notePage.classList.add('a4-page');
  notePage.setAttribute('data-page', pageCount);
  const bannerId = 'bannerUpload_' + Date.now();
  notePage.innerHTML = `
    <div style="width:100%; text-align:center; margin-bottom: 1rem; position:relative;">
      <input type="file" accept="image/*" id="${bannerId}" style="position:absolute; top:0; left:0; z-index:10;">
      <img src="https://via.placeholder.com/600x80?text=Banner" style="width:100%; height:auto; display:block;" />
    </div>
    <div class="page-header">Customer Note</div>
    <div class="page-body">
      <div class="column">
        <div contenteditable="true" style="width: 100%; height: 100%; font-size: 16px; line-height: 1.5;">
          Sehr geehrter Kunde,<br><br>
          Bitte lesen Sie die nachfolgenden Hinweise zu Ihrem individuellen Angebot. Bei Rückfragen stehen wir Ihnen gerne zur Verfügung.<br><br>
          Mit freundlichen Grüßen,<br>Ihr Team
        </div>
      </div>
    </div>
    <div class="page-footer">Footer</div>
    <div class="page-number">Page ${pageCount}</div>
  `;
  document.getElementById('pages-container').appendChild(notePage);
  updateHeaderFooter();

  const input = notePage.querySelector(`#${bannerId}`);
  const img = notePage.querySelector('img');
  input.addEventListener('change', function (event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
  updateHeaderFooter();
  updateHeaderFooter();
}

document.addEventListener('selectionchange', () => {
  const selection = window.getSelection();
  if (selection.rangeCount > 0 && selection.anchorNode && selection.anchorNode.parentElement.closest('[contenteditable]')) {
    const range = selection.getRangeAt(0);
    const rect = range.getBoundingClientRect();
    const toolbar = document.getElementById('toolbar');
    toolbar.style.display = 'flex';
    toolbar.style.top = `${window.scrollY + rect.top - 50}px`;
  } else {
    document.getElementById('toolbar').style.display = 'none';
  }
});

function execCmd(command) {
  document.execCommand(command);
}

document.getElementById('fontColor').addEventListener('change', function () {
  document.execCommand('foreColor', false, this.value);
});

document.getElementById('fontSize').addEventListener('change', function () {
  const sizeMap = {
    '12px': '1', '14px': '2', '16px': '3', '18px': '4', '24px': '5', '32px': '6'
  };
  document.execCommand('fontSize', false, sizeMap[this.value]);
});

document.getElementById('fontSelect').addEventListener('change', function () {
  document.execCommand('fontName', false, this.value);
});

function addHeadingTitle() {
  const block = document.createElement('div');
  block.classList.add('draggable');
  block.setAttribute('contenteditable', 'true');
  block.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #93c21c; padding-bottom: 5px; font-size: 16px;">
      <span style="color: #93c21c; font-weight: bold;">ANGEBOT</span> <span style="color: #555;">SA-AG25175</span>
      <img src="https://via.placeholder.com/100x30?text=SOLAR+ASPEKT" alt="SOLAR ASPEKT" style="height: 30px;">
    </div>`;

  const firstColumn = document.querySelector('.a4-page .page-body .column');
  if (firstColumn) firstColumn.insertBefore(block, firstColumn.firstChild);
}

</script>

</body>
</html>
