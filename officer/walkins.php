<?php
$pageTitle = 'BarberBus – Walk-In Entry';
$currentPanel = 'walkins';
$panelTitle = 'Walk-In Entry';
$panelSub = 'Add unscheduled customers';
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
      <div id="panel-walkins" class="panel active">
        <div style="max-width:640px;margin:0 auto;">
          <div class="card">
            <div class="card-header">
              <div class="card-title"><i class="fas fa-person-walking-arrow-right" style="color:var(--gold)"></i> Add Walk-In Customer</div>
            </div>
            <div class="card-body">
              <p style="color:var(--muted);font-size:0.82rem;margin-bottom:1.2rem;">Add a customer who walks in without a prior booking. They will be added to today's queue as confirmed.</p>
              <form id="walkInFormPanel" onsubmit="submitWalkInPanel(event)">
                <div style="display:flex;flex-direction:column;gap:0.9rem;">
                  <div class="form-row">
                    <div class="fg">
                      <label>Customer Name <span style="color:var(--gold)">*</span></label>
                      <input type="text" id="wiNameP" placeholder="Full name" required/>
                    </div>
                    <div class="fg">
                      <label>Phone Number</label>
                      <input type="tel" id="wiPhoneP" placeholder="+60 12-345 6789"/>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="fg">
                      <label>Service <span style="color:var(--gold)">*</span></label>
                      <select id="wiServiceP" required>
                        <option value="">Select service...</option>
                      </select>
                    </div>
                    <div class="fg">
                      <label>Assign Barber</label>
                      <select id="wiBarberP">
                        <option value="">Any Available</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="fg">
                      <label>Payment Method</label>
                      <select id="wiPayP">
                        <option value="cash">Cash</option>
                        <option value="online_banking">Online Banking</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="card">Card</option>
                      </select>
                    </div>
                    <div class="fg">
                      <label>Notes</label>
                      <input type="text" id="wiNotesP" placeholder="Special requests..."/>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-gold btn-full" style="margin-top:0.5rem;padding:0.75rem;">
                    <i class="fas fa-plus-circle"></i> Add to Queue Now
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/officer_walkin_modal.php'; ?>
<?php include 'includes/officer_toast.php'; ?>
<script>window.defaultOfficerPanel = 'walkins';</script>
<script src="app.js"></script>
</body>
</html>
