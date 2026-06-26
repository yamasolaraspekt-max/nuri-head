<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Ticket Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" />
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .dashboard-wrapper {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 300px;
      background-color: #fff;
      padding: 2rem 1rem;
      box-shadow: 2px 0 15px rgba(0, 0, 0, 0.05);
    }
    .main-content {
      flex-grow: 1;
      padding: 2rem;
    }
    .info-card {
      padding: 1.5rem;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
    }
    .kanban-board {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
    }
    .kanban-column {
      min-width: 300px;
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 10px;
    }
    .task-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .task-card .badge {
      margin-top: 0.5rem;
    }
    .timeline {
      position: relative;
      border-left: 4px solid #0d6efd;
      padding-left: 1.5rem;
    }
    .timeline-entry {
      position: relative;
      margin-bottom: 2rem;
      padding-left: 2rem;
    }
    .timeline-entry::before {
      content: "";
      position: absolute;
      left: -12px;
      top: 0.25rem;
      width: 1rem;
      height: 1rem;
      background-color: #0d6efd;
      border-radius: 50%;
      border: 2px solid #fff;
      box-shadow: 0 0 0 4px #cfe2ff;
    }
    .timeline-entry .time {
      font-size: 0.8rem;
      color: #6c757d;
    }

    .sidebar .accordion-button:not(.collapsed) {
      background-color: #eef4ff;
    }


    @media (max-width: 768px) {
      .dashboard-wrapper {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        padding: 1rem;
      }

      .main-content {
        padding: 1rem;
      }

      #mini-calendar {
        width: 100% !important;
      }

      .flatpickr-calendar {
        max-width: 100% !important;
        font-size: 14px;
      }
    }

    @media (max-width: 768px) {
      #ticketChart {
        width: 100% !important;
        height: auto !important;
      }
    }


    .accordion-button.collapsed {
      background: #f8f9fa;
    }
    .accordion-button:not(.collapsed) {
      background: #eef6ff;
    }
    .accordion-body ul li {
      margin-bottom: 0.5rem;
    }

  </style>
</head>
<body>
<div class="dashboard-wrapper">
<div class="sidebar position-sticky top-0">
  <div class="text-center mb-4">
    <img src="https://i.pravatar.cc/80?img=11" class="rounded-circle shadow-sm" alt="Avatar">
    <h6 class="mt-2">John Doe</h6>
    <span class="text-muted small">Customer</span>
    <div class="mt-2 d-flex justify-content-center gap-2">
      <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></button>
      <button class="btn btn-sm btn-outline-primary"><i class="fas fa-envelope"></i></button>
      <button class="btn btn-sm btn-outline-success"><i class="fas fa-phone"></i></button>
    </div>
  </div>

  <div class="accordion" id="sidebarAccordion">
    <!-- Customer Info -->
    <div class="accordion-item border-0 mb-2">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed bg-white shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustomer">
          <i class="fas fa-user me-2 text-primary"></i> Info
        </button>
      </h2>
      <div id="collapseCustomer" class="accordion-collapse collapse show">
        <div class="accordion-body">
          <p><strong>Email:</strong> john@example.com</p>
          <p><strong>Phone:</strong> +49 123 456789</p>
        </div>
      </div>
    </div>

    <!-- Product -->
    <div class="accordion-item border-0 mb-2">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed bg-white shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProduct">
          <i class="fas fa-box me-2 text-success"></i> Product
        </button>
      </h2>
      <div id="collapseProduct" class="accordion-collapse collapse">
        <div class="accordion-body">
          <p><strong>Model:</strong> Smart Thermostat X100</p>
          <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
        </div>
      </div>
    </div>

    <!-- Appointments -->
    <div class="accordion-item border-0 mb-2">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed bg-white shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCalendar">
          <i class="fas fa-calendar-alt me-2 text-info"></i> Appointments
        </button>
      </h2>
      <div id="collapseCalendar" class="accordion-collapse collapse">
        <div class="accordion-body">
            <div style="max-height: 250px; overflow-y: auto;">
              <input type="text" id="mini-calendar" class="form-control" readonly style="background: #fff; cursor: pointer;">
            </div>
            <div class="mt-2"><strong>Upcoming:</strong> 3 events</div>
          </div>

      </div>
    </div>

    <!-- Quick Filters -->
    <div class="accordion-item border-0">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed bg-white shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter">
          <i class="fas fa-filter me-2 text-warning"></i> Quick Filters
        </button>
      </h2>
      <div id="collapseFilter" class="accordion-collapse collapse">
        <div class="accordion-body">
          <button class="btn btn-sm w-100 mb-2 btn-outline-danger"><i class="fas fa-bolt me-1"></i> Urgent Tickets</button>
          <button class="btn btn-sm w-100 mb-2 btn-outline-primary"><i class="fas fa-hourglass-half me-1"></i> In Progress</button>
          <button class="btn btn-sm w-100 btn-outline-success"><i class="fas fa-check-circle me-1"></i> Completed</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Dark Mode Toggle -->
  <div class="form-check form-switch mt-4 ps-0 text-center">
    <input class="form-check-input" type="checkbox" id="darkModeToggle">
    <label class="form-check-label ms-2" for="darkModeToggle"><i class="fas fa-moon"></i> Dark Mode</label>
  </div>
