<?php
$pageTitle = "BarberBus – My Dashboard";
$currentPage = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<style>
    /* ── DASHBOARD LAYOUT ── */
    .dash-wrapper { display: flex; min-height: 100vh; background: var(--black); }

    /* ── SIDEBAR ── */
    .sidebar {
      width: 260px; min-height: 100vh;
      background: var(--dark);
      border-right: 1px solid rgba(201,168,76,0.12);
      display: flex; flex-direction: column;
      position: fixed; top: 0; left: 0; z-index: 100;
      transition: transform 0.3s ease;
    }
    .sidebar-logo {
      padding: 1.8rem 1.5rem 1.2rem;
      border-bottom: 1px solid rgba(201,168,76,0.1);
    }
    .sidebar-logo .logo-text { font-size: 1.4rem; }
    .sidebar-user {
      padding: 1.2rem 1.5rem;
      border-bottom: 1px solid rgba(201,168,76,0.08);
      display: flex; align-items: center; gap: 0.9rem;
    }
    .user-avatar {
      width: 42px; height: 42px; border-radius: 50%;
      background: linear-gradient(135deg, var(--gold), #8a5c0a);
      display: flex; align-items: center; justify-content: center;
      font-family: var(--font-display); font-size: 1.1rem; color: var(--black);
      flex-shrink: 0;
    }
    .user-info .user-name { font-size: 0.85rem; font-weight: 500; color: var(--white); }
    .user-info .user-role { font-size: 0.72rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.08em; }

    .sidebar-nav { padding: 1rem 0; flex: 1; }
    .nav-section-label {
      padding: 0.6rem 1.5rem 0.3rem;
      font-size: 0.65rem; letter-spacing: 0.15em; text-transform: uppercase;
      color: var(--gray); font-weight: 500;
    }
    .sidebar-link {
      display: flex; align-items: center; gap: 0.75rem;
      padding: 0.75rem 1.5rem;
      color: var(--light-gray); font-size: 0.85rem;
      transition: all 0.2s; cursor: pointer; border: none; background: none; width: 100%; text-align: left;
      text-decoration: none;
    }
    .sidebar-link i { width: 18px; text-align: center; font-size: 0.9rem; }
    .sidebar-link:hover { color: var(--white); background: rgba(201,168,76,0.06); }
    .sidebar-link.active { color: var(--gold); background: rgba(201,168,76,0.1); border-right: 2px solid var(--gold); }
    .sidebar-link .badge {
      margin-left: auto; background: var(--gold); color: var(--black);
      font-size: 0.65rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 20px;
    }

    .sidebar-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid rgba(201,168,76,0.08);
    }
    .btn-logout {
      width: 100%; padding: 0.7rem; background: transparent;
      border: 1px solid rgba(255,255,255,0.1); color: var(--light-gray);
      border-radius: 6px; cursor: pointer; font-size: 0.82rem;
      display: flex; align-items: center; justify-content: center; gap: 0.5rem;
      transition: all 0.2s;
    }
    .btn-logout:hover { border-color: #e05252; color: #e05252; }

    /* ── MAIN CONTENT ── */
    .dash-main {
      margin-left: 260px;
      flex: 1; padding: 2rem 2.5rem;
      min-height: 100vh;
    }
    .dash-topbar {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 2rem; padding-bottom: 1.2rem;
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .dash-topbar h1 { font-family: var(--font-serif); font-size: 1.8rem; }
    .topbar-right { display: flex; align-items: center; gap: 1rem; }
    .btn-book-now {
      padding: 0.6rem 1.4rem; background: var(--gold); color: var(--black);
      font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em;
      text-transform: uppercase; border-radius: 4px; text-decoration: none;
      transition: all 0.2s;
    }
    .btn-book-now:hover { background: #d4b05e; }

    /* ── TAB PANELS ── */
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .panel-fade-in { animation: fadeUp 0.3s ease; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:none; } }

    /* ── STAT CARDS ── */
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card {
      background: var(--dark2); border-radius: 12px;
      padding: 1.4rem; border: 1px solid rgba(201,168,76,0.1);
      position: relative; overflow: hidden;
    }
    .stat-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
      background: linear-gradient(90deg, var(--gold), transparent);
    }
    .stat-card .stat-icon { font-size: 1.5rem; margin-bottom: 0.8rem; color: var(--gold); opacity: 0.7; }
    .stat-card .stat-val { font-family: var(--font-display); font-size: 2rem; color: var(--white); }
    .stat-card .stat-lbl { font-size: 0.75rem; color: var(--light-gray); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 0.2rem; }

    /* ── BOOKING CARDS ── */
    .booking-list { display: flex; flex-direction: column; gap: 0.9rem; }
    .booking-card {
      background: var(--dark2); border-radius: 10px;
      border: 1px solid rgba(255,255,255,0.06);
      padding: 1.2rem 1.4rem;
      display: flex; align-items: center; justify-content: space-between;
      transition: border-color 0.2s;
    }
    .booking-card:hover { border-color: rgba(201,168,76,0.2); }
    .booking-info { flex: 1; }
    .booking-service { font-weight: 600; font-size: 0.95rem; color: var(--white); }
    .booking-meta { font-size: 0.78rem; color: var(--light-gray); margin-top: 0.3rem; }
    .booking-meta span { margin-right: 1rem; }
    .booking-meta i { color: var(--gold); margin-right: 0.3rem; }
    .booking-right { display: flex; align-items: center; gap: 1rem; }
    .booking-price { font-family: var(--font-display); font-size: 1.3rem; color: var(--gold); }
    .status-badge {
      padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.72rem;
      font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .status-pending   { background: rgba(255,193,7,0.15);  color: #ffc107; }
    .status-confirmed { background: rgba(40,167,69,0.15);  color: #40c878; }
    .status-completed { background: rgba(23,162,184,0.15); color: #17a2b8; }
    .status-cancelled { background: rgba(220,53,69,0.15);  color: #dc3545; }
    .btn-cancel-booking {
      background: transparent; border: 1px solid rgba(220,53,69,0.3);
      color: #dc3545; padding: 0.3rem 0.8rem; border-radius: 4px;
      font-size: 0.72rem; cursor: pointer; transition: all 0.2s;
    }
    .btn-cancel-booking:hover { background: rgba(220,53,69,0.1); }

    /* ── SECTION HEADERS ── */
    .section-title { font-family: var(--font-serif); font-size: 1.3rem; margin-bottom: 1.2rem; color: var(--white); }
    .section-title span { color: var(--gold); font-style: italic; }
    .card-box { background: var(--dark2); border-radius: 12px; padding: 1.5rem; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 1.5rem; }

    /* ── PROFILE FORM ── */
    .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group label { display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--light-gray); margin-bottom: 0.4rem; }
    .form-group input, .form-group select {
      width: 100%; padding: 0.7rem 1rem;
      background: var(--dark3); border: 1px solid rgba(255,255,255,0.08);
      color: var(--white); border-radius: 6px; font-size: 0.88rem;
      transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--gold); }
    .form-group.full { grid-column: 1 / -1; }
    .btn-save {
      padding: 0.7rem 1.8rem; background: var(--gold); color: var(--black);
      font-weight: 700; font-size: 0.82rem; letter-spacing: 0.08em;
      text-transform: uppercase; border: none; border-radius: 4px; cursor: pointer;
      transition: all 0.2s; margin-top: 0.5rem;
    }
    .btn-save:hover { background: #d4b05e; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray); }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; }
    .empty-state p { font-size: 0.9rem; }
    .empty-state a { color: var(--gold); }

    /* ── MOBILE TOGGLE ── */
    .sidebar-toggle {
      display: none; position: fixed; top: 1rem; left: 1rem; z-index: 200;
      background: var(--gold); color: var(--black); border: none; border-radius: 6px;
      width: 38px; height: 38px; cursor: pointer; font-size: 1rem;
    }
    .sidebar-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.7); z-index: 99;
    }

    /* ── LOADING ── */
    .loading-spinner { text-align: center; padding: 2rem; color: var(--gold); font-size: 1.5rem; }

    @media (max-width: 900px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .dash-main { margin-left: 0; padding: 1.5rem 1rem; padding-top: 4rem; }
      .sidebar-toggle { display: flex; align-items: center; justify-content: center; }
      .sidebar-overlay.open { display: block; }
      .profile-grid { grid-template-columns: 1fr; }
      .booking-card { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
      .booking-right { align-self: flex-end; }
    }
  </style>
</head>
<body>

  <!-- Mobile sidebar toggle -->
  <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
  <div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

  <div class="dash-wrapper">

    <!-- ── SIDEBAR ── -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <a href="index.php" class="logo-text">BARBER<span class="accent">BUS</span></a>
      </div>

      <div class="sidebar-user">
        <div class="user-avatar" id="avatarInitial">?</div>
        <div class="user-info">
          <div class="user-name" id="sidebarName">Loading...</div>
          <div class="user-role">Member</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <button class="sidebar-link active" onclick="showTab('overview')">
          <i class="fas fa-th-large"></i> Overview
        </button>
        <button class="sidebar-link" onclick="showTab('bookings')">
          <i class="fas fa-calendar-check"></i> My Bookings
          <span class="badge" id="pendingBadge" style="display:none">0</span>
        </button>
        <button class="sidebar-link" onclick="showTab('new-booking')">
          <i class="fas fa-plus-circle"></i> New Booking
        </button>

        <div class="nav-section-label" style="margin-top:0.5rem">Account</div>
        <button class="sidebar-link" onclick="showTab('profile')">
          <i class="fas fa-user"></i> My Profile
        </button>
        <button class="sidebar-link" onclick="showTab('security')">
          <i class="fas fa-shield-alt"></i> Security
        </button>
        <a href="index.php" class="sidebar-link">
          <i class="fas fa-home"></i> Back to Site
        </a>
      </nav>

      <div class="sidebar-footer">
        <button class="btn-logout" onclick="logout()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </div>
    </aside>

    <!-- ── MAIN ── -->
    <main class="dash-main">

      <!-- ── OVERVIEW ── -->
      <div id="tab-overview" class="tab-panel active panel-fade-in">
        <div class="dash-topbar">
          <div>
            <h1>Welcome back, <span id="welcomeName">...</span></h1>
            <p style="color:var(--light-gray);font-size:0.85rem;margin-top:0.2rem">Here's your grooming summary</p>
          </div>
          <div class="topbar-right">
            <a href="booking.php" class="btn-book-now"><i class="fas fa-plus"></i> Book Now</a>
          </div>
        </div>

        <div class="stat-grid">
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-val" id="statTotal">–</div>
            <div class="stat-lbl">Total Bookings</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-val" id="statPending">–</div>
            <div class="stat-lbl">Pending</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-val" id="statCompleted">–</div>
            <div class="stat-lbl">Completed</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="stat-val" id="statSpent">–</div>
            <div class="stat-lbl">Total Spent (RM)</div>
          </div>
        </div>

        <h3 class="section-title">Recent <span>Bookings</span></h3>
        <div id="recentBookingsList" class="booking-list">
          <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
      </div>

      <!-- ── MY BOOKINGS ── -->
      <div id="tab-bookings" class="tab-panel">
        <div class="dash-topbar">
          <h1>My <span style="color:var(--gold);font-style:italic">Bookings</span></h1>
          <a href="booking.php" class="btn-book-now"><i class="fas fa-plus"></i> New Booking</a>
        </div>

        <div class="card-box" style="padding:0.8rem 1.2rem;margin-bottom:1.2rem;display:flex;gap:0.8rem;flex-wrap:wrap">
          <button class="filter-btn active" onclick="filterBookings('all',this)" style="padding:0.4rem 1rem;background:var(--gold);color:var(--black);border:none;border-radius:4px;font-size:0.78rem;font-weight:700;cursor:pointer;letter-spacing:0.06em;text-transform:uppercase">All</button>
          <button class="filter-btn" onclick="filterBookings('pending',this)"   style="padding:0.4rem 1rem;background:transparent;border:1px solid rgba(255,255,255,0.1);color:var(--light-gray);border-radius:4px;font-size:0.78rem;cursor:pointer;text-transform:uppercase;letter-spacing:0.06em">Pending</button>
          <button class="filter-btn" onclick="filterBookings('confirmed',this)" style="padding:0.4rem 1rem;background:transparent;border:1px solid rgba(255,255,255,0.1);color:var(--light-gray);border-radius:4px;font-size:0.78rem;cursor:pointer;text-transform:uppercase;letter-spacing:0.06em">Confirmed</button>
          <button class="filter-btn" onclick="filterBookings('completed',this)" style="padding:0.4rem 1rem;background:transparent;border:1px solid rgba(255,255,255,0.1);color:var(--light-gray);border-radius:4px;font-size:0.78rem;cursor:pointer;text-transform:uppercase;letter-spacing:0.06em">Completed</button>
          <button class="filter-btn" onclick="filterBookings('cancelled',this)" style="padding:0.4rem 1rem;background:transparent;border:1px solid rgba(255,255,255,0.1);color:var(--light-gray);border-radius:4px;font-size:0.78rem;cursor:pointer;text-transform:uppercase;letter-spacing:0.06em">Cancelled</button>
        </div>

        <div id="allBookingsList" class="booking-list">
          <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
      </div>

      <!-- ── NEW BOOKING ── -->
      <div id="tab-new-booking" class="tab-panel">
        <div class="dash-topbar">
          <h1>New <span style="color:var(--gold);font-style:italic">Booking</span></h1>
        </div>
        <div class="card-box">
          <p style="color:var(--light-gray);margin-bottom:1.5rem;font-size:0.9rem">Fill in the form below to schedule your appointment.</p>

          <div class="profile-grid">
            <div class="form-group">
              <label>Service <span style="color:var(--gold)">*</span></label>
              <select id="nb-service">
                <option value="">Loading services...</option>
              </select>
            </div>
            <div class="form-group">
              <label>Barber</label>
              <select id="nb-barber">
                <option value="">Any Available</option>
              </select>
            </div>
            <div class="form-group">
              <label>Date <span style="color:var(--gold)">*</span></label>
              <input type="date" id="nb-date" min=""/>
            </div>
            <div class="form-group">
              <label>Time <span style="color:var(--gold)">*</span></label>
              <select id="nb-time">
                <option value="10:00">10:00 AM</option>
                <option value="10:30">10:30 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="11:30">11:30 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="12:30">12:30 PM</option>
                <option value="13:00">1:00 PM</option>
                <option value="13:30">1:30 PM</option>
                <option value="14:00">2:00 PM</option>
                <option value="14:30">2:30 PM</option>
                <option value="15:00">3:00 PM</option>
                <option value="15:30">3:30 PM</option>
                <option value="16:00">4:00 PM</option>
                <option value="16:30">4:30 PM</option>
                <option value="17:00">5:00 PM</option>
                <option value="17:30">5:30 PM</option>
                <option value="18:00">6:00 PM</option>
                <option value="18:30">6:30 PM</option>
                <option value="19:00">7:00 PM</option>
                <option value="19:30">7:30 PM</option>
                <option value="20:00">8:00 PM</option>
                <option value="20:30">8:30 PM</option>
              </select>
            </div>
            <div class="form-group">
              <label>Payment Method</label>
              <select id="nb-pay">
                <option value="cash">Cash</option>
                <option value="online_banking">Online Banking</option>
                <option value="ewallet">E-Wallet (Touch n Go / GrabPay)</option>
                <option value="card">Credit/Debit Card</option>
              </select>
            </div>
            <div class="form-group full">
              <label>Notes (optional)</label>
              <input type="text" id="nb-notes" placeholder="Any special requests or preferences..."/>
            </div>
          </div>

          <div style="margin-top:1rem;padding:1rem;background:var(--dark3);border-radius:8px;display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-size:0.75rem;color:var(--light-gray);text-transform:uppercase;letter-spacing:0.06em">Estimated Price</div>
              <div style="font-family:var(--font-display);font-size:1.6rem;color:var(--gold)" id="nb-price">RM 0</div>
            </div>
            <button class="btn-save" onclick="submitBooking()">
              <i class="fas fa-calendar-plus"></i> Confirm Booking
            </button>
          </div>
        </div>
      </div>

      <!-- ── PROFILE ── -->
      <div id="tab-profile" class="tab-panel">
        <div class="dash-topbar">
          <h1>My <span style="color:var(--gold);font-style:italic">Profile</span></h1>
        </div>
        <div class="card-box">
          <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid rgba(255,255,255,0.06)">
            <div class="user-avatar" id="profileAvatar" style="width:64px;height:64px;font-size:1.8rem">?</div>
            <div>
              <div style="font-weight:600;font-size:1.1rem" id="profileNameDisplay">–</div>
              <div style="color:var(--light-gray);font-size:0.82rem" id="profileEmailDisplay">–</div>
              <div style="color:var(--gold);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.08em;margin-top:0.2rem">Member</div>
            </div>
          </div>

          <h4 style="margin-bottom:1rem;color:var(--light-gray)">Edit Information</h4>
          <div class="profile-grid">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" id="prof-name" placeholder="Your name"/>
            </div>
            <div class="form-group">
              <label>Phone Number</label>
              <input type="tel" id="prof-phone" placeholder="+60 12-345 6789"/>
            </div>
            <div class="form-group full">
              <label>Email Address</label>
              <input type="email" id="prof-email" placeholder="your@email.com" disabled style="opacity:0.5;cursor:not-allowed"/>
            </div>
          </div>
          <button class="btn-save" onclick="saveProfile()">Save Changes</button>
        </div>
      </div>

      <!-- ── SECURITY ── -->
      <div id="tab-security" class="tab-panel">
        <div class="dash-topbar">
          <h1>Security <span style="color:var(--gold);font-style:italic">Settings</span></h1>
        </div>
        <div class="card-box">
          <h4 style="margin-bottom:1.2rem;color:var(--light-gray)">Change Password</h4>
          <div style="max-width:400px">
            <div class="form-group" style="margin-bottom:0.9rem">
              <label>Current Password</label>
              <input type="password" id="sec-current" placeholder="••••••••"/>
            </div>
            <div class="form-group" style="margin-bottom:0.9rem">
              <label>New Password</label>
              <input type="password" id="sec-new" placeholder="Min 6 characters"/>
            </div>
            <div class="form-group" style="margin-bottom:1rem">
              <label>Confirm New Password</label>
              <input type="password" id="sec-confirm" placeholder="Repeat new password"/>
            </div>
            <button class="btn-save" onclick="changePassword()">Update Password</button>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast"></div>

  <script src="js/main.js"></script>
  <script src="js/dashboard.js"></script>
</body>
</html>
