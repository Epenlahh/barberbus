<div class="modal-overlay" id="walkInModal">
  <div class="modal">
    <div class="modal-head">
      <h3>✂ Add Walk-In Customer</h3>
      <button class="modal-close" onclick="closeWalkIn()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="walkInForm" style="display:flex;flex-direction:column;gap:0.9rem;">
        <div class="form-row">
          <div class="fg">
            <label>Name <span style="color:var(--gold)">*</span></label>
            <input type="text" id="wiName" placeholder="Customer name" required/>
          </div>
          <div class="fg">
            <label>Phone</label>
            <input type="tel" id="wiPhone" placeholder="+60 12-xxx xxxx"/>
          </div>
        </div>
        <div class="form-row">
          <div class="fg">
            <label>Service <span style="color:var(--gold)">*</span></label>
            <select id="wiService" required>
              <option value="">Loading...</option>
            </select>
          </div>
          <div class="fg">
            <label>Barber</label>
            <select id="wiBarber">
              <option value="">Any Available</option>
            </select>
          </div>
        </div>
        <div class="fg">
          <label>Payment Method</label>
          <select id="wiPay">
            <option value="cash">Cash</option>
            <option value="online_banking">Online Banking</option>
            <option value="ewallet">E-Wallet (Touch n Go / GrabPay)</option>
            <option value="card">Credit / Debit Card</option>
          </select>
        </div>
        <div class="fg">
          <label>Notes (optional)</label>
          <input type="text" id="wiNotes" placeholder="Special requests or preferences..."/>
        </div>
      </form>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeWalkIn()">Cancel</button>
      <button class="btn btn-gold" id="wiSubmitBtn" onclick="document.getElementById('walkInForm').dispatchEvent(new Event('submit'))">
        <i class="fas fa-plus"></i> Add to Queue
      </button>
    </div>
  </div>
</div>
