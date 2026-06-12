<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">B</div>
    <div class="brand-name">BARBERBUS<span class="sub">Officer Portal</span></div>
  </div>

  <div class="sidebar-officer">
    <div class="officer-avatar" id="officerAvatar">O</div>
    <div class="officer-info">
      <div class="officer-name" id="officerName">Officer</div>
      <div class="officer-role" id="officerRole">Staff</div>
    </div>
    <div class="live-dot" title="Live – connected"></div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Operations</div>
    <a href="dashboard.php" class="nav-link <?php if ($currentPanel == 'overview') echo 'active'; ?>">
      <i class="nav-icon fas fa-th-large"></i>
      <span class="nav-label">Overview</span>
    </a>
    <a href="queue.php" class="nav-link <?php if ($currentPanel == 'queue') echo 'active'; ?>">
      <i class="nav-icon fas fa-list-ol"></i>
      <span class="nav-label">Live Queue</span>
      <span class="nav-badge" id="queueBadge" style="display:none">0</span>
    </a>
    <a href="schedule.php" class="nav-link <?php if ($currentPanel == 'schedule') echo 'active'; ?>">
      <i class="nav-icon fas fa-calendar-day"></i>
      <span class="nav-label">Today's Schedule</span>
    </a>

    <div class="nav-section">Customers</div>
    <a href="customers.php" class="nav-link <?php if ($currentPanel == 'customers') echo 'active'; ?>">
      <i class="nav-icon fas fa-user-magnifying-glass"></i>
      <span class="nav-label">Customer Lookup</span>
    </a>
    <a href="users.php" class="nav-link <?php if ($currentPanel == 'users') echo 'active'; ?>">
      <i class="nav-icon fas fa-users"></i>
      <span class="nav-label">User Accounts</span>
    </a>
    <a href="walkins.php" class="nav-link <?php if ($currentPanel == 'walkins') echo 'active'; ?>">
      <i class="nav-icon fas fa-person-walking-arrow-right"></i>
      <span class="nav-label">Walk-In Entry</span>
    </a>

    <div class="nav-section">Links</div>
    <a href="../index.php" class="nav-link" target="_blank">
      <i class="nav-icon fas fa-globe"></i>
      <span class="nav-label">Customer Site</span>
    </a>
    <a href="../admin/index.php" class="nav-link" target="_blank">
      <i class="nav-icon fas fa-shield-halved"></i>
      <span class="nav-label">Admin Panel</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <button class="btn-logout-sm" onclick="logout()">
      <i class="fas fa-sign-out-alt"></i>
      <span>Sign out</span>
    </button>
    <button class="btn-collapse" onclick="toggleSidebar()" title="Collapse sidebar">
      <i class="fas fa-chevron-left" id="collapseIcon"></i>
    </button>
  </div>
</aside>
