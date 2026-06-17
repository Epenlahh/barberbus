/**
 * BARBERBUS – USER DASHBOARD JS
 * Handles auth guards, user info population, and dashboard logic.
 */

document.addEventListener('DOMContentLoaded', () => {
    checkUserAuth();
    initUserMenu();
    
    // If we are on my_bookings.php, load stats and recent bookings
    if (window.location.pathname.includes('my_bookings.php')) {
        loadDashboardData();
    }
});

/**
 * Check if user is logged in and has user role
 */
function checkUserAuth() {
    const token = localStorage.getItem('bb_token');
    const user = JSON.parse(localStorage.getItem('bb_user'));

    if (!token || !user) {
        window.location.href = '../login.php';
        return;
    }

    // Check role - only users can access user pages
    if (user.role !== 'user') {
        if (user.role === 'admin') {
            window.location.href = '../admin/index.php';
        } else if (user.role === 'officer') {
            window.location.href = '../officer/index.php';
        } else {
            window.location.href = '../login.php';
        }
        return;
    }

    // Update UI with user name
    const nameLabels = document.querySelectorAll('#userNameLabel, #dropdownName');
    nameLabels.forEach(el => el.textContent = user.name);
    
    const emailLabel = document.getElementById('dropdownEmail');
    if (emailLabel) emailLabel.textContent = user.email;

    const initialsLabel = document.getElementById('userInitials');
    if (initialsLabel) {
        const initials = user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        initialsLabel.textContent = initials;
    }
}

/**
 * Handle user menu dropdown
 */
function initUserMenu() {
    const userMenu = document.getElementById('userMenu');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.remove('show');
        });
    }
}

/**
 * Logout functionality
 */
function userLogout() {
    localStorage.removeItem('bb_token');
    localStorage.removeItem('bb_user');
    window.location.href = '../login.php';
}

/**
 * Fetch and load dashboard data (stats + recent bookings)
 */
async function loadDashboardData() {
    const token = localStorage.getItem('bb_token');
    
    try {
        const response = await fetch('../api/bookings.php', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            const bookings = result.data.bookings;
            populateStats(bookings);
            populateRecentBookings(bookings.slice(0, 5)); // Last 5
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function populateStats(bookings) {
    const totalBookingsEl = document.getElementById('stat-total-bookings');
    const upcomingBookingsEl = document.getElementById('stat-upcoming-bookings');
    
    if (totalBookingsEl) totalBookingsEl.textContent = bookings.length;
    
    if (upcomingBookingsEl) {
        const now = new Date();
        const upcomingCount = bookings.filter(b => {
            const bDate = new Date(`${b.booking_date} ${b.booking_time}`);
            return bDate >= now && b.status !== 'cancelled';
        }).length;
        upcomingBookingsEl.textContent = upcomingCount;
    }
}

function populateRecentBookings(bookings) {
    const tableBody = document.getElementById('recent-bookings-body');
    if (!tableBody) return;

    if (bookings.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-muted);">No bookings found. <a href="booking.php" style="color:var(--gold);">Book your first cut!</a></td></tr>';
        return;
    }

    tableBody.innerHTML = bookings.map(b => `
        <tr>
            <td><strong>${b.service_name}</strong></td>
            <td>${b.barber_name || 'Any Available'}</td>
            <td>${formatDate(b.booking_date)}<br/><small style="color:var(--text-muted)">${formatTime(b.booking_time)}</small></td>
            <td>RM ${parseFloat(b.total_price).toFixed(0)}</td>
            <td><span class="status-badge status-${b.status}">${b.status}</span></td>
        </tr>
    `).join('');
}

// Helpers
function formatDate(dateStr) {
    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString('en-MY', options);
}

function formatTime(timeStr) {
    const [h, m] = timeStr.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const h12 = hour % 12 || 12;
    return `${h12}:${m} ${ampm}`;
}
