/**
 * BARBERBUS – USER BOOKINGS JS
 * Handles fetching, displaying, and cancelling user bookings.
 */

document.addEventListener('DOMContentLoaded', () => {
    loadUserBookings();
});

async function loadUserBookings() {
    const token = localStorage.getItem('bb_token');
    const container = document.getElementById('bookings-container');
    
    if (!container) return;

    try {
        const response = await fetch('../api/bookings.php', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            renderBookings(result.data.bookings);
        } else {
            container.innerHTML = `<div style="text-align:center; padding:3rem;">Error: ${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error loading bookings:', error);
        container.innerHTML = `<div style="text-align:center; padding:3rem;">Connection error. Please try again.</div>`;
    }
}

function renderBookings(bookings) {
    const container = document.getElementById('bookings-container');
    if (bookings.length === 0) {
        container.innerHTML = `
            <div style="text-align:center; padding:5rem 2rem; background:var(--card-bg); border-radius:12px; border:1px solid var(--card-border);">
                <i class="fas fa-calendar-times" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem; display:block;"></i>
                <h3 style="margin-bottom:0.5rem;">No Bookings Yet</h3>
                <p style="color:var(--text-muted); margin-bottom:1.5rem;">You haven't made any appointments. Ready for a fresh cut?</p>
                <a href="booking.php" class="btn-primary">Book Now</a>
            </div>
        `;
        return;
    }

    container.innerHTML = bookings.map(b => {
        const date = new Date(b.booking_date);
        const day = date.getDate();
        const month = date.toLocaleString('default', { month: 'short' });
        const time = formatTime(b.booking_time);
        
        return `
            <div class="booking-card" id="booking-${b.id}">
                <div class="booking-main-info">
                    <div class="booking-date-badge">
                        <span class="day">${day}</span>
                        <span class="month">${month}</span>
                    </div>
                    <div class="booking-details">
                        <span class="status-badge status-${b.status}" style="margin-bottom:0.5rem; display:inline-block;">${b.status}</span>
                        <h3>${b.service_name}</h3>
                        <div class="booking-meta">
                            <span><i class="fas fa-user"></i> ${b.barber_name || 'Any Available'}</span>
                            <span><i class="fas fa-clock"></i> ${time}</span>
                            <span><i class="fas fa-money-bill"></i> RM ${parseFloat(b.total_price).toFixed(0)}</span>
                        </div>
                    </div>
                </div>
                <div class="booking-actions">
                    ${b.status === 'pending' ? `<button class="btn-cancel" onclick="cancelBooking(${b.id})">Cancel Appointment</button>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

async function cancelBooking(id) {
    if (!confirm('Are you sure you want to cancel this appointment?')) return;
    
    const token = localStorage.getItem('bb_token');
    const btn = document.querySelector(`#booking-${id} .btn-cancel`);
    if (btn) btn.disabled = true;

    try {
        const response = await fetch(`../api/bookings.php?id=${id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'cancelled' })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Booking cancelled successfully.');
            loadUserBookings(); // Refresh list
        } else {
            showToast(result.message, 'error');
            if (btn) btn.disabled = false;
        }
    } catch (error) {
        showToast('Error cancelling booking.', 'error');
        if (btn) btn.disabled = false;
    }
}

// Helpers
function formatTime(timeStr) {
    const [h, m] = timeStr.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const h12 = hour % 12 || 12;
    return `${h12}:${m} ${ampm}`;
}
