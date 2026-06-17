// ========================
// BARBERBUS – BOOKING JS
// ========================

let currentStep = 1;
let selectedDate = null;
let selectedTime = null;
let calYear, calMonth;

const booking = {
  service: 'Classic Haircut',
  price: 25,
  duration: 30,
  barber: 'Any Available',
  date: null,
  time: null,
  payMethod: 'online_banking',
  bank: 'Maybank'
};

// ── STEP NAVIGATION ──
function nextStep(step) {
  // Validate before moving forward
  if (step > currentStep) {
    if (currentStep === 1) {
      const name = document.getElementById('clientName').value.trim();
      const phone = document.getElementById('clientPhone').value.trim();
      const email = document.getElementById('clientEmail').value.trim();
      if (!name || !phone || !email) {
        showToast('Please fill in your name, phone and email.', 'error');
        return;
      }
    }
    if (currentStep === 2) {
      if (!selectedDate || !selectedTime) {
        showToast('Please select a date and time slot.', 'error');
        return;
      }
    }
  }

  // Hide current step
  document.getElementById('step' + currentStep).classList.remove('active');
  document.getElementById('step-indicator-' + currentStep).classList.remove('active');
  document.getElementById('step-indicator-' + currentStep).classList.add('done');

  // Show new step
  currentStep = step;
  document.getElementById('step' + currentStep).classList.add('active');
  document.getElementById('step-indicator-' + currentStep).classList.add('active');

  // If reaching confirm step, build confirmation
  if (step === 4) buildConfirmation();

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── SERVICE SELECTION ──
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.service-select-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.service-select-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      booking.service = card.dataset.service;
      booking.price = parseInt(card.dataset.price);
      booking.duration = parseInt(card.dataset.duration);
      updateSummary();
    });
  });

  // ── BARBER SELECTION ──
  document.querySelectorAll('.barber-select-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.barber-select-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      booking.barber = card.dataset.barber;
      updateSummary();
    });
  });

  // ── PAYMENT METHOD ──
  document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      booking.payMethod = card.dataset.pay;
      document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
      const panel = document.getElementById('panel-' + card.dataset.pay);
      if (panel) panel.classList.add('active');
    });
  });

  // ── BANK SELECTION ──
  document.querySelectorAll('.bank-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const parent = btn.closest('.pay-panel');
      parent.querySelectorAll('.bank-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      booking.bank = btn.dataset.bank || btn.dataset.ew;
    });
  });

  // Init calendar
  const now = new Date();
  calYear = now.getFullYear();
  calMonth = now.getMonth();
  renderCalendar();
});

// ── CALENDAR ──
function renderCalendar() {
  const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  document.getElementById('calMonthYear').textContent = `${monthNames[calMonth]} ${calYear}`;

  const grid = document.getElementById('calendarGrid');
  grid.innerHTML = '';

  // Day headers
  ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
    const el = document.createElement('div');
    el.className = 'cal-day-name';
    el.textContent = d;
    grid.appendChild(el);
  });

  const firstDay = new Date(calYear, calMonth, 1).getDay();
  const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
  const today = new Date();

  // Empty cells
  for (let i = 0; i < firstDay; i++) {
    const el = document.createElement('div');
    el.className = 'cal-day empty';
    grid.appendChild(el);
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const el = document.createElement('div');
    el.className = 'cal-day';
    el.textContent = d;

    const thisDate = new Date(calYear, calMonth, d);
    if (thisDate < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
      el.classList.add('past');
    } else {
      if (thisDate.toDateString() === today.toDateString()) el.classList.add('today');
      el.addEventListener('click', () => selectDate(d, el));
    }

    if (selectedDate && selectedDate.getDate() === d && selectedDate.getMonth() === calMonth && selectedDate.getFullYear() === calYear) {
      el.classList.add('selected');
    }

    grid.appendChild(el);
  }
}

function selectDate(day, el) {
  document.querySelectorAll('.cal-day').forEach(d => d.classList.remove('selected'));
  el.classList.add('selected');
  selectedDate = new Date(calYear, calMonth, day);
  booking.date = selectedDate.toLocaleDateString('en-MY', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
  updateSummary();
  renderTimeSlots();
}

function changeMonth(dir) {
  calMonth += dir;
  if (calMonth > 11) { calMonth = 0; calYear++; }
  if (calMonth < 0) { calMonth = 11; calYear--; }
  renderCalendar();
}

// ── TIME SLOTS ──
function renderTimeSlots() {
  const grid = document.getElementById('slotsGrid');
  grid.innerHTML = '';
  const times = ['10:00 AM','10:30 AM','11:00 AM','11:30 AM','12:00 PM','12:30 PM','2:00 PM','2:30 PM','3:00 PM','3:30 PM','4:00 PM','4:30 PM','5:00 PM','5:30 PM','6:00 PM','6:30 PM','7:00 PM','7:30 PM','8:00 PM'];
  // Randomly mark some as booked for demo
  const booked = ['11:00 AM','2:00 PM','4:30 PM','6:00 PM'];

  times.forEach(t => {
    const slot = document.createElement('div');
    slot.className = 'time-slot';
    slot.textContent = t;
    if (booked.includes(t)) {
      slot.classList.add('booked');
    } else {
      slot.addEventListener('click', () => {
        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
        selectedTime = t;
        booking.time = t;
        updateSummary();
      });
    }
    grid.appendChild(slot);
  });
}

// ── SUMMARY UPDATE ──
function updateSummary() {
  const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  set('sum-service', booking.service);
  set('sum-barber', booking.barber);
  set('sum-date', booking.date || 'Not selected');
  set('sum-time', booking.time || 'Not selected');
  set('sum-duration', booking.duration + ' min');
  set('sum-total', 'RM ' + booking.price);
}

// ── CARD FORMATTING ──
function formatCard(input) {
  let v = input.value.replace(/\D/g, '').substring(0, 16);
  input.value = v.replace(/(.{4})/g, '$1 ').trim();
}

function formatExpiry(input) {
  let v = input.value.replace(/\D/g, '').substring(0, 4);
  if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
  input.value = v;
}

// ── CONFIRMATION ──
function buildConfirmation() {
  const ref = 'BB-' + Date.now().toString().slice(-6);
  document.getElementById('bookingRef').textContent = ref;

  const summary = document.getElementById('confirmSummary');
  const name = document.getElementById('clientName')?.value || 'Guest';
  summary.innerHTML = `
    <div class="summary-item"><span>Client</span><strong>${name}</strong></div>
    <div class="summary-item"><span>Service</span><strong>${booking.service}</strong></div>
    <div class="summary-item"><span>Barber</span><strong>${booking.barber}</strong></div>
    <div class="summary-item"><span>Date</span><strong>${booking.date || 'N/A'}</strong></div>
    <div class="summary-item"><span>Time</span><strong>${booking.time || 'N/A'}</strong></div>
    <div class="summary-item"><span>Duration</span><strong>${booking.duration} min</strong></div>
    <div class="summary-divider"></div>
    <div class="summary-item total"><span>Total Paid</span><strong>RM ${booking.price}</strong></div>
  `;
}
