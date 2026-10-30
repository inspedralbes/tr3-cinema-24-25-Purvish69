<template>
  <footer class="cinema-footer">
    <!-- Top Accent Line -->
    <div class="footer-ambient-line"></div>

    <div class="footer-container">
      <!-- 4 Columnas Compactas con Toda la Información -->
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
            El teu cinema galàctic de referència. Estrenes a la gran pantalla amb butaques VIP i so envoltant d'última generació.
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
                Cartellera
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
                <strong>Dimecres (Dia Espectador)</strong>
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
                <strong>Tarifa Normal & Caps Setmana</strong>
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
              <span>Gran Via de les Corts Catalanes, Barcelona</span>
            </li>
            <li class="contact-item">
              <i class="mdi mdi-clock-time-four-outline"></i>
              <span>Dilluns - Diumenge: 16:00 - 23:30</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Franja d'Avís Demo Educatiu -->
      <div class="academic-disclaimer-compact">
        <div class="disclaimer-icon">
          <i class="mdi mdi-school-outline"></i>
        </div>
        <div class="disclaimer-text">
          <span class="disclaimer-badge">AVÍS DE PROJECTE DEMO</span>
          <p>
            FilmGalaxy és un projecte educatiu de portfolio (DAW Pedralbes). No és un cinema real ni una web oficial. No es realitza cap cobrament real ni les entrades tenen validesa.
          </p>
        </div>
      </div>

      <!-- Barra Inferior de Copyright & Badges -->
      <div class="footer-bottom">
        <p class="copyright-text">
          &copy; {{ new Date().getFullYear() }} <strong>FilmGalaxy</strong> &bull; Projecte Desenvolupament d'Aplicacions Web
        </p>

        <button 
          type="button"
          class="back-to-top-btn" 
          @click="scrollToTop" 
          title="Tornar a dalt"
          aria-label="Tornar a dalt"
        >
          <i class="mdi mdi-arrow-up"></i>
        </button>
      </div>
    </div>
  </footer>
</template>

<script setup>
const scrollToTop = () => {
  if (typeof window !== 'undefined') {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  }
}
</script>

<style scoped>
.cinema-footer {
  background: #090c18;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  color: #EAE0D5;
  position: relative;
  font-family: inherit;
}

.footer-ambient-line {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, rgba(200, 169, 110, 0.4) 50%, transparent 100%);
}

/* Espaiat global compactat: padding reduït de 4rem a 1.75rem */
.footer-container {
  max-width: 1320px;
  margin: 0 auto;
  padding: 1.75rem 1.25rem 1rem 1.25rem;
}

/* Grid de 4 columnes amb separació ajustada */
.footer-grid {
  display: grid;
  grid-template-columns: 1.25fr 0.9fr 1.1fr 1.15fr;
  gap: 1.5rem;
  margin-bottom: 1.25rem;
}

.footer-col {
  display: flex;
  flex-direction: column;
}

.col-title {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.95rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 0.75rem;
  letter-spacing: 0.3px;
}

.col-title i {
  color: #C8A96E;
  font-size: 1.1rem;
}

/* Logo & Descripció */
.footer-logo-link {
  display: inline-block;
  margin-bottom: 0.5rem;
}

.footer-logo {
  height: 32px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 2px 6px rgba(200, 169, 110, 0.25));
}

.brand-description {
  color: #9A8C98;
  font-size: 0.82rem;
  line-height: 1.45;
  margin-bottom: 0.75rem;
}

.social-links-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.social-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #C9ADA7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.2s ease;
}

.social-icon:hover {
  background: rgba(200, 169, 110, 0.2);
  border-color: #C8A96E;
  color: #F5D78E;
  transform: translateY(-2px);
}

/* Enllaços Explora */
.footer-links-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.footer-link {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  color: #9A8C98;
  text-decoration: none;
  font-size: 0.84rem;
  transition: all 0.2s ease;
}

.footer-link i {
  color: #6c7293;
  font-size: 0.95rem;
  transition: transform 0.2s ease, color 0.2s ease;
}

.footer-link:hover {
  color: #F5D78E;
  transform: translateX(3px);
}

.footer-link:hover i {
  color: #C8A96E;
}

/* Targetes de Tarifes compactades */
.pricing-cards {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}

.price-mini-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 10px;
  padding: 0.5rem 0.75rem;
  transition: all 0.2s ease;
}

.price-mini-card.highlight {
  background: rgba(200, 169, 110, 0.07);
  border-color: rgba(200, 169, 110, 0.25);
}

.price-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.25rem;
  font-size: 0.8rem;
  color: #F2E9E4;
}

.badge-promo {
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 0.1rem 0.4rem;
  border-radius: 9999px;
}

.price-body {
  display: flex;
  gap: 0.85rem;
  font-size: 0.78rem;
  color: #9A8C98;
}

.price-body strong {
  color: #F5D78E;
}

/* Contacte & Horaris */
.contact-info-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 0.45rem;
  color: #9A8C98;
  font-size: 0.82rem;
  line-height: 1.35;
}

.contact-item i {
  color: #C8A96E;
  font-size: 1rem;
  flex-shrink: 0;
  margin-top: 1px;
}

/* Avís Demo: més compacte i elegant */
.academic-disclaimer-compact {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: rgba(200, 169, 110, 0.06);
  border: 1px solid rgba(200, 169, 110, 0.2);
  border-radius: 10px;
  padding: 0.55rem 0.9rem;
  margin-bottom: 1.15rem;
}

.disclaimer-icon i {
  font-size: 1.3rem;
  color: #C8A96E;
}

.disclaimer-text {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.4rem 0.6rem;
}

.disclaimer-badge {
  background: rgba(200, 169, 110, 0.18);
  color: #F5D78E;
  border: 1px solid rgba(200, 169, 110, 0.35);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding: 0.15rem 0.45rem;
  border-radius: 4px;
}

.disclaimer-text p {
  margin: 0;
  color: #D6CCC2;
  font-size: 0.78rem;
  line-height: 1.35;
}

/* Barra Inferior */
.footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 0.85rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 0.78rem;
  color: #6c7293;
}

.copyright-text strong {
  color: #F2E9E4;
}


.back-to-top-btn {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: rgba(200, 169, 110, 0.12);
  border: 1px solid rgba(200, 169, 110, 0.3);
  color: #F5D78E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-to-top-btn:hover {
  background: #C8A96E;
  color: #121526;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(200, 169, 110, 0.25);
}

/* Responsive */
@media (max-width: 1024px) {
  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
  }
}

@media (max-width: 640px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 1.25rem;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 0.75rem;
    text-align: center;
  }

}
</style>
