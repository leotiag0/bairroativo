/* ===== ESTILOS DA PÁGINA INICIAL ===== */
.home-page {
    --hero-bg: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
}

.hero-section {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-xl);
    padding: var(--space-xl) 0;
    align-items: center;
}

.hero-content {
    order: 2;
}

.hero-image {
    order: 1;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.hero-section h1 {
    font-size: 2.5rem;
    color: var(--primary-dark);
    margin-bottom: var(--space-md);
}

.lead {
    font-size: 1.25rem;
    color: var(--text-medium);
    margin-bottom: var(--space-lg);
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: var(--space-md);
    flex-wrap: wrap;
}

.imagem-explicativa {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius-lg);
    transition: transform 0.3s ease;
}

.imagem-explicativa:hover {
    transform: scale(1.02);
}

.features-section {
    padding: var(--space-xl) 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
}

.section-title {
    text-align: center;
    font-size: 2rem;
    margin-bottom: var(--space-sm);
    color: var(--primary-dark);
}

.section-subtitle {
    text-align: center;
    color: var(--text-medium);
    margin-bottom: var(--space-xl);
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--space-lg);
    margin-top: var(--space-xl);
}

.feature-card {
    background: var(--bg-white);
    border-radius: var(--border-radius-lg);
    padding: var(--space-lg);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.card-icon {
    font-size: 2.5rem;
    margin-bottom: var(--space-md);
}

.feature-card h3 {
    font-size: 1.5rem;
    margin-bottom: var(--space-sm);
    color: var(--primary-color);
}

.feature-card p {
    margin-bottom: var(--space-lg);
    flex-grow: 1;
    color: var(--text-medium);
}

.btn-outline {
    background: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    align-self: flex-start;
}

.btn-outline:hover {
    background: var(--primary-light);
}

.btn-geolocation {
    background: var(--primary-dark);
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
}

.localizacao-section {
    padding: var(--space-xl) 0;
    text-align: center;
}

.localizacao-content {
    max-width: 800px;
    margin: 0 auto;
}

.localizacao-content p {
    margin-bottom: var(--space-lg);
    color: var(--text-medium);
}

/* Responsividade */
@media (min-width: 992px) {
    .hero-section {
        grid-template-columns: 1fr 1fr;
    }
    
    .hero-content {
        order: 1;
    }
    
    .hero-image {
        order: 2;
    }
}

@media (max-width: 576px) {
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
}
