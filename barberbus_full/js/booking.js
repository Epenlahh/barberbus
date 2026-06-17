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
  payMethod: 'cash'
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
  prefillUserDetails();

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

  // Initialize selected service from current markup
  const activeService = document.querySelector('.service-select-card.selected');
  if (activeService) {
    booking.service = activeService.dataset.service;
    booking.price = parseInt(activeService.dataset.price);
    booking.duration = parseInt(activeService.dataset.duration);
  }

  // ── BARBER SELECTION ──
  document.querySelectorAll('.barber-select-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.barber-select-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      booking.barber = card.dataset.barber;
      updateSummary();
    });
  });

  const activeBarber = document.querySelector('.barber-select-card.selected');
  if (activeBarber) {
    booking.barber = activeBarber.dataset.barber;
  }

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

  const activePay = document.querySelector('.payment-card.selected');
  if (activePay) {
    booking.payMethod = activePay.dataset.pay;
  }

  // Init calendar
  const now = new Date();
  calYear = now.getFullYear();
  calMonth = now.getMonth();
  renderCalendar();
});

function prefillUserDetails() {
  const user = JSON.parse(localStorage.getItem('bb_user') || 'null');
  if (!user) return;

  const nameEl = document.getElementById('clientName');
  const emailEl = document.getElementById('clientEmail');
  const phoneEl = document.getElementById('clientPhone');

  if (nameEl) {
    nameEl.value = user.name;
    nameEl.readOnly = true;
  }
  if (emailEl) {
    emailEl.value = user.email;
    emailEl.readOnly = true;
  }
  if (phoneEl && user.phone) {
    phoneEl.value = user.phone;
  }
}

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
  const times = [
    { label: '10:00 AM', raw: '10:00:00' }, { label: '10:30 AM', raw: '10:30:00' },
    { label: '11:00 AM', raw: '11:00:00' }, { label: '11:30 AM', raw: '11:30:00' },
    { label: '12:00 PM', raw: '12:00:00' }, { label: '12:30 PM', raw: '12:30:00' },
    { label: '02:00 PM', raw: '14:00:00' }, { label: '02:30 PM', raw: '14:30:00' },
    { label: '03:00 PM', raw: '15:00:00' }, { label: '03:30 PM', raw: '15:30:00' },
    { label: '04:00 PM', raw: '16:00:00' }, { label: '04:30 PM', raw: '16:30:00' },
    { label: '05:00 PM', raw: '17:00:00' }, { label: '05:30 PM', raw: '17:30:00' },
    { label: '06:00 PM', raw: '18:00:00' }, { label: '06:30 PM', raw: '18:30:00' },
    { label: '07:00 PM', raw: '19:00:00' }, { label: '07:30 PM', raw: '19:30:00' },
    { label: '08:00 PM', raw: '20:00:00' }
  ];
  const booked = ['11:00 AM', '2:00 PM', '4:30 PM', '6:00 PM'];

  times.forEach(slotInfo => {
    const slot = document.createElement('div');
    slot.className = 'time-slot';
    slot.textContent = slotInfo.label;
    if (booked.includes(slotInfo.label)) {
      slot.classList.add('booked');
    } else {
      slot.addEventListener('click', () => {
        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
        selectedTime = slotInfo.raw;
        booking.time = slotInfo.label;
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

async function submitBooking() {
  const name = document.getElementById('clientName')?.value.trim();
  const phone = document.getElementById('clientPhone')?.value.trim();
  const email = document.getElementById('clientEmail')?.value.trim();
  const notes = document.getElementById('notes')?.value.trim() || '';
  const serviceCard = document.querySelector('.service-select-card.selected');
  const barberCard = document.querySelector('.barber-select-card.selected');
  const payCard = document.querySelector('.payment-card.selected');

  if (!name || !phone || !email) {
    showToast('Please fill in your name, phone and email.', 'error');
    return;
  }
  if (!selectedDate || !selectedTime) {
    showToast('Please select a date and time slot.', 'error');
    return;
  }
  if (!serviceCard) {
    showToast('Please select a service.', 'error');
    return;
  }
  if (!payCard) {
    showToast('Please select a payment method.', 'error');
    return;
  }

  const serviceId = serviceCard.dataset.serviceId;
  const barberId = barberCard?.dataset.barberId || '';
  const payMethod = payCard.dataset.pay;
  const bookingDate = selectedDate.toISOString().slice(0, 10);

  const btn = document.querySelector('#step3 .btn-primary');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Booking...';
  }

  const payload = {
    name,
    email,
    phone,
    service_id: parseInt(serviceId, 10),
    barber_id: barberId ? parseInt(barberId, 10) : 0,
    booking_date: bookingDate,
    booking_time: selectedTime,
    pay_method: payMethod,
    notes
  };

  const headers = { 'Content-Type': 'application/json' };
  const token = localStorage.getItem('bb_token');
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  try {
    const response = await fetch('api/bookings.php', {
      method: 'POST',
      headers,
      body: JSON.stringify(payload)
    });
    const result = await response.json();

    if (result.success) {
      document.getElementById('bookingRef').textContent = 'BB-' + result.data.booking_id;
      buildConfirmation();
      nextStep(4);
      showToast('Booking created successfully!', 'success');
    } else {
      showToast(result.message || 'Booking failed.', 'error');
    }
  } catch (error) {
    console.error('Booking submission error:', error);
    showToast('Unable to submit booking. Please try again.', 'error');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Confirm Booking →';
    }
  }
}
