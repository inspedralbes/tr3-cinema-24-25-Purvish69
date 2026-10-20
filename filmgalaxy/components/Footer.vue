<template>
  <footer class="cinema-footer">
    <!-- Top Ambient Accent Line -->
    <div class="footer-ambient-line"></div>

    <div class="footer-container">
      <div class="footer-grid">
        <!-- Columna 1: Brand & Descripció -->
        <div class="footer-col brand-col">
          <NuxtLink to="/" class="footer-logo-link">
            <img 
              src="/logo/logofilmgalaxyTransparente.png" 
              alt="FilmGalaxy Logo" 
              class="footer-logo"
            />
          </NuxtLink>
          <p class="brand-description">
            El teu cinema galàctic de referència. Gaudeix de les millors estrenes a la gran pantalla amb butaques VIP reclinables i so envoltant d'última generació.
          </p>
          <div class="social-links-row">
            <a href="https://github.com/inspedralbes/tr3-cinema-24-25-Purvish69" target="_blank" rel="noopener" class="social-icon" title="GitHub">
              <i class="mdi mdi-github"></i>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener" class="social-icon" title="Instagram">
              <i class="mdi mdi-instagram"></i>
            </a>
            <a href="https://twitter.com" target="_blank" rel="noopener" class="social-icon" title="Twitter / X">
              <i class="mdi mdi-twitter"></i>
            </a>
            <a href="https://youtube.com" target="_blank" rel="noopener" class="social-icon" title="YouTube">
              <i class="mdi mdi-youtube"></i>
            </a>
          </div>
        </div>

        <!-- Columna 2: Navegació Ràpida -->
        <div class="footer-col">
          <h4 class="col-title">
            <i class="mdi mdi-compass-outline"></i>
            Explora
          </h4>
          <ul class="footer-links-list">
            <li>
              <NuxtLink to="/" class="footer-link">
                <i class="mdi mdi-chevron-right"></i>
                Inici & Estrenes
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/movies" class="footer-link">
                <i class="mdi mdi-chevron-right"></i>
                Cartellera de Pel·lícules
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/movieDetails/moviSession" class="footer-link">
                <i class="mdi mdi-chevron-right"></i>
                Sessions i Horaris
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/billetesDeUsuario" class="footer-link">
                <i class="mdi mdi-chevron-right"></i>
                Les Meves Entrades
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/profile" class="footer-link">
                <i class="mdi mdi-chevron-right"></i>
                El Meu Perfil VIP
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Columna 3: Tarifes & Promocions -->
        <div class="footer-col">
          <h4 class="col-title">
            <i class="mdi mdi-ticket-percent-outline"></i>
            Tarifes & Descomptes
          </h4>
          <div class="pricing-cards">
            <!-- Dia de l'Espectador -->
            <div class="price-mini-card highlight">
              <div class="price-header">
                <strong>Dimecres (Dia d'Espectador)</strong>
                <span class="badge-promo">-30%</span>
              </div>
              <div class="price-body">
                <span>Normal: <strong>4,00€</strong></span>
                <span>VIP: <strong>6,00€</strong></span>
              </div>
            </div>

            <!-- Cap de setmana / Normal -->
            <div class="price-mini-card">
              <div class="price-header">
                <strong>Tarifa Normal & Caps de Setmana</strong>
              </div>
              <div class="price-body">
                <span>Normal: <strong>6,00€</strong></span>
                <span>VIP: <strong>8,00€</strong></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna 4: Contacte & Horaris -->
        <div class="footer-col">
          <h4 class="col-title">
            <i class="mdi mdi-map-marker-radius-outline"></i>
            Contacte & Horaris
          </h4>
          <ul class="contact-info-list">
            <li class="contact-item">
              <i class="mdi mdi-phone-in-talk-outline"></i>
              <span>+34 932 123 456</span>
            </li>
            <li class="contact-item">
              <i class="mdi mdi-email-fast-outline"></i>
              <span>info@filmgalaxy.cat</span>
            </li>
            <li class="contact-item">
              <i class="mdi mdi-map-marker-outline"></i>
              <span>Carrer Gran Via de les Corts Catalanes, Barcelona</span>
            </li>
            <li class="contact-item">
              <i class="mdi mdi-clock-time-four-outline"></i>
              <span>Dilluns a Diumenge: 15:30h - 23:30h</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="footer-bottom">
        <p class="copyright-text">
          &copy; {{ new Date().getFullYear() }} <strong>FilmGalaxy</strong>. Tots els drets reservats.
        </p>
        <div class="footer-badges">
          <span class="tech-badge">
            <i class="mdi mdi-dolby"></i>
            Dolby Atmos & 4K
          </span>
          <span class="tech-badge">
            <i class="mdi mdi-star-shooting"></i>
            Projecte DAW Cinema
          </span>
        </div>
        <button type="button" class="back-to-top-btn" @click="scrollToTop" title="Tornar a dalt">
          <i class="mdi mdi-arrow-up"></i>
        </button>
      </div>
    </div>
  </footer>
</template>

<script setup>
const scrollToTop = () => {
  if (typeof window !== 'undefined') {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}
</script>

<style scoped>
/* Main Footer */
.cinema-footer {
  position: relative;
  background: linear-gradient(180deg, #101426 0%, #080a14 100%);
  color: #F2E9E4;
  overflow: hidden;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-ambient-line {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, rgba(200, 169, 110, 0.4) 50%, transparent 100%);
}

.footer-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 4rem 1.5rem 2rem 1.5rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.3fr 0.9fr 1.1fr 1fr;
  gap: 2.5rem;
  margin-bottom: 3.5rem;
}

/* Column general */
.footer-col {
  display: flex;
  flex-direction: column;
}

.col-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 1.25rem;
  letter-spacing: 0.3px;
}

.col-title i {
  color: #C8A96E;
  font-size: 1.25rem;
}

/* Brand Column */
.footer-logo-link {
  display: inline-block;
  margin-bottom: 1rem;
}

.footer-logo {
  height: 48px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 2px 8px rgba(200, 169, 110, 0.3));
}

