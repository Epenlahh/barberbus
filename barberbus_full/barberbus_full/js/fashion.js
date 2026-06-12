// ========================
// BARBERBUS – FASHION JS
// ========================

document.addEventListener('DOMContentLoaded', () => {
  const filterBtns = document.querySelectorAll('.fashion-filter .filter-btn');
  const cards = document.querySelectorAll('#fashionGrid .fashion-card');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      cards.forEach(card => {
        if (filter === 'all' || card.dataset.cat === filter) {
          card.style.display = 'block';
          card.style.opacity = '0';
          card.style.transform = 'scale(0.96)';
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
          }, 50);
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
});
