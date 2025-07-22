import { Component, OnInit, AfterViewInit, ElementRef, ViewChild, ViewChildren, QueryList, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { ProductService, Product, Category } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { SearchService } from '../../services/search.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable, combineLatest, interval, Subscription } from 'rxjs';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css', './home-futuristic.css']
})
export class HomeComponent implements OnInit, AfterViewInit, OnDestroy {
  @ViewChild('matrixBg') matrixBg!: ElementRef;
  @ViewChild('heroSection') heroSection!: ElementRef;
  @ViewChild('heroTitle') heroTitle!: ElementRef;
  @ViewChild('glitchText') glitchText!: ElementRef;
  @ViewChild('heroSubtitle') heroSubtitle!: ElementRef;
  @ViewChild('heroCta') heroCta!: ElementRef;
  @ViewChild('filtersSection') filtersSection!: ElementRef;
  @ViewChild('productsSection') productsSection!: ElementRef;
  @ViewChild('sectionHeader') sectionHeader!: ElementRef;
  @ViewChild('productsGrid') productsGrid!: ElementRef;
  @ViewChildren('productCard') productCards!: QueryList<ElementRef>;

  products$: Observable<Product[]>;
  categories$: Observable<Category[]>;
  filteredProducts$: Observable<Product[]>;
  loading = true;

  // Animation subscriptions
  private animationSubscriptions: Subscription[] = [];
  private matrixParticles: any[] = [];
  private glitchInterval: any;

