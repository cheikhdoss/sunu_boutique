import { Component, Input, OnInit, OnDestroy, ElementRef, ViewChild, AfterViewInit, Inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { FeaturedProduct } from '../../models/featured-product.interface';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatSnackBarModule, MatSnackBar } from '@angular/material/snack-bar';
import { CurrencyPipe } from '@angular/common';
import { Router } from '@angular/router';
import { CartService } from '../../services/cart.service';
import { Product } from '../../services/product.service';

@Component({
  selector: 'app-featured-product-banner',
  templateUrl: './featured-product-banner.component.html',
  styleUrls: ['./featured-product-banner.component.css'],
  standalone: true,
  imports: [
    CommonModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatSnackBarModule,
    CurrencyPipe
  ]
})
export class FeaturedProductBannerComponent implements OnInit, AfterViewInit, OnDestroy {
  @Input() product!: FeaturedProduct;
  @ViewChild('ctaButton') ctaButton!: ElementRef;
  
  private intersectionObserver!: IntersectionObserver;
  private animationInterval: any;
  private isBrowser: boolean;
  
  constructor(
    private snackBar: MatSnackBar,
    private router: Router,
    private elementRef: ElementRef,
    private cartService: CartService,
    @Inject(PLATFORM_ID) platformId: Object
  ) {
    this.isBrowser = isPlatformBrowser(platformId);
  }

  ngOnInit() {
    if (this.isBrowser) {
      // Initialisation des animations au scroll
      this.setupScrollAnimations();
    }
  }

  ngAfterViewInit() {
    if (this.isBrowser) {
      // Animation du bouton après le rendu
      setTimeout(() => {
        this.startButtonAnimation();
      }, 1000);
    }
  }

  ngOnDestroy() {
    if (this.isBrowser) {
      if (this.intersectionObserver) {
        this.intersectionObserver.disconnect();
      }
      if (this.animationInterval) {
        clearInterval(this.animationInterval);
      }
    }
  }

  private setupScrollAnimations() {
    if (!this.isBrowser) return;

    this.intersectionObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            // Animation d'entrée
            this.animateOnScroll();
          }
        });
      },
      {
        threshold: 0.3,
        rootMargin: '0px 0px -100px 0px'
      }
    );

    this.intersectionObserver.observe(this.elementRef.nativeElement);
  }

  private animateOnScroll() {
    if (!this.isBrowser) return;

    const banner = this.elementRef.nativeElement.querySelector('.featured-banner');
    const textContent = this.elementRef.nativeElement.querySelector('.text-content');
    const imageContainer = this.elementRef.nativeElement.querySelector('.image-container');
    
    if (banner && textContent && imageContainer) {
      // Animation du banner principal
      banner.style.opacity = '0';
      banner.style.transform = 'translateY(50px)';
      banner.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
      
      setTimeout(() => {
        banner.style.opacity = '1';
        banner.style.transform = 'translateY(0)';
      }, 100);

      // Animation du contenu texte (de la gauche)
      textContent.style.opacity = '0';
      textContent.style.transform = 'translateX(-100px)';
      textContent.style.transition = 'all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
      
      setTimeout(() => {
        textContent.style.opacity = '1';
        textContent.style.transform = 'translateX(0)';
      }, 300);

      // Animation de l'image (de la droite)
      imageContainer.style.opacity = '0';
      imageContainer.style.transform = 'translateX(100px) scale(0.8)';
      imageContainer.style.transition = 'all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
      
      setTimeout(() => {
        imageContainer.style.opacity = '1';
        imageContainer.style.transform = 'translateX(0) scale(1)';
      }, 500);
    }
  }

  private startButtonAnimation() {
    if (!this.isBrowser) return;

    // Animation pulsante du bouton toutes les 5 secondes
    this.animationInterval = setInterval(() => {
      const button = this.elementRef.nativeElement.querySelector('.cta-button');
      if (button) {
        button.classList.add('animate-pulse');
        setTimeout(() => {
          button.classList.remove('animate-pulse');
        }, 2000);
      }
    }, 5000);
  }

  addToCart() {
    if (!this.product.inStock) {
      this.snackBar.open('Produit en rupture de stock', 'Fermer', {
        duration: 3000,
        horizontalPosition: 'center',
        verticalPosition: 'top',
        panelClass: ['error-snackbar']
      });
      return;
    }

    // Animation du bouton au clic
    if (this.isBrowser) {
      const button = this.elementRef.nativeElement.querySelector('.cta-button');
      if (button) {
        button.style.transform = 'scale(0.95)';
        button.style.transition = 'transform 0.1s ease';
        
        setTimeout(() => {
          button.style.transform = 'scale(1)';
        }, 100);
      }
    }

    // Convertir FeaturedProduct en Product pour le CartService
    const productForCart: Product = {
      id: this.product.id,
      name: this.product.name,
      description: this.product.description,
      price: this.product.currentPrice,
      image: this.product.imageUrl,
      stock: this.product.inStock ? 10 : 0, // Valeur par défaut
      category_id: 1, // Valeur par défaut
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };

    // Ajout réel au panier
    this.cartService.addToCart(productForCart, 1);
    console.log('Product added to cart:', this.product);
    
    // Affichage du message de succès
    this.snackBar.open(
      `${this.product.name} ajouté au panier avec succès!`, 
      'Voir le panier', 
      {
        duration: 4000,
        horizontalPosition: 'center',
        verticalPosition: 'top',
        panelClass: ['success-snackbar']
      }
    ).onAction().subscribe(() => {
      // Redirection vers le panier
      this.router.navigate(['/cart']);
    });

    // Effet de particules
    if (this.isBrowser) {
      this.createParticleEffect();
    }
  }

  private createParticleEffect() {
    if (!this.isBrowser) return;

    const button = this.elementRef.nativeElement.querySelector('.cta-button');
    if (!button) return;

    const rect = button.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;

    // Créer des particules
    for (let i = 0; i < 12; i++) {
      const particle = document.createElement('div');
      particle.style.cssText = `
        position: fixed;
        width: 6px;
        height: 6px;
        background: linear-gradient(45deg, #3fa4ee, #e91e63);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        left: ${centerX}px;
        top: ${centerY}px;
      `;

      document.body.appendChild(particle);

      const angle = (i / 12) * Math.PI * 2;
      const velocity = 100 + Math.random() * 50;
      const vx = Math.cos(angle) * velocity;
      const vy = Math.sin(angle) * velocity;

      let x = 0, y = 0, opacity = 1;
      const animate = () => {
        x += vx * 0.02;
        y += vy * 0.02 + 2; // Gravité
        opacity -= 0.02;

        particle.style.transform = `translate(${x}px, ${y}px)`;
        particle.style.opacity = opacity.toString();

        if (opacity > 0) {
          requestAnimationFrame(animate);
        } else {
          if (document.body.contains(particle)) {
            document.body.removeChild(particle);
          }
        }
      };

      requestAnimationFrame(animate);
    }
  }

  onButtonHover(isHovered: boolean) {
    if (!this.isBrowser) return;

    const button = this.elementRef.nativeElement.querySelector('.cta-button');
    if (!button) return;

    if (isHovered) {
      // Arrêter l'animation automatique pendant le hover
      if (this.animationInterval) {
        clearInterval(this.animationInterval);
      }
    } else {
      // Reprendre l'animation automatique après le hover
      setTimeout(() => {
        this.startButtonAnimation();
      }, 2000);
    }
  }

  // Méthode pour tester le bouton
  testButton() {
    console.log('Test button clicked!');
    this.addToCart();
  }
}
