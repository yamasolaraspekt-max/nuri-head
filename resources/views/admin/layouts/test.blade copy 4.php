<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Swipe Progress Card</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      touch-action: pan-y;
    }
    .card {
      user-select: none;
    }
    .progress-bar {
      height: 40px;
      background-color: #e5e7eb;
      border-radius: 9999px;
      overflow: hidden;
      position: relative;
      cursor: pointer;
    }
    .progress-fill {
      height: 100%;
      background-color: #4ade80;
      transition: width 0.3s ease;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="card w-full max-w-sm p-5 bg-white shadow-lg rounded-2xl">
    <h2 class="text-xl font-bold mb-1">Task Title</h2>
    <p class="text-gray-800 mb-3">Task description goes here.</p>

    <div class="flex justify-between text-sm mb-4">
      <span class="text-blue-500 font-medium">Status: In Progress</span>
      <span class="text-yellow-500 font-medium">Priority: High</span>
    </div>

    <div class="flex items-center justify-between mb-4">
      <label class="flex items-center space-x-2">
        <input type="checkbox" id="doneCheckbox" disabled class="w-5 h-5 text-green-500" />
        <span class="text-sm text-gray-700">Done</span>
      </label>
      <div id="progressLabel" class="text-sm font-medium text-gray-700">0%</div>
    </div>

    <div id="progressBar" class="progress-bar">
      <div id="progressFill" class="progress-fill" style="width: 0%;"></div>
    </div>
  </div>

  <script>
    const progressBar = document.getElementById('progressBar');
    const progressFill = document.getElementById('progressFill');
    const progressLabel = document.getElementById('progressLabel');
    const doneCheckbox = document.getElementById('doneCheckbox');

    const steps = [0, 25, 50, 75, 100];
    let currentStep = 0;

    function updateProgress(stepIndex) {
      const value = steps[stepIndex];
      progressFill.style.width = `${value}%`;
      progressLabel.textContent = `${value}%`;
      doneCheckbox.checked = value === 100;
    }

    function handleDrag(e) {
      const rect = progressBar.getBoundingClientRect();
      const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
      const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));

      if (percent < 12.5) currentStep = 0;
      else if (percent < 37.5) currentStep = 1;
      else if (percent < 62.5) currentStep = 2;
      else if (percent < 87.5) currentStep = 3;
      else currentStep = 4;

      updateProgress(currentStep);
    }

    let isDragging = false;

    progressBar.addEventListener('mousedown', (e) => {
      isDragging = true;
      handleDrag(e);
    });

    progressBar.addEventListener('mousemove', (e) => {
      if (isDragging) handleDrag(e);
    });

    document.addEventListener('mouseup', () => isDragging = false);

    progressBar.addEventListener('touchstart', (e) => {
      isDragging = true;
      handleDrag(e);
    });

    progressBar.addEventListener('touchmove', (e) => {
      if (isDragging) handleDrag(e);
    });

    progressBar.addEventListener('touchend', () => isDragging = false);

    updateProgress(currentStep);
  </script>

</body>
</html>