  constructor(
    private productService: ProductService,
    private cartService: CartService,
    private searchService: SearchService,
    private snackBar: MatSnackBar
  ) {
    this.products$ = this.productService.getProducts();
    this.categories$ = this.productService.getCategories();
    
    this.filteredProducts$ = combineLatest([
      this.products$,
      this.searchService.selectedCategory$,
      this.searchService.searchTerm$
    ]).pipe(
      map(([products, categoryId, searchTerm]) => {
        let filtered = products;
        
        if (categoryId) {
          filtered = filtered.filter(product => product.category_id === categoryId);
        }
        
        if (searchTerm) {
          filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            product.description.toLowerCase().includes(searchTerm.toLowerCase())
          );
        }
        
        return filtered;
      })
    );
  }

  ngOnInit(): void {
    this.products$.subscribe((products) => {
      this.loading = false;
      console.log('Produits chargés:', products);
    });
  }

  ngAfterViewInit(): void {
    // Démarrer les animations après que la vue soit initialisée
    setTimeout(() => {
      this.initializeAnimations();
    }, 100);
  }

  ngOnDestroy(): void {
    // Nettoyer les animations
    this.animationSubscriptions.forEach(sub => sub.unsubscribe());
    if (this.glitchInterval) {
      clearInterval(this.glitchInterval);
    }
  }

  private initializeAnimations(): void {
    this.createMatrixEffect();
    this.animateHeroSection();
    this.animateGlitchText();
    this.animateProductCards();
    this.createEnergyBorders();
    this.animateNeonText();
  }

  private createMatrixEffect(): void {
    if (!this.matrixBg) return;

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.zIndex = '-1';
    canvas.style.pointerEvents = 'none';
    
    this.matrixBg.nativeElement.appendChild(canvas);

    // Créer des particules
    for (let i = 0; i < 50; i++) {
      this.matrixParticles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        size: Math.random() * 3 + 1,
        speedX: (Math.random() - 0.5) * 0.5,
        speedY: Math.random() * 1 + 0.5,
        opacity: Math.random() * 0.5 + 0.2,
        color: Math.random() > 0.5 ? '#3fa4ee' : '#e91e63'
      });
    }

    const animateMatrix = () => {
      if (!ctx) return;
      
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      this.matrixParticles.forEach(particle => {
        particle.x += particle.speedX;
        particle.y += particle.speedY;
        
        if (particle.y > canvas.height) {
          particle.y = -10;
          particle.x = Math.random() * canvas.width;
        }
        
        ctx.globalAlpha = particle.opacity;
        ctx.fillStyle = particle.color;
        ctx.beginPath();
        ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        ctx.fill();
      });
      
      requestAnimationFrame(animateMatrix);
    };
    
    animateMatrix();
  }

  private animateHeroSection(): void {
    if (!this.heroSection) return;

    // Animation d'apparition en cascade
    const elements = [this.heroTitle, this.heroSubtitle, this.heroCta];
    
    elements.forEach((element, index) => {
      if (element) {
        const el = element.nativeElement;
        el.style.opacity = '0';
        el.style.transform = 'translateY(50px)';
        
        setTimeout(() => {
          el.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        }, index * 200);
      }
    });

    // Effet de pulsation sur le bouton CTA
    if (this.heroCta) {
      const button = this.heroCta.nativeElement;
      
      button.addEventListener('mouseenter', () => {
        button.style.transform = 'scale(1.1) translateY(-5px)';
        button.style.boxShadow = '0 10px 30px rgba(63, 164, 238, 0.6), 0 0 20px rgba(233, 30, 99, 0.4)';
      });
      
      button.addEventListener('mouseleave', () => {
        button.style.transform = 'scale(1) translateY(0)';
        button.style.boxShadow = '0 4px 15px rgba(63, 164, 238, 0.3)';
      });
    }
  }

  private animateGlitchText(): void {
    if (!this.glitchText) return;

    const element = this.glitchText.nativeElement;
    
    this.glitchInterval = setInterval(() => {
      // Effet de glitch aléatoire
      element.style.transform = `translate(${Math.random() * 4 - 2}px, ${Math.random() * 4 - 2}px)`;
      element.style.filter = `hue-rotate(${Math.random() * 360}deg)`;
      
      setTimeout(() => {
        element.style.transform = 'translate(0, 0)';
        element.style.filter = 'hue-rotate(0deg)';
      }, 100);
    }, 3000);
  }

  private animateProductCards(): void {
    // Observer pour animer les cartes quand elles entrent dans la vue
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          const card = entry.target as HTMLElement;
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
          }, index * 100);
        }
      });
    }, { threshold: 0.1 });

    // Observer les cartes produits
    setTimeout(() => {
      const cards = document.querySelectorAll('.product-card');
      cards.forEach(card => {
        const htmlCard = card as HTMLElement;
        htmlCard.style.opacity = '0';
        htmlCard.style.transform = 'translateY(30px) scale(0.9)';
        htmlCard.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        observer.observe(card);
      });
    }, 500);
  }

  private createEnergyBorders(): void {
    const energyElements = document.querySelectorAll('.energy-border');
    
    energyElements.forEach(element => {
      const htmlElement = element as HTMLElement;
      
      // Créer l'effet de bordure énergétique
      const createEnergyEffect = () => {
        htmlElement.style.boxShadow = `
          0 0 10px rgba(63, 164, 238, ${Math.random() * 0.5 + 0.3}),
          0 0 20px rgba(233, 30, 99, ${Math.random() * 0.3 + 0.2}),
          inset 0 0 10px rgba(63, 164, 238, 0.1)
        `;
      };
      
      setInterval(createEnergyEffect, 2000);
    });
  }

  private animateNeonText(): void {
    const neonElements = document.querySelectorAll('.neon-glow');
    
    neonElements.forEach(element => {
      const htmlElement = element as HTMLElement;
      
      const pulseNeon = () => {
        const intensity = Math.sin(Date.now() * 0.003) * 0.3 + 0.7;
        htmlElement.style.textShadow = `
          0 0 5px rgba(63, 164, 238, ${intensity}),
          0 0 10px rgba(63, 164, 238, ${intensity * 0.8}),
          0 0 15px rgba(63, 164, 238, ${intensity * 0.6}),
          0 0 20px rgba(63, 164, 238, ${intensity * 0.4})
        `;
      };
      
      const animationSub = interval(50).subscribe(pulseNeon);
      this.animationSubscriptions.push(animationSub);
    });
  }

  
  addToCart(product: Product, event?: Event): void {
    if (product.stock > 0) {
      // Animation du bouton
      if (event) {
        const button = event.target as HTMLElement;
        button.style.transform = 'scale(0.95)';
        button.style.boxShadow = '0 0 20px rgba(63, 164, 238, 0.8), inset 0 0 10px rgba(0, 0, 0, 0.2)';
        
        setTimeout(() => {
          button.style.transform = 'scale(1)';
          button.style.boxShadow = '';
        }, 150);
      }

      this.cartService.addToCart(product, 1);
      this.snackBar.open(`${product.name} ajouté au panier`, 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    } else {
      this.snackBar.open('Produit en rupture de stock', 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    }
  }

  onCardHover(productId: number, isHovered: boolean, event: Event): void {
    const card = event.currentTarget as HTMLElement;
    
    if (isHovered) {
      card.style.transform = 'perspective(1000px) rotateX(-5deg) rotateY(5deg) translateY(-10px) scale(1.02)';
      card.style.boxShadow = '0 20px 40px rgba(63, 164, 238, 0.4), 0 0 20px rgba(233, 30, 99, 0.3)';
      
      // Effet de scan holographique
      const overlay = document.createElement('div');
      overlay.className = 'hologram-scan';
      overlay.style.cssText = `
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(63, 164, 238, 0.3), transparent);
        animation: scan 0.6s ease-out;
        pointer-events: none;
        z-index: 10;
      `;
      
      card.style.position = 'relative';
      card.appendChild(overlay);
      
      setTimeout(() => {
        if (overlay.parentNode) {
          overlay.parentNode.removeChild(overlay);
        }
      }, 600);
      
    } else {
      card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale(1)';
      card.style.boxShadow = '0 4px 20px rgba(63, 164, 238, 0.2)';
    }
  }

  onButtonHover(productId: number, isHovered: boolean, event: Event): void {
    const button = event.currentTarget as HTMLElement;
    
    if (isHovered) {
      button.style.transform = 'scale(1.05) translateY(-2px)';
      button.style.boxShadow = '0 8px 25px rgba(63, 164, 238, 0.5), 0 0 20px rgba(233, 30, 99, 0.3)';
    } else {
      button.style.transform = 'scale(1) translateY(0)';
      button.style.boxShadow = '0 4px 15px rgba(63, 164, 238, 0.3)';
    }
  }

  getImageUrl(imagePath: string): string {
    if (!imagePath) return '/assets/images/placeholder.svg';
    if (imagePath.startsWith('http')) return imagePath;
    
    if (imagePath.startsWith('products/')) {
      return `http://localhost:8000/storage/${imagePath}`;
    } else {
      return `http://localhost:8000/storage/products/${imagePath}`;
    }
  }

  trackByProductId(index: number, product: Product): number {
    return product.id;
  }
}