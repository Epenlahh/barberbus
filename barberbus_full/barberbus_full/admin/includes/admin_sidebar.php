<aside class="admin-sidebar">
  <div class="admin-logo">
    <div class="logo">BARBER<span class="accent">BUS</span></div>
    <span class="sub">Admin Panel</span>
  </div>

  <nav class="admin-nav">
    <div class="nav-group-label">Dashboard</div>
    <a href="dashboard.php" class="nav-item <?php if ($currentPanel == 'dashboard') echo 'active'; ?>"><i class="fas fa-th-large"></i> Overview</a>
    <a href="bookings.php" class="nav-item <?php if ($currentPanel == 'bookings') echo 'active'; ?>"><i class="fas fa-calendar-check"></i> Bookings <span class="nav-badge" id="pendingCount" style="display:none">0</span></a>

    <div class="nav-group-label" style="margin-top:0.5rem">Manage</div>
    <a href="users.php" class="nav-item <?php if ($currentPanel == 'users') echo 'active'; ?>"><i class="fas fa-users"></i> Users</a>
    <a href="barbers.php" class="nav-item <?php if ($currentPanel == 'barbers') echo 'active'; ?>"><i class="fas fa-user-tie"></i> Barbers</a>
    <a href="services.php" class="nav-item <?php if ($currentPanel == 'services') echo 'active'; ?>"><i class="fas fa-cut"></i> Services</a>
    
    <div class="nav-group-label" style="margin-top:0.5rem">Site Config</div>
    <a href="store.php" class="nav-item <?php if ($currentPanel == 'store') echo 'active'; ?>"><i class="fas fa-store"></i> Store Settings</a>
    <a href="pages.php" class="nav-item <?php if ($currentPanel == 'pages') echo 'active'; ?>"><i class="fas fa-file-alt"></i> Pages & Content</a>
    <a href="promotions.php" class="nav-item <?php if ($currentPanel == 'promotions') echo 'active'; ?>"><i class="fas fa-megaphone"></i> Promotions</a>
    
    <div class="nav-group-label" style="margin-top:0.5rem">Site</div>
    <a href="../index.php" class="nav-item"><i class="fas fa-external-link-alt"></i> View Website</a>
  </nav>

  <div class="admin-sidebar-footer">
    <div class="admin-user">
      <div class="admin-avatar" id="adminAvatar">A</div>
      <div>
        <div class="admin-name" id="adminName">Admin</div>
        <div class="admin-role">Administrator</div>
      </div>
    </div>
    <button class="btn-logout" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
  </div>
</aside>
