// =============================================
// PROMOTIONS DISPLAY
// =============================================

document.addEventListener('DOMContentLoaded', function() {
  loadPromotionsCarousel();
});

async function loadPromotionsCarousel() {
  try {
    const response = await fetch('api/promotions.php');
    const result = await response.json();
    
    if (!result.success || !result.data.promotions || result.data.promotions.length === 0) {
      // No promotions, hide carousel
      document.getElementById('promotionsCarousel').style.display = 'none';
      return;
    }

    const promotions = result.data.promotions;
    const carousel = document.getElementById('promotionsCarousel');

    // Create carousel HTML
    let carouselHTML = `
      <div style="max-width:1200px;margin:0 auto;padding:0 1rem">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem">
    `;

    promotions.forEach(promo => {
      const bgImage = promo.image ? `background-image:url('${promo.image}');` : '';
      const linkAttr = promo.link ? `onclick="window.location.href='${promo.link}'"` : '';
      const clickStyle = promo.link ? 'cursor:pointer;' : '';

      carouselHTML += `
        <div class="promo-card" style="${bgImage}background-size:cover;background-position:center;border-radius:12px;overflow:hidden;background-color:var(--gold);min-height:200px;position:relative;box-shadow:0 8px 32px rgba(201,168,76,0.15);transition:transform 0.3s ease,box-shadow 0.3s ease;${clickStyle}" ${linkAttr} onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 12px 48px rgba(201,168,76,0.25)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 32px rgba(201,168,76,0.15)'">
          <div style="background:linear-gradient(135deg,rgba(0,0,0,0.4),rgba(0,0,0,0.8));padding:1.5rem;height:100%;display:flex;flex-direction:column;justify-content:flex-end">
            <h3 style="color:var(--gold);font-size:1.4rem;margin:0 0 0.5rem 0;font-family:'Playfair Display',serif">${promo.title}</h3>
            <p style="color:#fff;font-size:0.9rem;margin:0;opacity:0.95">${promo.description ? promo.description.substring(0, 80) + (promo.description.length > 80 ? '...' : '') : ''}</p>
            ${promo.link ? '<div style="margin-top:0.8rem;color:var(--gold);font-size:0.85rem;font-weight:500">Learn More →</div>' : ''}
          </div>
        </div>
      `;
    });

    carouselHTML += `
        </div>
      </div>
    `;

    carousel.innerHTML = carouselHTML;
    carousel.style.display = 'block';

  } catch (error) {
    console.error('Error loading promotions:', error);
  }
}