</div>



    <div class="main-content">
      <!-- Widgets Row -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="info-card text-center">
            <h6><i class="fas fa-ticket-alt"></i> Total Tickets</h6>
            <h3 class="text-primary">58</h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info-card text-center">
            <h6><i class="fas fa-bolt"></i> Urgent Issues</h6>
            <h3 class="text-danger">5</h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info-card text-center">
            <h6><i class="fas fa-check-circle"></i> Resolved</h6>
            <h3 class="text-success">47</h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info-card text-center">
            <h6><i class="fas fa-user-clock"></i> Avg. Response Time</h6>
            <h3 class="text-warning">2.1h</h3>
          </div>
        </div>
      </div>

       <div class="info-card mb-4">
          <ul class="nav nav-tabs" id="ticketTab" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                <i class="fas fa-info-circle me-1 text-primary"></i> Overview
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="errors-tab" data-bs-toggle="tab" data-bs-target="#errors" type="button" role="tab">
                <i class="fas fa-bug me-1 text-danger"></i> Errors & Solutions
              </button>
            </li>
          </ul>

          <div class="tab-content mt-4" id="ticketTabContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
              <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                  <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Ticket Overview</h5>
                  <p><strong>Current Ticket:</strong> <span class="text-muted">#1001 - Device not powering up</span></p>
                  <p><strong>Reported By:</strong> Alex</p>
                  <p><strong>Priority:</strong> <span class="badge bg-danger">High</span></p>
                  <p><strong>Status:</strong> <span class="badge bg-warning text-dark">Open</span></p>
                  <p><strong>Last Updated:</strong> 2 hours ago</p>

                  <div class="progress mt-3" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: 25%;"></div>
                  </div>
                  <small class="text-muted">25% completed</small>
                </div>

                <div class="col-md-6">
                  <canvas id="ticketChart" style="max-height: 250px;"></canvas>
                </div>
              </div>
            </div>

            <!-- Error Log Tab -->
            <div class="tab-pane fade" id="errors" role="tabpanel">
              <div class="accordion mt-3" id="errorAccordion">
                <!-- Error 1 -->
                <div class="accordion-item border-0 shadow-sm mb-3 rounded-4">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-light rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#error1">
                      <i class="fas fa-exclamation-circle text-danger me-2"></i> Power Failure on Start
                    </button>
                  </h2>
                  <div id="error1" class="accordion-collapse collapse">
                    <div class="accordion-body row g-3">
                      <div class="col-md-6">
                        <h6><i class="fas fa-bug me-2 text-danger"></i> Error Details</h6>
                        <ul class="list-unstyled">
                          <li><strong>Error Code:</strong> E-102</li>
                          <li><strong>Cause:</strong> Overload detected at boot time</li>
                          <li><strong>Category:</strong> Electrical</li>
                          <li><strong>Occurred:</strong> 3 times last week</li>
                        </ul>
                      </div>
                      <div class="col-md-6">
                        <h6><i class="fas fa-tools me-2 text-success"></i> Solution</h6>
                        <p>Reset the power switch, check voltage input. If persists, replace internal fuse.</p>
                        <a href="/manuals/product-x100-powerfix.pdf" class="btn btn-outline-primary btn-sm mt-2" target="_blank">
                          <i class="fas fa-file-pdf me-1"></i> View Manual
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Error 2 -->
                <div class="accordion-item border-0 shadow-sm mb-3 rounded-4">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-light rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#error2">
                      <i class="fas fa-exclamation-circle text-warning me-2"></i> Sensor Sync Issue
                    </button>
                  </h2>
                  <div id="error2" class="accordion-collapse collapse">
                    <div class="accordion-body row g-3">
                      <div class="col-md-6">
                        <h6><i class="fas fa-bug me-2 text-warning"></i> Error Details</h6>
                        <ul class="list-unstyled">
                          <li><strong>Error Code:</strong> S-204</li>
                          <li><strong>Cause:</strong> Sensor connection timeout</li>
                          <li><strong>Category:</strong> Communication</li>
                          <li><strong>Occurred:</strong> 5 times in 24h</li>
                        </ul>
                      </div>
                      <div class="col-md-6">
                        <h6><i class="fas fa-lightbulb me-2 text-success"></i> Solution</h6>
                        <p>Restart device and resync sensors. Update firmware if needed.</p>
                        <a href="/manuals/sensor-sync-guide.pdf" class="btn btn-outline-primary btn-sm mt-2" target="_blank">
                          <i class="fas fa-file-pdf me-1"></i> Download Manual
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Add more error items as needed -->
              </div>
            </div>
          </div>
        </div>




      <!-- Tabs -->
      <ul class="nav nav-tabs" id="taskTab" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" id="kanban-tab" data-bs-toggle="tab" data-bs-target="#kanban" type="button" role="tab">Kanban View</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">List View</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">Timeline View</button>
        </li>
      </ul>

      <div class="tab-content mt-4" id="taskTabContent">
        <div class="tab-pane fade show active" id="kanban" role="tabpanel">
            <div class="info-card">
              <div class="kanban-board">
                  <div class="kanban-column">
                    <h6 class="d-flex justify-content-between align-items-center">
                      Open <button class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i></button>
                    </h6>
                    <div class="task-card" draggable="true">
                      <div class="d-flex justify-content-between">
                        <strong>#1001</strong>
                        <span class="badge bg-danger">High</span>
                      </div>
                      <p class="mb-1">Device not powering up</p>
                      <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/30?img=12" class="rounded-circle me-2" alt="User">
                        <small>Alex</small>
                      </div>
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: 25%;"></div>
                      </div>
                      <div class="mt-2">
                        <textarea class="form-control form-control-sm mb-1" rows="1" placeholder="Add a comment..."></textarea>
                        <div class="text-end">
                          <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                          <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                          <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="kanban-column">
                    <h6 class="d-flex justify-content-between align-items-center">
                      In Progress <button class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i></button>
                    </h6>
                    <div class="task-card" draggable="true">
                      <div class="d-flex justify-content-between">
                        <strong>#1002</strong>
                        <span class="badge bg-info">Medium</span>
                      </div>
                      <p class="mb-1">Sensor syncing issue</p>
                      <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/30?img=5" class="rounded-circle me-2" alt="User">
                        <small>Maria</small>
                      </div>
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 60%;"></div>
                      </div>
                      <div class="mt-2">
                        <textarea class="form-control form-control-sm mb-1" rows="1" placeholder="Add a comment..."></textarea>
                        <div class="text-end">
                          <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                          <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                          <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="kanban-column">
                    <h6 class="d-flex justify-content-between align-items-center">
                      Done <button class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i></button>
                    </h6>
                    <div class="task-card" draggable="true">
                      <div class="d-flex justify-content-between">
                        <strong>#1003</strong>
                        <span class="badge bg-success">Low</span>
                      </div>
                      <p class="mb-1">Thermostat firmware updated</p>
                      <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/30?img=8" class="rounded-circle me-2" alt="User">
                        <small>John</small>
                      </div>
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 100%;"></div>
                      </div>
                      <div class="mt-2">
                        <textarea class="form-control form-control-sm mb-1" rows="1" placeholder="Add a comment..."></textarea>
                        <div class="text-end">
                          <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                          <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                          <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

        <div class="tab-pane fade" id="list" role="tabpanel">
          <div class="info-card mt-4">
            <h5>Ticket List</h5>
            <table class="table table-striped table-bordered">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Assigned</th>
                  <th>Status</th>
                  <th>Priority</th>
                  <th>Progress</th>
                  <th>Comment</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#1001</td>
                  <td>Device not powering up</td>
                  <td>Alex</td>
                  <td><span class="badge bg-danger">Open</span></td>
                  <td><span class="badge bg-danger">High</span></td>
                  <td>
                    <div class="progress" style="height: 6px;">
                      <div class="progress-bar bg-warning" style="width: 25%;"></div>
                    </div>
                  </td>
                  <td><input type="text" class="form-control form-control-sm" placeholder="Add a comment"></td>
                  <td>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </td>
                </tr>
                <tr>
                  <td>#1002</td>
                  <td>Sensor syncing issue</td>
                  <td>Maria</td>
                  <td><span class="badge bg-info text-dark">In Progress</span></td>
                  <td><span class="badge bg-info">Medium</span></td>
                  <td>
                    <div class="progress" style="height: 6px;">
                      <div class="progress-bar bg-info" style="width: 60%;"></div>
                    </div>
                  </td>
                  <td><input type="text" class="form-control form-control-sm" placeholder="Add a comment"></td>
                  <td>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </td>
                </tr>
                <tr>
                  <td>#1003</td>
                  <td>Thermostat firmware updated</td>
                  <td>John</td>
                  <td><span class="badge bg-success">Done</span></td>
                  <td><span class="badge bg-success">Low</span></td>
                  <td>
                    <div class="progress" style="height: 6px;">
                      <div class="progress-bar bg-success" style="width: 100%;"></div>
                    </div>
                  </td>
                  <td><input type="text" class="form-control form-control-sm" placeholder="Add a comment"></td>
                  <td>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>


        <div class="tab-pane fade" id="timeline" role="tabpanel">
          <div class="info-card">
            <h5>Activity Timeline</h5>
            <div class="timeline">
              <div class="timeline-entry">
                <h6>Task Created</h6>
                <p class="mb-1">Check device connection was created by <strong>System</strong>.</p>
                <div class="time">2025-04-18 09:00</div>
              </div>
              <div class="timeline-entry">
                <h6>Assigned</h6>
                <p class="mb-1">Assigned to <strong>Alex</strong>.</p>
                <div class="time">2025-04-19 10:00</div>
              </div>
              <div class="timeline-entry">
                <h6>Work in Progress</h6>
                <p class="mb-1">Alex started working on the issue.</p>
                <div class="time">2025-04-19 14:00</div>
              </div>
              <div class="timeline-entry">
                <h6>Completed</h6>
                <p class="mb-1">Task marked completed by <strong>Alex</strong>. Device confirmed operational.</p>
                <div class="time">2025-04-20 12:30</div>
              </div>
            </div>
          </div>
        </div>

      </div>

 
        <div class="row">
    <!-- Left Column: Service Report -->
        <div class="col-lg-6 mb-4">
          <div class="info-card h-100">
            <h5 class="mb-4">📋 Create a Service Report</h5>
            <form class="mb-5">
              <div class="mb-3">
                <select id="language-select" class="form-select">
                  <option value="de-DE">German</option>
                  <option value="fa-IR">Persian</option>
                  <option value="ar-SA">Arabic</option>
                  <option value="en-US">English</option>
                  <option value="tr-TR">Turkish</option> 
                </select>
              </div>
              <div class="mb-3">
                <input type="text" class="form-control form-control-lg" placeholder="Report Title" required>
              </div>
              <div class="mb-3 position-relative">
                <div id="quill-editor" style="height: 200px;"></div>
                <button type="button" id="mic-button" class="btn btn-sm btn-outline-secondary position-absolute end-0 bottom-0 me-2 mb-2">
                  <i class="fas fa-microphone"></i>
                </button>
              </div>
              <div class="text-end">
                <button class="btn btn-success btn-lg px-4"><i class="fas fa-paper-plane me-2"></i>Submit Report</button>
              </div>
            </form>

            <h5 class="mb-3">🧾 Service Reports</h5>
            <div class="card mb-4 shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h6 class="card-title text-primary"><i class="fas fa-clipboard-list me-2"></i> Not a Repair, but Reclamation</h6>
                <p class="card-text text-muted">Customer reported the issue as a defect, but upon inspection, it's a reclamation case.</p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-thumbs-up"></i> Like</button>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#comment1"><i class="fas fa-comment"></i> Comment</button>
                  </div>
                  <small class="text-muted fst-italic">Reported by Alex • 2 days ago</small>
                </div>
                <div class="collapse mt-3" id="comment1">
                  <textarea class="form-control" rows="2" placeholder="Write a comment..."></textarea>
                  <div class="text-end mt-2">
                    <button class="btn btn-sm btn-outline-success">Post Comment</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card mb-4 shadow-sm border-0 rounded-4">
              <div class="card-body">
                <h6 class="card-title text-primary"><i class="fas fa-wrench me-2"></i> PV Spring Replacement Needed</h6>
                <p class="card-text text-muted">Customer needs a new PV spring installed. Part worn out.</p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-thumbs-up"></i> Like</button>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#comment2"><i class="fas fa-comment"></i> Comment</button>
                  </div>
                  <small class="text-muted fst-italic">Reported by Maria • 5 hours ago</small>
                </div>
                <div class="collapse mt-3" id="comment2" style="position: relative;">
                  <textarea class="form-control comment-textarea" rows="2" placeholder="Write a comment..."></textarea>
                  <button type="button" class="btn btn-sm btn-outline-secondary position-absolute end-0 bottom-0 me-2 mb-2 mic-comment-btn"><i class="fas fa-microphone"></i></button>
                  <div class="text-end mt-2">
                    <button class="btn btn-sm btn-outline-success">Post Comment</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Comments -->
        <div class="col-lg-6 mb-4">
          <div class="info-card h-100 d-flex flex-column">
            <h5 class="mb-3"><i class="fas fa-comments me-2 text-primary"></i> Ticket Discussion</h5>
            <div class="chat-box flex-grow-1 overflow-auto mb-3" style="max-height: 500px;">
              <div class="d-flex align-items-start mb-3">
                <img src="https://i.pravatar.cc/40?img=3" class="rounded-circle me-2" alt="User">
                <div class="flex-grow-1">
                  <div class="bg-light p-2 rounded-3 shadow-sm">
                    <strong>Alex</strong> <small class="text-muted">• 2h ago</small>
                    <p class="mb-1">Device won’t power up, tried different socket.</p>
                    <div class="d-flex gap-2">
                      <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-reply"></i></button>
                      <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-start mb-3">
                <img src="https://i.pravatar.cc/40?img=5" class="rounded-circle me-2" alt="User">
                <div class="flex-grow-1">
                  <div class="bg-white p-2 rounded-3 shadow-sm">
                    <strong>Maria</strong> <small class="text-muted">• 1h ago</small>
                    <p class="mb-1">Check fuse and reset device. Might be a surge.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="input-group mt-auto">
              <input type="text" class="form-control comment-input" placeholder="Type your comment..." />
              <button class="btn btn-outline-secondary mic-comment-btn"><i class="fas fa-microphone"></i></button>
              <button class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
            </div>
          </div>
        </div>
      </div>



        <div class="info-card mb-4">
          <h5 class="mb-3"><i class="fas fa-image me-2 text-success"></i> Ticket Gallery</h5>
          <form action="/upload" class="dropzone border border-2 rounded-3 mb-3" id="ticketDropzone"></form>

          <div class="row g-3" id="galleryGrid">
            <div class="col-6 col-md-3">
              <div class="position-relative gallery-thumb">
                <img src="https://picsum.photos/200/200?random=1" class="img-fluid rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#imgModal1">
                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"><i class="fas fa-trash-alt"></i></button>
              </div>
            </div>
            <!-- Repeat col-6 col-md-3 for more -->
          </div>
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imgModal1" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">
              <div class="modal-body p-0">
                <img src="https://picsum.photos/800/600?random=1" class="img-fluid w-100 rounded">
              </div>
            </div>
          </div>
        </div>


    
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    document.querySelectorAll('.kanban-column').forEach(col => {
      new Sortable(col, {
        group: 'kanban',
        animation: 150
      });
    });
  </script>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  const quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Describe the issue in detail...',
    modules: {
      toolbar: [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline'],
        ['link', 'image'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        [{ align: [] }]
      ]
    }
  });

  const langSelect = document.getElementById('language-select');
  const micButton = document.getElementById('mic-button');
  let recognition;

  if ('webkitSpeechRecognition' in window) {
    recognition = new webkitSpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.lang = langSelect.value;

    langSelect.addEventListener('change', () => {
      recognition.lang = langSelect.value;
    });

    micButton.addEventListener('click', () => {
      recognition.start();
      micButton.classList.add('btn-danger');
    });

    recognition.onresult = (event) => {
      const transcript = event.results[0][0].transcript;
      const currentContent = quill.getText().trim();
      quill.setText(currentContent + ' ' + transcript);
      micButton.classList.remove('btn-danger');
    };

    recognition.onerror = () => {
      micButton.classList.remove('btn-danger');
    };
  } else {
    micButton.disabled = true;
    micButton.title = "Your browser doesn't support speech recognition.";
  }

  // Microphone for comment areas
  const commentMicButtons = document.querySelectorAll('.mic-comment-btn');
  const commentTextareas = document.querySelectorAll('.comment-textarea');

  commentMicButtons.forEach((btn, index) => {
    btn.addEventListener('click', () => {
      if (!recognition) return;
      recognition.lang = langSelect.value;
      recognition.start();
      btn.classList.add('btn-danger');

      recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        commentTextareas[index].value += (commentTextareas[index].value ? ' ' : '') + transcript;
        btn.classList.remove('btn-danger');
      };

      recognition.onerror = () => {
        btn.classList.remove('btn-danger');
      };
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  flatpickr("#mini-calendar", {
    inline: true,
    enable: [
      "2025-04-23",
      "2025-04-26",
      "2025-05-01"
    ],
    locale: "en",
    onChange: function(selectedDates, dateStr, instance) {
      console.log("Appointment date selected:", dateStr);
    }
  });
</script>
<script>
  const darkToggle = document.getElementById("darkModeToggle");
  darkToggle.addEventListener("change", () => {
    document.body.classList.toggle("bg-dark");
    document.body.classList.toggle("text-white");
    document.querySelectorAll('.info-card, .kanban-column, .task-card, .card').forEach(card => {
      card.classList.toggle("bg-dark");
      card.classList.toggle("text-white");
    });
  });
</script>
<script>
  const ctx = document.getElementById('ticketChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Resolved', 'Pending', 'In Progress'],
      datasets: [{
        label: 'Ticket Status',
        data: [47, 6, 5],
        backgroundColor: ['#198754', '#ffc107', '#0dcaf0'],
        borderWidth: 1
      }]
    },
    options: {
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 15,
            padding: 10
          }
        }
      }
    }
  });
</script>
<script>
  Dropzone.options.ticketDropzone = {
    maxFilesize: 5, // MB
    acceptedFiles: "image/*",
    success: function(file, response) {
      console.log("Uploaded", response);
    }
  };

  // Speech recognition for chat input
  const micBtns = document.querySelectorAll('.mic-comment-btn');
  micBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      if (!window.webkitSpeechRecognition) return;
      const recognition = new webkitSpeechRecognition();
      recognition.lang = 'en-US';
      recognition.start();
      recognition.onresult = (e) => {
        const input = btn.previousElementSibling;
        input.value += ' ' + e.results[0][0].transcript;
      };
    });
  });
</script>

</body>
</html>
