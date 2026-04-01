<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Project Timeline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.min.js"></script>
    <style>
        body { background-color: #181818; color: #ffffff; font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; }
        .gantt-wrapper { width: 100%; overflow-x: auto; white-space: nowrap; position: relative; }
        #gantt { min-width: 5000px; height: 500px; }
        .task-container { max-width: 1200px; margin: 20px auto; background: #222; padding: 20px; border-radius: 8px; }
        .connecting-line { position: absolute; height: 2px; background: orange; z-index: 10; }
    </style>
</head>
<body>
    <h2>Interactive Project Timeline</h2>
    <div class="task-container">
        <div class="gantt-wrapper" id="gantt-container">
            <svg id="gantt"></svg>
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
            let tasks = [
                { id: "1", name: "Task 1", start: "2025-03-20", end: "2025-03-21", progress: 80, dependencies: "" },
                { id: "2", name: "Task 2", start: "2025-03-22", end: "2025-03-23", progress: 50, dependencies: "1" },
                { id: "3", name: "Task 3", start: "2025-03-24", end: "2025-03-25", progress: 30, dependencies: "2" },
                { id: "4", name: "Task 4", start: "2025-03-26", end: "2025-03-27", progress: 0, dependencies: "3" }
            ];
            
            let selectedTask = null;
            let gantt = new Gantt("#gantt", tasks, {
                on_click: function(task) {
                    selectedTask = task.id;
                },
                on_date_change: function(task, start, end) {
                    console.log(`Task ${task.name} changed to ${start} - ${end}`);
                },
                on_progress_change: function(task, progress) {
                    console.log(`Task ${task.name} progress updated to ${progress}%`);
                },
                view_modes: ["Day", "Week", "Month", "Quarter", "Year"],
                view_mode: "Month",
                bar_height: 30,
                padding: 50,
                date_format: "YYYY-MM-DD",
                start_date: "2025-03-19",
                end_date: "2025-03-28"
            });
            
            $("#gantt-container").on("click", function(event) {
                if (selectedTask) {
                    let targetTaskId = prompt("Enter task ID to connect to:");
                    if (targetTaskId && tasks.some(t => t.id === targetTaskId)) {
                        let taskToUpdate = tasks.find(t => t.id === selectedTask);
                        taskToUpdate.dependencies = targetTaskId;
                        selectedTask = null;
                        gantt.refresh(tasks);
                    } else {
                        alert("Invalid task ID.");
                    }
                }
            });
            
            $("#gantt-container").on("dblclick", function(event) {
                let removeDependency = prompt("Enter task ID to remove dependency:");
                if (removeDependency && tasks.some(t => t.id === removeDependency)) {
                    let taskToUpdate = tasks.find(t => t.id === removeDependency);
                    taskToUpdate.dependencies = "";
                    gantt.refresh(tasks);
                } else {
                    alert("Invalid task ID.");
                }
            });
        });
    </script>
</body>
</html>