.brand-description {
  color: #9A8C98;
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.social-links-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.social-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #C9ADA7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  text-decoration: none;
  transition: all 0.25s ease;
}

.social-icon:hover {
  background: rgba(200, 169, 110, 0.2);
  border-color: #C8A96E;
  color: #F5D78E;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(200, 169, 110, 0.25);
}

/* Navigation Links */
.footer-links-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.footer-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  color: #9A8C98;
  text-decoration: none;
  font-size: 0.925rem;
  transition: all 0.25s ease;
}

.footer-link i {
  color: #6c7293;
  font-size: 1rem;
  transition: transform 0.2s ease, color 0.2s ease;
}

.footer-link:hover {
  color: #F5D78E;
  transform: translateX(4px);
}

.footer-link:hover i {
  color: #C8A96E;
}

/* Pricing Mini Cards */
.pricing-cards {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.price-mini-card {
  background: rgba(255, 255, 255, 0.035);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 0.85rem 1rem;
  transition: all 0.25s ease;
}

.price-mini-card.highlight {
  background: rgba(200, 169, 110, 0.08);
  border-color: rgba(200, 169, 110, 0.3);
}

.price-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.4rem;
  font-size: 0.85rem;
  color: #F2E9E4;
}

.badge-promo {
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.7rem;
  font-weight: 800;
  padding: 0.15rem 0.5rem;
  border-radius: 9999px;
}

.price-body {
  display: flex;
  gap: 1rem;
  font-size: 0.825rem;
  color: #9A8C98;
}

.price-body strong {
  color: #F5D78E;
}

/* Contact List */
.contact-info-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
  color: #9A8C98;
  font-size: 0.9rem;
  line-height: 1.4;
}

.contact-item i {
  color: #C8A96E;
  font-size: 1.15rem;
  flex-shrink: 0;
  margin-top: 1px;
}

/* Bottom Bar */
.footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 1.75rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 0.85rem;
  color: #6c7293;
}

.copyright-text strong {
  color: #F2E9E4;
}

.footer-badges {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.tech-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  padding: 0.25rem 0.65rem;
  border-radius: 8px;
  color: #9A8C98;
  font-size: 0.75rem;
}

.tech-badge i {
  color: #C8A96E;
}

.back-to-top-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(200, 169, 110, 0.12);
  border: 1px solid rgba(200, 169, 110, 0.3);
  color: #F5D78E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.25s ease;
}

.back-to-top-btn:hover {
  background: #C8A96E;
  color: #121526;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(200, 169, 110, 0.3);
}

/* Responsive */
@media (max-width: 1024px) {
  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
}

@media (max-width: 640px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .footer-badges {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>
