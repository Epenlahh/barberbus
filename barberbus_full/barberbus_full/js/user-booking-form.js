/**
 * BARBERBUS – USER BOOKING FORM JS (standalone, no booking.js dependency)
 * Handles full booking flow with API submission for logged-in users.
 */

// ── STATE ──
let currentStep = 1;
let curYear, curMonth;

const userBooking = {
    service_id: 0,
    barber_id: null,
    booking_date: null,
    booking_date_raw: null,
    booking_time: null,
    booking_time_raw: null,
    pay_method: null,
    notes: '',
    service_name: 'Select a service',
    barber_name: 'Any Available',
    price: 0,
    duration: 30
};

document.addEventListener('DOMContentLoaded', () => {
    prefillUserData();
    setupBookingListeners();

    const selectedPaymentCard = document.querySelector('.payment-card.selected');
    if (selectedPaymentCard) {
        userBooking.pay_method = selectedPaymentCard.dataset.pay;
    }

    const activeService = document.querySelector('.service-select-card.selected');
    if (activeService) {
        userBooking.service_id = parseInt(activeService.dataset.serviceId, 10);
        userBooking.service_name = activeService.dataset.service;
        userBooking.price = parseInt(activeService.dataset.price, 10);
        userBooking.duration = parseInt(activeService.dataset.duration, 10);
    }

    const activeBarber = document.querySelector('.barber-select-card.selected');
    if (activeBarber) {
        userBooking.barber_id = activeBarber.dataset.barberId ? parseInt(activeBarber.dataset.barberId, 10) : null;
        userBooking.barber_name = activeBarber.dataset.barber;
    }

    initCalendar();
    updateUserSummary();

    // Pre-select barber from URL param (e.g., booking.php?barber=aariz)
    const params = new URLSearchParams(window.location.search);
    const barberParam = params.get('barber');
    if (barberParam) {
        document.querySelectorAll('.barber-select-card').forEach(card => {
            if (card.dataset.barber && card.dataset.barber.toLowerCase().replace(/\s+/g,'') === barberParam.toLowerCase()) {
                document.querySelectorAll('.barber-select-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                userBooking.barber_id = card.dataset.barberId ? parseInt(card.dataset.barberId, 10) : null;
                userBooking.barber_name = card.dataset.barber;
                updateUserSummary();
            }
        });
    }
});

// ── PRE-FILL USER DATA ──
function prefillUserData() {
    const user = JSON.parse(localStorage.getItem('bb_user'));
    if (!user) { window.location.href = '../login.php'; return; }

    const nameEl = document.getElementById('clientName');
    const emailEl = document.getElementById('clientEmail');
    const phoneEl = document.getElementById('clientPhone');

    if (nameEl)  { nameEl.value = user.name;  nameEl.readOnly = true; }
    if (emailEl) { emailEl.value = user.email; emailEl.readOnly = true; }
    if (phoneEl && user.phone) phoneEl.value = user.phone;
}

// ── STEP NAVIGATION ──
function nextStep(step) {
    // Validate before moving forward
    if (step > currentStep) {
        if (currentStep === 1) {
            if (!userBooking.service_id) {
                showToast('Please select a service.', 'error'); return;
            }
        }
        if (currentStep === 2) {
            if (!userBooking.booking_date_raw || !userBooking.booking_time_raw) {
                showToast('Please select a date and time slot.', 'error'); return;
            }
        }
    }

    document.getElementById('step' + currentStep).classList.remove('active');
    const prevIndicator = document.getElementById('step-indicator-' + currentStep);
    if (prevIndicator) { prevIndicator.classList.remove('active'); prevIndicator.classList.add('done'); }

    currentStep = step;
    document.getElementById('step' + currentStep).classList.add('active');
    const curIndicator = document.getElementById('step-indicator-' + currentStep);
    if (curIndicator) curIndicator.classList.add('active');

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── LISTENERS ──
function setupBookingListeners() {
    // Service cards
    document.querySelectorAll('.service-select-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.service-select-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            userBooking.service_id = parseInt(card.dataset.serviceId, 10);
            userBooking.service_name = card.dataset.service;
            userBooking.price = parseInt(card.dataset.price, 10);
            userBooking.duration = parseInt(card.dataset.duration, 10);
            updateUserSummary();
        });
    });

    // Barber cards
    document.querySelectorAll('.barber-select-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.barber-select-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            userBooking.barber_id = card.dataset.barberId ? parseInt(card.dataset.barberId) : null;
            userBooking.barber_name = card.dataset.barber;
            updateUserSummary();
        });
    });

    // Payment method cards
    document.querySelectorAll('.payment-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            userBooking.pay_method = card.dataset.pay;
            document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
            const panel = document.getElementById('panel-' + card.dataset.pay);
            if (panel) panel.classList.add('active');
        });
    });
}

