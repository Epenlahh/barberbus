<?php
header('Location: dashboard.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<body>

<div class="admin-wrapper">

  <!-- ── SIDEBAR ── -->
  <?php include 'includes/admin_sidebar.php'; ?>

  <!-- ── MAIN ── -->
  <main class="admin-main">

    <!-- ════════════════════════ DASHBOARD ════════════════════════ -->
    <div id="panel-dashboard" class="panel active">
      <div class="topbar">
        <div>
          <h1>Dashboard <span style="color:var(--gold);font-style:italic">Overview</span></h1>
          <p style="color:var(--light-gray);font-size:0.82rem;margin-top:0.2rem">Real-time BarberBus statistics</p>
        </div>
        <div class="topbar-actions">
          <span style="font-size:0.78rem;color:var(--light-gray)" id="lastUpdated"></span>
          <button class="btn btn-outline" onclick="loadDashboard()"><i class="fas fa-sync-alt"></i> Refresh</button>
        </div>
      </div>

      <div class="kpi-grid">
        <div class="kpi-card gold"><div class="kpi-icon"><i class="fas fa-receipt"></i></div><div class="kpi-val" id="kpiRevenue">–</div><div class="kpi-lbl">Total Revenue (RM)</div></div>
        <div class="kpi-card blue"><div class="kpi-icon"><i class="fas fa-calendar-check"></i></div><div class="kpi-val" id="kpiBookings">–</div><div class="kpi-lbl">Total Bookings</div></div>
        <div class="kpi-card green"><div class="kpi-icon"><i class="fas fa-users"></i></div><div class="kpi-val" id="kpiUsers">–</div><div class="kpi-lbl">Registered Users</div></div>
        <div class="kpi-card orange"><div class="kpi-icon"><i class="fas fa-calendar-day"></i></div><div class="kpi-val" id="kpiToday">–</div><div class="kpi-lbl">Today's Bookings</div></div>
        <div class="kpi-card red"><div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div><div class="kpi-val" id="kpiPending">–</div><div class="kpi-lbl">Pending Approval</div></div>
        <div class="kpi-card gold"><div class="kpi-icon"><i class="fas fa-user-tie"></i></div><div class="kpi-val" id="kpiBarbers">–</div><div class="kpi-lbl">Active Barbers</div></div>
      </div>

      <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:1.4rem">
        <!-- Revenue Chart -->
        <div class="box">
          <div class="box-header">
            <div class="box-title">Monthly Revenue</div>
            <span style="font-size:0.75rem;color:var(--light-gray)">Last 6 months</span>
          </div>
          <div class="chart-wrap" id="revenueChart">
            <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
          </div>
        </div>

        <!-- Popular Services -->
        <div class="box">
          <div class="box-header"><div class="box-title">Top Services</div></div>
          <div id="topServices"><div class="loading"><i class="fas fa-spinner fa-spin"></i></div></div>
        </div>
      </div>

      <!-- Recent Bookings -->
      <div class="box">
        <div class="box-header">
          <div class="box-title">Recent Bookings</div>
          <button class="btn btn-outline btn-sm" onclick="showPanel('bookings')">View All</button>
        </div>
        <div style="overflow-x:auto">
          <table class="data-table">
            <thead><tr><th>#</th><th>Client</th><th>Service</th><th>Barber</th><th>Date & Time</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
            <tbody id="recentBookingsTable"><tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ════════════════════════ BOOKINGS ════════════════════════ -->
    <div id="panel-bookings" class="panel">
      <div class="topbar">
        <h1>All <span style="color:var(--gold);font-style:italic">Bookings</span></h1>
        <div class="topbar-actions">
          <div class="search-box"><i class="fas fa-search"></i><input type="text" id="bookingSearch" placeholder="Search..." oninput="filterTable('bookingSearch','bookingsTable')"/></div>
          <select onchange="filterBookingStatus(this.value)" style="background:var(--dark3);border:1px solid rgba(255,255,255,0.08);color:var(--white);padding:0.5rem 0.8rem;border-radius:6px;font-size:0.82rem">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Client</th><th>Contacts</th><th>Service</th><th>Barber</th><th>Date & Time</th><th>Amount</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="bookingsTable"><tr><td colspan="10" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ════════════════════════ USERS ════════════════════════ -->
    <div id="panel-users" class="panel">
      <div class="topbar">
        <h1>User <span style="color:var(--gold);font-style:italic">Management</span></h1>
        <div class="topbar-actions">
          <div class="search-box"><i class="fas fa-search"></i><input type="text" id="userSearch" placeholder="Search users..." oninput="filterTable('userSearch','usersTable')"/></div>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Bookings</th><th>Actions</th></tr></thead>
          <tbody id="usersTable"><tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ════════════════════════ BARBERS ════════════════════════ -->
    <div id="panel-barbers" class="panel">
      <div class="topbar">
        <h1>Barber <span style="color:var(--gold);font-style:italic">Profiles</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openBarberModal()"><i class="fas fa-plus"></i> Add Barber</button>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Name</th><th>Specialty</th><th>Experience</th><th>Rating</th><th>Officer Account</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="barbersTable"><tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ════════════════════════ SERVICES ════════════════════════ -->
    <div id="panel-services" class="panel">
      <div class="topbar">
        <h1>Service <span style="color:var(--gold);font-style:italic">List</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openServiceModal()"><i class="fas fa-plus"></i> Add Service</button>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Service</th><th>Category</th><th>Duration</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="servicesTable"><tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ════════════════════════ STORE SETTINGS ════════════════════════ -->
    <div id="panel-store" class="panel">
      <div class="topbar">
        <h1>Store <span style="color:var(--gold);font-style:italic">Configuration</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openStoreModal()"><i class="fas fa-edit"></i> Edit Store Info</button>
        </div>
      </div>
      <div class="box" id="storeDisplay">
        <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
      </div>
    </div>

    <!-- ════════════════════════ PAGES ════════════════════════ -->
    <div id="panel-pages" class="panel">
      <div class="topbar">
        <h1>Pages & <span style="color:var(--gold);font-style:italic">Content</span></h1>
      </div>
      <div class="box">
        <div class="tabs">
          <button class="tab-btn active" onclick="switchContentPage('dashboard', this)">Dashboard</button>
          <button class="tab-btn" onclick="switchContentPage('booking', this)">Booking Page</button>
          <button class="tab-btn" onclick="switchContentPage('fashion', this)">Fashion Page</button>
          <button class="tab-btn" onclick="switchContentPage('tryon', this)">Try-on Page</button>
        </div>
        <div id="pageContent" style="padding-top:1.5rem">
          <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
      </div>
    </div>

    <!-- ════════════════════════ PROMOTIONS ════════════════════════ -->
    <div id="panel-promotions" class="panel">
      <div class="topbar">
        <h1>Site <span style="color:var(--gold);font-style:italic">Promotions</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openPromotionModal()"><i class="fas fa-plus"></i> Add Promotion</button>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Title</th><th>Description</th><th>Status</th><th>Starts</th><th>Ends</th><th>Actions</th></tr></thead>
          <tbody id="promotionsTable"><tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ── BARBER MODAL ── -->
<div class="modal-overlay" id="barberModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="barberModalTitle">Add Barber</div>
      <button class="modal-close" onclick="closeModal('barberModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="b-id"/>
      <div class="form-group"><label>Full Name *</label><input type="text" id="b-name" placeholder="Barber name"/></div>
      <div class="form-group"><label>Specialty</label><input type="text" id="b-specialty" placeholder="e.g. Fade &amp; Taper"/></div>
      <div class="form-group"><label>Experience (years)</label><input type="number" id="b-exp" min="0" value="0"/></div>
      <div class="form-group"><label>Rating (1-5)</label><input type="number" id="b-rating" min="1" max="5" step="0.1" value="5.0"/></div>
      <div class="form-group"><label>Status</label>
        <select id="b-active"><option value="1">Active</option><option value="0">Inactive</option></select>
      </div>
      <div class="form-group full"><label>Bio</label><textarea id="b-bio" placeholder="Short biography..."></textarea></div>
    </div>

    <div class="officer-section">
      <div class="officer-section-title"><i class="fas fa-user-shield"></i>&nbsp; Officer Dashboard Access</div>
      <p style="font-size:0.78rem;color:var(--light-gray);margin-bottom:1rem">Set login credentials so this barber can access the Officer Dashboard. Leave blank to skip.</p>
      <div class="form-grid">
        <div class="form-group"><label>Officer Login Email</label><input type="email" id="b-officer-email" placeholder="barber@barberbus.com"/></div>
        <div class="form-group"><label>Password <span id="b-pass-hint" style="color:var(--gray);font-style:italic;display:none">(leave blank to keep)</span></label><input type="password" id="b-officer-pass" placeholder="Min. 6 characters"/></div>
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('barberModal')">Cancel</button>
      <button class="btn btn-gold" onclick="saveBarber()"><i class="fas fa-save"></i> Save Barber</button>
    </div>
  </div>
</div>

<!-- ── SERVICE MODAL ── -->
<div class="modal-overlay" id="serviceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="serviceModalTitle">Add Service</div>
      <button class="modal-close" onclick="closeModal('serviceModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="s-id"/>
      <div class="form-group full"><label>Service Name *</label><input type="text" id="s-name" placeholder="e.g. Classic Haircut"/></div>
      <div class="form-group"><label>Price (RM) *</label><input type="number" id="s-price" min="0" step="0.50" placeholder="25.00"/></div>
      <div class="form-group"><label>Duration (mins) *</label><input type="number" id="s-duration" min="5" step="5" placeholder="30"/></div>
      <div class="form-group"><label>Category</label>
        <select id="s-category">
          <option value="haircut">Haircut</option>
          <option value="shave">Shave & Beard</option>
          <option value="treatment">Treatment</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group"><label>Status</label>
        <select id="s-active"><option value="1">Active</option><option value="0">Inactive</option></select>
      </div>
      <div class="form-group full"><label>Description</label><textarea id="s-desc" placeholder="Service details..."></textarea></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('serviceModal')">Cancel</button>
      <button class="btn btn-gold" onclick="saveService()"><i class="fas fa-save"></i> Save Service</button>
    </div>
  </div>
</div>

<!-- ── STORE MODAL ── -->
<div class="modal-overlay" id="storeModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Store Settings</div>
      <button class="modal-close" onclick="closeModal('storeModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <div class="form-group full"><label>Store Name</label><input type="text" id="store-name"/></div>
      <div class="form-group full"><label>Description</label><textarea id="store-desc"></textarea></div>
      <div class="form-group"><label>Store Status</label>
        <select id="store-open">
          <option value="1">Open</option>
          <option value="0">Closed</option>
        </select>
      </div>
      <div class="form-group"><label>Profile Image URL</label><input type="text" id="store-image"/></div>
      <div class="form-group"><label>Business Hours</label><input type="text" id="store-hours" placeholder="e.g. 10AM - 10PM"/></div>
      <div class="form-group"><label>Location</label><input type="text" id="store-location"/></div>
      <div class="form-group"><label>Phone</label><input type="text" id="store-phone"/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('storeModal')">Cancel</button>
      <button class="btn btn-gold" onclick="saveStore()"><i class="fas fa-save"></i> Save Settings</button>
    </div>
  </div>
</div>

<!-- ── PAGE CONTENT MODAL ── -->
<div class="modal-overlay" id="pageModal">
  <div class="modal" style="max-width:700px">
    <div class="modal-header">
      <div class="modal-title" id="pageModalTitle">Edit Page</div>
      <button class="modal-close" onclick="closeModal('pageModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="page-name"/>
      <div class="form-group full"><label>Page Title</label><input type="text" id="page-title"/></div>
      <div class="form-group full"><label>Hero Text</label><input type="text" id="page-hero"/></div>
      <div class="form-group full"><label>Main Content</label><textarea id="page-content" style="height:200px"></textarea></div>
      <div class="form-group full"><label>Footer Note</label><input type="text" id="page-footer"/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('pageModal')">Cancel</button>
      <button class="btn btn-gold" onclick="savePageContent()"><i class="fas fa-save"></i> Save Content</button>
    </div>
  </div>
</div>

<!-- ── PROMOTION MODAL ── -->
<div class="modal-overlay" id="promotionModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="promotionModalTitle">Add Promotion</div>
      <button class="modal-close" onclick="closeModal('promotionModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="promo-id"/>
      <div class="form-group full"><label>Promotion Title *</label><input type="text" id="promo-title" placeholder="e.g. 50% Off First Cut"/></div>
      <div class="form-group full"><label>Description</label><textarea id="promo-desc" placeholder="Promotion details..."></textarea></div>
      <div class="form-group full"><label>Image URL</label><input type="text" id="promo-image" placeholder="https://..."/></div>
      <div class="form-group"><label>Start Date</label><input type="date" id="promo-start"/></div>
      <div class="form-group"><label>End Date</label><input type="date" id="promo-end"/></div>
      <div class="form-group"><label>Status</label>
        <select id="promo-active">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div class="form-group full"><label>Promo Link (optional)</label><input type="text" id="promo-link" placeholder="https://..."/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('promotionModal')">Cancel</button>
      <button class="btn btn-gold" onclick="savePromotion()"><i class="fas fa-save"></i> Save Promotion</button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-dashboard.js"></script>
</body>
</html>
