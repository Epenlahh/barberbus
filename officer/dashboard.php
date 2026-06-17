<?php
$pageTitle = 'BarberBus – Officer Dashboard';
$currentPanel = 'overview';
$panelTitle = 'Live Overview';
$panelSub = 'Real-time operations dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/officer_head.php'; ?>
<body>
<?php include 'includes/officer_login.php'; ?>
<div class="app" id="app" style="display:none;">
  <?php include 'includes/officer_sidebar.php'; ?>
  <div class="main">
    <?php include 'includes/officer_topbar.php'; ?>
    <div class="content">
      <div id="panel-overview" class="panel active">
        <div class="kpi-strip">
          <div class="kpi gold">
            <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
            <div class="kpi-val" id="statRevenue">RM –</div>
            <div class="kpi-lbl">Today's Revenue</div>
          </div>
          <div class="kpi green">
            <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-val" id="statDone">–</div>
            <div class="kpi-lbl">Completed</div>
          </div>
          <div class="kpi blue">
            <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-val" id="statTotal">–</div>
            <div class="kpi-lbl">Today's Bookings</div>
          </div>
          <div class="kpi blue">
            <div class="kpi-icon"><i class="fas fa-list-alt"></i></div>
            <div class="kpi-val" id="statAllTime">–</div>
            <div class="kpi-lbl">Total Bookings</div>
          </div>
          <div class="kpi amber">
            <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="kpi-val" id="statPending">–</div>
            <div class="kpi-lbl">Pending Approval</div>
          </div>
          <div class="kpi purple">
            <div class="kpi-icon"><i class="fas fa-scissors"></i></div>
            <div class="kpi-val" id="statConfirmed">–</div>
            <div class="kpi-lbl">In Progress</div>
          </div>
          <div class="kpi red">
            <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            <div class="kpi-val" id="statRemaining">–</div>
            <div class="kpi-lbl">Remaining</div>
          </div>
        </div>

        <div class="next-banner" id="nextBanner" style="display:none;">
          <div class="nb-icon">✂️</div>
          <div>
            <div class="nb-label">Next Customer</div>
            <div class="nb-name" id="nextName">–</div>
            <div class="nb-meta" id="nextService">–</div>
            <div class="nb-meta" id="nextBarber" style="color:var(--gold)"></div>
          </div>
          <div class="nb-time" id="nextTime">–</div>
        </div>

        <div class="grid-6040">
          <div class="card mb">
            <div class="card-header">
              <div class="card-title"><span class="dot"></span> Live Queue <span style="font-weight:400;color:var(--muted)"> — updates every 12s</span></div>
              <a class="btn btn-ghost btn-sm" href="queue.php">View All</a>
            </div>
            <div class="card-body no-pad" id="liveQueue">
              <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading queue...</p></div>
            </div>
            <div class="card-footer">
              <button class="btn btn-gold btn-sm" onclick="openWalkIn()"><i class="fas fa-plus"></i> Add Walk-In</button>
            </div>
          </div>

          <div>
            <div class="card mb">
              <div class="card-header">
                <div class="card-title"><i class="fas fa-user-tie" style="color:var(--gold)"></i> Barber Load Today</div>
              </div>
              <div class="card-body" id="barberUtil">
                <div class="empty-state"><i class="fas fa-spinner fa-spin"></i></div>
              </div>
            </div>

            <div class="card">
              <div class="card-body" style="text-align:center;padding:1.5rem;">
                <div style="font-family:var(--font-mono);font-size:2.8rem;color:var(--gold);letter-spacing:0.06em;line-height:1;" class="clock-display">00:00:00</div>
                <div style="font-size:0.75rem;color:var(--muted);margin-top:0.4rem;" class="date-display">–</div>
                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
                  <button class="btn btn-ghost btn-sm" onclick="manualRefresh()"><i class="fas fa-arrows-rotate"></i> Refresh All</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/officer_walkin_modal.php'; ?>
<?php include 'includes/officer_toast.php'; ?>
<script>window.defaultOfficerPanel = 'overview';</script>
<script src="app.js"></script>
</body>
</html>
