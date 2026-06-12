// =====================
// BARBERBUS – MAIN JS
// =====================

// ── NAVBAR SCROLL ──
const navbar = document.getElementById('navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  });
}

// ── HAMBURGER MENU ──
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');
if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    const spans = hamburger.querySelectorAll('span');
    hamburger.classList.toggle('active');
  });
}

// ── TOAST NOTIFICATIONS ──
function showToast(msg, type = 'success') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.className = `toast show ${type}`;
  setTimeout(() => { toast.className = 'toast'; }, 3500);
}

// ── SCROLL ANIMATIONS ──
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.style.opacity = '1';
      e.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.preview-card, .testimonial-card, .service-card, .barber-card, .fashion-card').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(24px)';
  el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  observer.observe(el);
});

// ── COUNTER ANIMATION ──
function animateCounter(el, target) {
  let current = 0;
  const isDecimal = target.toString().includes('.');
  const step = Math.max(1, Math.floor(target / 50));
  const timer = setInterval(() => {
    current = Math.min(current + step, target);
    el.textContent = isDecimal ? current.toFixed(1) + '★' : current + (el.dataset.suffix || '');
    if (current >= target) clearInterval(timer);
  }, 30);
}

// ── URL PARAM FOR BARBER SELECT ──
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const barber = params.get('barber');
  if (barber) {
    const cards = document.querySelectorAll('.barber-select-card');
    cards.forEach(c => {
      c.classList.remove('selected');
      if (c.dataset.barber && c.dataset.barber.toLowerCase().startsWith(barber.toLowerCase())) {
        c.classList.add('selected');
        const sumBarber = document.getElementById('sum-barber');
        if (sumBarber) sumBarber.textContent = c.dataset.barber;
      }
    });
  }

  // Update main nav login button when a user is already logged in
  const mainLoginBtn = document.getElementById('mainLoginBtn');
  const token = localStorage.getItem('bb_token');
  const user = JSON.parse(localStorage.getItem('bb_user') || 'null');
  if (mainLoginBtn && token && user && user.name) {
    mainLoginBtn.textContent = 'Dashboard';
    mainLoginBtn.href = 'user/my_bookings.php';
    mainLoginBtn.classList.add('btn-primary');
  }
});
