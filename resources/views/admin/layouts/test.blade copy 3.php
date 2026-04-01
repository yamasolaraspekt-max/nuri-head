<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Weekly Gantt Chart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
       color: #fff;
      font-family: 'Segoe UI', sans-serif;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #cfe09b;
      margin-bottom: 30px;
    }

    .gantt-wrapper {
      display: flex;
      overflow-x: auto;
      border-radius: 10px;
      background-color: rgba(255,255,255,0.05);
      box-shadow: 0 0 10px rgba(0,255,255,0.2);
    }

    .gantt-sidebar {
      min-width: 180px;
      border-right: 1px solid rgba(255,255,255,0.1);
      padding: 10px;
      background-color: rgba(255,255,255,0.05);
    }

    .gantt-sidebar .employee {
      padding: 20px 10px;
      border-bottom: 1px dashed rgba(255,255,255,0.1);
      font-weight: 600;
    }

    .gantt-timeline {
      flex-grow: 1;
      padding: 10px;
      min-width: 900px;
    }

    .timeline-header {
      display: flex;
      justify-content: space-between;
      font-weight: 600;
      color: #c0d8ea;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      padding-bottom: 5px;
    }

    .timeline-header div {
      flex: 1;
      text-align: center;
    }

    .timeline-rows {
      display: flex;
      flex-direction: column;
    }

    .timeline-row {
      display: flex;
      height: 50px;
      position: relative;
      margin-bottom: 20px;
    }

    .timeline-cell {
      flex: 1;
      border: 1px dashed rgba(255,255,255,0.1);
      position: relative;
    }

    .task-block {
      position: absolute;
      top: 8px;
      left: 5%;
      width: 90%;
      height: 35px;
      background: linear-gradient(to right, #cfe09b, #c0d8ea);
      color: #000;
      border-radius: 10px;
      text-align: center;
      line-height: 35px;
      font-weight: 500;
      font-size: 13px;
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <h2>Custom Employee Weekly Gantt Chart</h2>

  <div class="gantt-wrapper">
    <div class="gantt-sidebar" id="sidebar">
      <!-- Employees will be injected here -->
    </div>
    <div class="gantt-timeline">
      <div class="timeline-header" id="weekDays">
        <!-- Weekdays + Dates -->
      </div>
      <div class="timeline-rows" id="ganttRows">
        <!-- Rows will be populated -->
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
    const employees = ["Alice", "Bob", "Charlie"];
    const tasks = [
      { employee: "Alice", day: "Monday", text: "Team Meeting" },
      { employee: "Bob", day: "Tuesday", text: "Design Draft" },
      { employee: "Charlie", day: "Wednesday", text: "Testing Phase" },
      { employee: "Alice", day: "Thursday", text: "Code Review" },
      { employee: "Charlie", day: "Friday", text: "Deploy App" }
    ];

    function getCurrentWeekDates() {
      const startOfWeek = new Date();
      const day = startOfWeek.getDay(); // 0 (Sun) - 6 (Sat)
      const diff = startOfWeek.getDate() - day + (day === 0 ? -6 : 1); // adjust when Sunday
      startOfWeek.setDate(diff);

      const week = [];
      for (let i = 0; i < 5; i++) {
        const d = new Date(startOfWeek);
        d.setDate(d.getDate() + i);
        const dayName = d.toLocaleDateString('en-US', { weekday: 'short' });
        const fullDate = d.toISOString().split('T')[0];
        week.push({ dayName, fullDate, label: `${dayName} (${d.getDate()}/${d.getMonth()+1})` });
      }
      return week;
    }

    $(document).ready(function () {
      const week = getCurrentWeekDates();

      // Render header
      week.forEach(day => {
        $('#weekDays').append(`<div>${day.label}</div>`);
      });

      // Render employee rows
      employees.forEach(emp => {
        $('#sidebar').append(`<div class="employee">${emp}</div>`);

        const $row = $('<div class="timeline-row"></div>');
        week.forEach(day => {
          const $cell = $('<div class="timeline-cell"></div>');
          const task = tasks.find(t => t.employee === emp && t.day.startsWith(day.dayName));
          if (task) {
            $cell.append(`<div class="task-block">${task.text}</div>`);
          }
          $row.append($cell);
        });
        $('#ganttRows').append($row);
      });
    });
  </script>
</body>
</html>
