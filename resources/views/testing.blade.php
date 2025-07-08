<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Attentive Campaign Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background: #fff;
      border-right: 1px solid #dee2e6;
      padding: 1rem;
    }
    .sidebar h6 {
      margin-top: 1.5rem;
      font-size: 0.75rem;
      color: #6c757d;
      text-transform: uppercase;
    }
    .sidebar .nav-link {
      color: #000;
      font-size: 0.9rem;
    }
    .sidebar .nav-link.active {
      font-weight: bold;
    }
    .badge {
      background: #ffc107;
      color: #000;
      font-size: 0.75rem;
    }
    .topbar {
      border-bottom: 1px solid #dee2e6;
      padding: 1rem 2rem;
    }
    .status-pill {
      background: #d9f0c3;
      color: #4b8b0c;
      border-radius: 20px;
      padding: 2px 10px;
      font-size: 0.75rem;
    }
    .campaign-card {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
    }
    .filters button {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-2 sidebar">
      <div class="mb-4">
        <i class="bi bi-house me-2"></i> Home
      </div>
      <h6>GROWTH</h6>
      <a href="#" class="nav-link"><i class="bi bi-plus-circle me-2"></i> Sign-up Units</a>
      <h6>AUDIENCE</h6>
      <a href="#" class="nav-link"><i class="bi bi-people me-2"></i> Subscribers</a>
      <a href="#" class="nav-link"><i class="bi bi-segment me-2"></i> Segments</a>
      <h6>MESSAGING</h6>
      <a href="#" class="nav-link"><i class="bi bi-arrow-repeat me-2"></i> Journeys</a>
      <a href="#" class="nav-link active">
        <i class="bi bi-bullseye me-2"></i> Campaigns
      </a>
      <a href="#" class="nav-link">
        <i class="bi bi-chat-dots me-2"></i> Conversations <span class="badge">99+</span>
      </a>
      <h6>ANALYTICS</h6>
      <a href="#" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
      <a href="#" class="nav-link"><i class="bi bi-bar-chart-line me-2"></i> Reports</a>
      <h6>SETUP</h6>
      <a href="#" class="nav-link"><i class="bi bi-shop me-2"></i> Marketplace</a>
      <a href="#" class="nav-link"><i class="bi bi-tags me-2"></i> Offers</a>
      <a href="#" class="nav-link"><i class="bi bi-palette me-2"></i> Brand Kit</a>
      <a href="#" class="nav-link"><i class="bi bi-envelope me-2"></i> Email Templates</a>
      <a href="#" class="nav-link"><i class="bi bi-gear me-2"></i> Settings</a>
    </div>

    <!-- Main Content -->
    <div class="col-10">
      <!-- Topbar -->
      <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
          <h4 class="mb-0">Campaigns</h4>
          <button class="btn btn-warning">+ Create campaign</button>
        </div>
        <div>
          <i class="bi bi-question-circle me-3"></i>
          <i class="bi bi-bell me-3"></i>
          <i class="bi bi-chat-left-text me-3"></i>
          HIREV Sports <i class="bi bi-caret-down-fill"></i>
        </div>
      </div>

      <!-- Filters -->
      <div class="p-4">
        <div class="row mb-3">
          <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Search campaigns"/>
          </div>
          <div class="col-md-2">
            <select class="form-select">
              <option>All statuses</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-select">
              <option>All time</option>
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">More filters (2)</button>
          </div>
          <div class="col-md-2 filters text-end">
            <button class="btn btn-outline-secondary">%</button>
            <button class="btn btn-outline-secondary">#</button>
          </div>
        </div>

        <!-- Campaigns -->
        <div id="campaignList">
          <!-- Example Campaign -->
          <div class="campaign-card">
            <div class="d-flex justify-content-between">
              <div>
                <h6>HRS | 4th July</h6>
                <div>Sent Sat, Jul 5, 2025 at 12:00 PM CDT</div>
                <div>Sent to <a href="#">Purchased in last 30 days</a>, All</div>
                <button class="btn btn-outline-secondary btn-sm mt-1">
                  <i class="bi bi-play-circle"></i> Smart Sending On
                </button>
              </div>
              <div class="text-end">
                <span class="status-pill">Delivered</span>
                <div>Delivered: 25,463</div>
                <div>Open Rate: 41.2%</div>
                <div>Click Rate: 1.2%</div>
                <div>CVR: 1.7%</div>
                <div>Unsubs: 0.5%</div>
                <div>Revenue: $2,141</div>
              </div>
            </div>
          </div>

          <div class="campaign-card">
            <div class="d-flex justify-content-between">
              <div>
                <h6>DAM | 10% off discount</h6>
                <div>Sent Wed, May 7, 2025 at 12:00 PM CDT</div>
                <div>Sent to <a href="#">DAM NEW USERS</a></div>
                <button class="btn btn-outline-secondary btn-sm mt-1">
                  <i class="bi bi-play-circle"></i> Smart Sending On
                </button>
              </div>
              <div class="text-end">
                <span class="status-pill">Delivered</span>
                <div>Delivered: 25,162</div>
                <div>Open Rate: 39.6%</div>
                <div>Click Rate: 1.5%</div>
                <div>CVR: 3.6%</div>
                <div>Unsubs: 0.0%</div>
                <div>Revenue: $5,406</div>
              </div>
            </div>
          </div>

          <!-- Add more campaign-card divs -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  // Example: filter campaigns by search input
  $('#searchInput').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#campaignList .campaign-card').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });
</script>
</body>
</html>