// ── SUMMARY UPDATE ──
function updateUserSummary() {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('sum-service',  userBooking.service_name);
    set('sum-barber',   userBooking.barber_name);
    set('sum-date',     userBooking.booking_date  || 'Not selected');
    set('sum-time',     userBooking.booking_time  || 'Not selected');
    set('sum-duration', userBooking.duration + ' min');
    set('sum-total',    'RM ' + userBooking.price);
}

// ── CALENDAR ──
function initCalendar() {
    const now = new Date();
    curYear = now.getFullYear();
    curMonth = now.getMonth();
    renderCalendar();
}

function changeMonth(dir) {
    curMonth += dir;
    if (curMonth > 11) { curMonth = 0; curYear++; }
    if (curMonth < 0)  { curMonth = 11; curYear--; }
    renderCalendar();
}

function renderCalendar() {
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const calLabel = document.getElementById('calMonthYear');
    if (calLabel) calLabel.textContent = `${monthNames[curMonth]} ${curYear}`;

    const grid = document.getElementById('calendarGrid');
    if (!grid) return;
    grid.innerHTML = '';

    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
        const el = document.createElement('div');
        el.className = 'cal-day-name';
        el.textContent = d;
        grid.appendChild(el);
    });

    const firstDay     = new Date(curYear, curMonth, 1).getDay();
    const daysInMonth  = new Date(curYear, curMonth + 1, 0).getDate();
    const today        = new Date();

    for (let i = 0; i < firstDay; i++) {
        const el = document.createElement('div');
        el.className = 'cal-day empty';
        grid.appendChild(el);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const el = document.createElement('div');
        el.className = 'cal-day';
        el.textContent = d;
        const thisDate = new Date(curYear, curMonth, d);

        if (thisDate < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
            el.classList.add('past');
        } else {
            if (thisDate.toDateString() === today.toDateString()) el.classList.add('today');
            el.addEventListener('click', () => {
                document.querySelectorAll('.cal-day').forEach(cd => cd.classList.remove('selected'));
                el.classList.add('selected');
                userBooking.booking_date     = thisDate.toLocaleDateString('en-MY', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                userBooking.booking_date_raw = `${curYear}-${String(curMonth + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                updateUserSummary();
                renderTimeSlots();
            });
        }
        grid.appendChild(el);
    }
}

// ── TIME SLOTS ──
function renderTimeSlots() {
    const grid = document.getElementById('slotsGrid');
    if (!grid) return;
    grid.innerHTML = '';

    const slots = [
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

    slots.forEach(s => {
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        slot.textContent = s.label;
        slot.dataset.raw = s.raw;
        slot.addEventListener('click', () => {
            document.querySelectorAll('.time-slot').forEach(ts => ts.classList.remove('selected'));
            slot.classList.add('selected');
            userBooking.booking_time     = s.label;
            userBooking.booking_time_raw = s.raw;
            updateUserSummary();
        });
        grid.appendChild(slot);
    });
}

// ── SUBMIT BOOKING TO API ──
async function submitBooking() {
    const token = localStorage.getItem('bb_token');
    if (!token) {
        showToast('Session expired. Please log in again.', 'error');
        setTimeout(() => window.location.href = '../login.php', 2000);
        return;
    }

    const selectedService = document.querySelector('.service-select-card.selected');
    const selectedBarber = document.querySelector('.barber-select-card.selected');
    const selectedPay = document.querySelector('.payment-card.selected');
    const selectedDateCell = document.querySelector('.cal-day.selected');
    const selectedTimeSlot = document.querySelector('.time-slot.selected');

    if (!selectedService) {
        showToast('Please select a service.', 'error');
        return;
    }
    if (!selectedDateCell) {
        showToast('Please select a date.', 'error');
        return;
    }
    if (!selectedTimeSlot) {
        showToast('Please select a time slot.', 'error');
        return;
    }
    if (!selectedPay) {
        showToast('Please select a payment method.', 'error');
        return;
    }

    const serviceId = parseInt(selectedService.dataset.serviceId, 10);
    const barberId = selectedBarber?.dataset.barberId ? parseInt(selectedBarber.dataset.barberId, 10) : 0;
    const payMethod = selectedPay.dataset.pay;
    const notes = document.getElementById('notes')?.value || '';

    const monthYearText = document.getElementById('calMonthYear')?.textContent || '';
    const [monthName, yearText] = monthYearText.split(' ');
    const monthIndex = ['January','February','March','April','May','June','July','August','September','October','November','December'].indexOf(monthName);
    const day = selectedDateCell.textContent.trim();
    const year = parseInt(yearText, 10);
    const bookingDate = monthIndex >= 0 && year && day ? `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(parseInt(day, 10)).padStart(2, '0')}` : '';
    const bookingTime = selectedTimeSlot.dataset.raw || '';

    if (!bookingDate) {
        showToast('Unable to resolve selected date. Please choose again.', 'error');
        return;
    }
    if (!bookingTime) {
        showToast('Unable to resolve selected time. Please choose a slot again.', 'error');
        return;
    }

    userBooking.service_id = serviceId;
    userBooking.barber_id = barberId || 0;
    userBooking.pay_method = payMethod;
    userBooking.booking_date_raw = bookingDate;
    userBooking.booking_time_raw = bookingTime;
    userBooking.booking_date = new Date(year, monthIndex, parseInt(day, 10)).toLocaleDateString('en-MY', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    userBooking.booking_time = selectedTimeSlot.textContent;
    userBooking.service_name = selectedService.dataset.service;
    userBooking.price = parseInt(selectedService.dataset.price, 10);
    userBooking.duration = parseInt(selectedService.dataset.duration, 10);
    userBooking.barber_name = selectedBarber?.dataset.barber || 'Any Available';
    updateUserSummary();

    const btn = document.querySelector('#step3 .btn-primary');
    if (btn) { btn.textContent = 'Booking...'; btn.disabled = true; }

    const payload = {
        service_id:   serviceId,
        barber_id:    barberId,
        booking_date: bookingDate,
        booking_time: bookingTime,
        pay_method:   payMethod,
        notes:        notes
    };

    try {
        const response = await fetch('../api/bookings.php', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('bookingRef').textContent = 'BB-' + result.data.booking_id;
            buildUserConfirmationSummary();
            nextStep(4);
            showToast('Appointment booked successfully!', 'success');
        } else {
            showToast(result.message || 'Booking failed. Please try again.', 'error');
            if (btn) { btn.textContent = 'Confirm Booking →'; btn.disabled = false; }
        }
    } catch (error) {
        console.error('Booking error:', error);
        showToast('Connection error. Please check your internet or server status.', 'error');
        if (btn) { btn.textContent = 'Confirm Booking →'; btn.disabled = false; }
    }
}

// ── CONFIRMATION SUMMARY ──
function buildUserConfirmationSummary() {
    const summary = document.getElementById('confirmSummary');
    if (!summary) return;
    const user = JSON.parse(localStorage.getItem('bb_user')) || {};

    summary.innerHTML = `
        <div class="summary-item"><span>Client</span><strong>${user.name || 'Guest'}</strong></div>
        <div class="summary-item"><span>Service</span><strong>${userBooking.service_name}</strong></div>
        <div class="summary-item"><span>Barber</span><strong>${userBooking.barber_name}</strong></div>
        <div class="summary-item"><span>Date</span><strong>${userBooking.booking_date}</strong></div>
        <div class="summary-item"><span>Time</span><strong>${userBooking.booking_time}</strong></div>
        <div class="summary-item"><span>Duration</span><strong>${userBooking.duration} min</strong></div>
        <div class="summary-item"><span>Payment</span><strong>${userBooking.pay_method ? userBooking.pay_method.replace('_', ' ').toUpperCase() : 'N/A'}</strong></div>
        <div class="summary-divider"></div>
        <div class="summary-item total"><span>Total</span><strong>RM ${userBooking.price}</strong></div>
    `;
}

// ── CARD FORMATTING HELPERS ──
function formatCard(input) {
    let v = input.value.replace(/\D/g,'').substring(0,16);
    input.value = v.replace(/(.{4})/g,'$1 ').trim();
}
function formatExpiry(input) {
    let v = input.value.replace(/\D/g,'').substring(0,4);
    if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
    input.value = v;
}
