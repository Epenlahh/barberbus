<div class="topbar">
  <div class="topbar-title">
    <span id="panelTitle"><?php echo htmlspecialchars($panelTitle ?? 'Live Overview'); ?></span>
    <span class="page-sub" id="panelSub"><?php echo htmlspecialchars($panelSub ?? 'Real-time operations dashboard'); ?></span>
  </div>

  <div class="topbar-clock clock-display">00:00:00</div>
  <div class="topbar-date date-display"></div>

  <button class="topbar-refresh" id="refreshBtn" onclick="manualRefresh()" title="Refresh data">
    <i class="fas fa-arrows-rotate"></i>
  </button>

  <div style="position:relative;">
    <div class="topbar-notif" id="notifBtn" onclick="toggleNotifPanel()" title="Notifications">
      <i class="fas fa-bell"></i>
      <div class="notif-count" id="notifCount" style="display:none">0</div>
    </div>
    <div class="notif-panel" id="notifPanel">
      <div class="notif-header">
        <span>Recent Activity</span>
        <span style="color:var(--muted);font-size:0.65rem">Last 30 seconds</span>
      </div>
      <div id="notifList"><div class="notif-empty">No new notifications</div></div>
    </div>
  </div>

  <button class="btn-walkin" onclick="openWalkIn()">
    <i class="fas fa-plus"></i> Walk-In
  </button>
</div>
