// Méthodes d'animation pour la section "En Vedette"

export function animateFeaturedTitleSection(featuredTitleSection: any, featuredTitle: any, featuredSubtitle: any, animationSubscriptions: any[]) {
  if (!featuredTitleSection) return;

  // Configuration de l'Intersection Observer pour déclencher l'animation au scroll
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        triggerFeaturedTitleAnimation(featuredTitle, featuredSubtitle, animationSubscriptions);
        observer.unobserve(entry.target); // Animation une seule fois
      }
    });
  }, { 
    threshold: 0.3, // Déclenche quand 30% de l'élément est visible
    rootMargin: '-50px 0px' // Déclenche un peu avant que l'élément soit complètement visible
  });

  observer.observe(featuredTitleSection.nativeElement);
}

function triggerFeaturedTitleAnimation(featuredTitle: any, featuredSubtitle: any, animationSubscriptions: any[]) {
  if (!featuredTitle || !featuredSubtitle) return;

  const title = featuredTitle.nativeElement;
  const subtitle = featuredSubtitle.nativeElement;

  // État initial - invisible et décalé
  title.style.opacity = '0';
  title.style.transform = 'translateY(100px) scale(0.8)';
  subtitle.style.opacity = '0';
  subtitle.style.transform = 'translateY(50px)';

  // Animation du titre principal avec effet Apple
  setTimeout(() => {
    title.style.transition = 'all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
    title.style.opacity = '1';
    title.style.transform = 'translateY(0) scale(1)';
    
    // Effet de brillance qui traverse le texte
    const shimmer = document.createElement('div');
    shimmer.className = 'title-shimmer';
    shimmer.style.cssText = `
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      animation: shimmer 1.5s ease-out;
      pointer-events: none;
      z-index: 10;
    `;
    
    title.style.position = 'relative';
    title.appendChild(shimmer);
    
    setTimeout(() => {
      if (shimmer.parentNode) {
        shimmer.parentNode.removeChild(shimmer);
      }
    }, 1500);
  }, 200);

  // Animation du sous-titre
  setTimeout(() => {
    subtitle.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
    subtitle.style.opacity = '1';
    subtitle.style.transform = 'translateY(0)';
  }, 600);

  // Effet de pulsation continue pour le titre
  setTimeout(() => {
    const pulseTitle = () => {
      const intensity = Math.sin(Date.now() * 0.002) * 0.1 + 0.9;
      title.style.transform = `translateY(0) scale(${intensity})`;
    };
    
    const pulseAnimation = setInterval(pulseTitle, 50);
    // Ajouter à la liste des animations pour nettoyage
    animationSubscriptions.push({ unsubscribe: () => clearInterval(pulseAnimation) });
  }, 1500);
}