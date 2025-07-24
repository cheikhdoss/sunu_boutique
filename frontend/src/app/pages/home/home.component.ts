import { Component, OnInit, AfterViewInit, ElementRef, ViewChild, ViewChildren, QueryList, OnDestroy, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { MaterialModule } from '../../material.module';
import { ProductService, Product, Category } from '../../services/product.service';
import { FeaturedProduct } from '../../models/featured-product.interface'; // Chemin corrigé
import { FeaturedProductBannerComponent } from '../../components/featured-product-banner/featured-product-banner.component';
import { CartService } from '../../services/cart.service';
import { SearchService } from '../../services/search.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable, combineLatest, interval, Subscription } from 'rxjs';
import { map } from 'rxjs/operators';
import { SearchBarComponent } from '../../components/search-bar/search-bar.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule, 
    MaterialModule, 
    FormsModule,
    FeaturedProductBannerComponent,
    SearchBarComponent
  ],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css', './home-futuristic.css', './carousel-improvements.css', './featured-title.css']
})
export class HomeComponent implements OnInit, AfterViewInit, OnDestroy {
  @ViewChild('matrixBg') matrixBg!: ElementRef;
  @ViewChild('heroSection') heroSection!: ElementRef;
  @ViewChild('heroTitle') heroTitle!: ElementRef;
  @ViewChild('glitchText') glitchText!: ElementRef;
  @ViewChild('heroSubtitle') heroSubtitle!: ElementRef;
  @ViewChild('heroCta') heroCta!: ElementRef;
  @ViewChild('carouselTrack') carouselTrack!: ElementRef;
  @ViewChild('filtersSection') filtersSection!: ElementRef;
  @ViewChild('productsSection') productsSection!: ElementRef;
  @ViewChild('sectionHeader') sectionHeader!: ElementRef;
  @ViewChild('productsGrid') productsGrid!: ElementRef;
  @ViewChild('featuredTitleSection') featuredTitleSection!: ElementRef;
  @ViewChild('featuredTitleWrapper') featuredTitleWrapper!: ElementRef;
  @ViewChild('featuredTitle') featuredTitle!: ElementRef;
  @ViewChild('featuredSubtitle') featuredSubtitle!: ElementRef;
  @ViewChildren('productCard') productCards!: QueryList<ElementRef>;

  products$: Observable<Product[]>;
  categories$: Observable<Category[]>;
  filteredProducts$: Observable<Product[]>;
  featuredProducts$: Observable<Product[]>;
  loading = true;

  // Carousel properties
  currentSlide = 0;
  slideWidth = 0; // Sera calculé dynamiquement
  maxSlides = 0;
  totalProducts = 0;
  visibleProducts = 3;
  autoplayEnabled = true;
  private autoplayInterval: any;
  isCarouselInitialized = false; // Public pour le debug
  
  // Touch support properties
  private touchStartX = 0;
  private touchEndX = 0;
  private minSwipeDistance = 50;

  // Animation subscriptions
  private animationSubscriptions: Subscription[] = [];
  private matrixParticles: any[] = [];
  private glitchInterval: any;

  // Ajout de la propriété featuredProduct
  featuredProduct: FeaturedProduct = {
    id: 1,
    name: 'iPhone 16 Pro Max 256GB',
    description: 'Le tout nouveau iPhone 16 avec puce A18 Pro, appareil photo révolutionnaire et design en titane.',
    features: ['Écran 6.7\" Super Retina XDR', 'Puce A18 Pro', 'Système photo Pro 48 Mpx', 'Résistance à l\'eau IP68'],
    currentPrice: 1399000,
    oldPrice: 1599000,
    imageUrl: 'http://localhost:8000/storage/products/iphone16.png',
    promotion: 15,
    inStock: true
  };

  constructor(
    private productService: ProductService,
    private cartService: CartService,
    private searchService: SearchService,
    private snackBar: MatSnackBar
  ) {
    this.products$ = this.productService.getProducts();
    this.categories$ = this.productService.getCategories();
    
    this.featuredProducts$ = this.products$.pipe(
      map(products => products.slice(0, 8))
    );
    
    this.filteredProducts$ = combineLatest([
      this.products$,
      this.searchService.selectedCategory$,
      this.searchService.searchTerm$,
      this.searchService.sortBy$
    ]).pipe(
      map(([products, categoryId, searchTerm, sortBy]) => {
        let filtered = products;
        
        // Filtrage par catégorie
        if (categoryId) {
          filtered = filtered.filter(product => product.category_id === categoryId);
        }
        
        // Filtrage par terme de recherche
        if (searchTerm) {
          const searchLower = searchTerm.toLowerCase();
          filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(searchLower) ||
            product.description.toLowerCase().includes(searchLower)
          );
        }
        
        // Tri des produits
        filtered = [...filtered].sort((a, b) => {
          switch (sortBy) {
            case 'name':
              return a.name.localeCompare(b.name);
            case 'price_asc':
              return a.price - b.price;
            case 'price_desc':
              return b.price - a.price;
            case 'newest':
              return new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime();
            default:
              return 0;
          }
        });
        
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
    setTimeout(() => {
      this.initializeAnimations();
      this.initializeCarousel();
      this.calculateSlideWidth();
    }, 100);
  }

  // ===== MÉTHODES DE NAVIGATION DU CARROUSEL =====
  
  nextSlide(): void {
    console.log('🔄 nextSlide appelé');
    console.log('   - currentSlide AVANT:', this.currentSlide);
    console.log('   - maxSlides:', this.maxSlides);
    console.log('   - totalProducts:', this.totalProducts);
    
    this.stopAutoplay();
    
    if (this.currentSlide < this.maxSlides) {
      this.currentSlide++;
    } else {
      this.currentSlide = 0; // Retour au début
    }
    
    console.log('   - currentSlide APRÈS:', this.currentSlide);
    this.moveToSlide();
    
    setTimeout(() => {
      if (this.autoplayEnabled) {
        this.startAutoplay();
      }
    }, 5000);
  }

  previousSlide(): void {
    console.log('🔄 previousSlide appelé');
    console.log('   - currentSlide AVANT:', this.currentSlide);
    console.log('   - maxSlides:', this.maxSlides);
    
    this.stopAutoplay();
    
    if (this.currentSlide > 0) {
      this.currentSlide--;
    } else {
      this.currentSlide = this.maxSlides; // Aller à la fin
    }
    
    console.log('   - currentSlide APRÈS:', this.currentSlide);
    this.moveToSlide();
    
    setTimeout(() => {
      if (this.autoplayEnabled) {
        this.startAutoplay();
      }
    }, 5000);
  }

  goToSlide(slideIndex: number): void {
    console.log('🎯 goToSlide appelé avec index:', slideIndex);
    
    this.stopAutoplay();
    this.currentSlide = Math.max(0, Math.min(slideIndex, this.maxSlides));
    console.log('🎯 Slide sélectionné:', this.currentSlide);
    this.moveToSlide();
    
    setTimeout(() => {
      if (this.autoplayEnabled) {
        this.startAutoplay();
      }
    }, 5000);
  }

  @HostListener('window:resize', ['$event'])
  onResize(event?: Event) {
    this.calculateSlideWidth();
    this.moveToSlide(false); // Recalculer la position sans animation
  }

  private calculateSlideWidth(): void {
    if (this.carouselTrack && this.carouselTrack.nativeElement) {
      const track = this.carouselTrack.nativeElement;
      const firstSlide = track.querySelector('.carousel-slide');
      if (firstSlide) {
        const slideStyle = window.getComputedStyle(firstSlide);
        const marginRight = parseFloat(slideStyle.marginRight) || 0;
        this.slideWidth = firstSlide.offsetWidth + marginRight;
        console.log(`Recalculated slide width: ${this.slideWidth}px`);
      }
    }
  }

  private moveToSlide(animate = true): void {
    if (!this.carouselTrack || !this.carouselTrack.nativeElement) {
      // console.error('❌ Carousel track non trouvé ou pas encore initialisé.');
      return;
    }
    const track = this.carouselTrack.nativeElement;

    if (this.slideWidth === 0) {
        console.warn('⚠️ slideWidth est 0, le calcul de la translation sera incorrect.');
    }

    const translateX = -(this.currentSlide * this.slideWidth);

    // console.log('🎬 DÉFILEMENT DÉTAILLÉ:');
    // console.log(`   - Slide: ${this.currentSlide}, Width: ${this.slideWidth}px, TranslateX: ${translateX}px`);

    track.style.transition = animate ? 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)' : 'none';
    track.style.transform = `translateX(${translateX}px)`;
  }

  getSlideIndicators(): number[] {
    const indicators = Array(this.maxSlides + 1).fill(0).map((_, i) => i);
    console.log('📍 Indicateurs générés:', indicators);
    return indicators;
  }

  // ===== INITIALISATION DU CARROUSEL =====
  
  private initializeCarousel(): void {
    this.featuredProducts$.subscribe(products => {
      if (products.length > 0 && !this.isCarouselInitialized) {
        this.totalProducts = products.length;
        this.maxSlides = Math.max(0, this.totalProducts - this.visibleProducts);
        this.isCarouselInitialized = true;
        
        console.log('🔧 INITIALISATION CARROUSEL:');
        console.log('   - Produits total:', this.totalProducts);
        console.log('   - Produits visibles:', this.visibleProducts);
        console.log('   - Slides possibles (maxSlides):', this.maxSlides);
        console.log('   - Positions: 0 à', this.maxSlides);
        console.log('   - Largeur par slide:', this.slideWidth + 'px');
        
        if (this.maxSlides === 0) {
          console.log('⚠️ ATTENTION: maxSlides = 0, pas de défilement possible');
        }
        
        setTimeout(() => {
          this.startJavaScriptCarousel();
          console.log('✅ Carrousel initialisé');
        }, 2000);
      }
    });
  }

  private startJavaScriptCarousel(): void {
    const track = document.querySelector('.carousel-track') as HTMLElement;
    if (!track) {
      console.log('⏳ Track non trouvé, retry dans 1s');
      setTimeout(() => this.startJavaScriptCarousel(), 1000);
      return;
    }

    console.log('🚀 Track trouvé, démarrage du carrousel');
    
    // Vérifier les dimensions du track
    const trackRect = track.getBoundingClientRect();
    console.log('📏 Dimensions du track:', {
      width: trackRect.width,
      height: trackRect.height,
      children: track.children.length
    });
    
    const moveCarousel = () => {
      if (this.maxSlides === 0) {
        console.log('⏸️ Autoplay arrêté: pas de slides à défiler');
        return;
      }
      
      if (this.currentSlide < this.maxSlides) {
        this.currentSlide++;
      } else {
        this.currentSlide = 0;
      }
      
      const translateX = -(this.currentSlide * this.slideWidth);
      track.style.transition = 'transform 0.5s ease-in-out';
      track.style.transform = `translateX(${translateX}px)`;
      
      console.log('🔄 Auto-slide:', this.currentSlide, 'Position:', translateX);
    };

    if (this.autoplayEnabled && this.maxSlides > 0) {
      this.autoplayInterval = setInterval(moveCarousel, 3000);
      console.log('▶️ Autoplay démarré');
    } else {
      console.log('⏸️ Autoplay non démarré (maxSlides:', this.maxSlides + ')');
    }
  }

  startAutoplay(): void {
    this.stopAutoplay();
    if (this.autoplayEnabled && this.maxSlides > 0) {
      const track = document.querySelector('.carousel-track') as HTMLElement;
      if (track) {
        this.autoplayInterval = setInterval(() => {
          this.nextSlide();
        }, 3000);
      }
    }
  }

  stopAutoplay(): void {
    if (this.autoplayInterval) {
      clearInterval(this.autoplayInterval);
      this.autoplayInterval = null;
    }
  }

  toggleAutoplay(): void {
    this.autoplayEnabled = !this.autoplayEnabled;
    if (this.autoplayEnabled) {
      this.startAutoplay();
    } else {
      this.stopAutoplay();
    }
  }

  // ===== SUPPORT TACTILE =====
  
  onTouchStart(event: TouchEvent): void {
    this.touchStartX = event.touches[0].clientX;
  }

  onTouchEnd(event: TouchEvent): void {
    this.touchEndX = event.changedTouches[0].clientX;
    this.handleSwipe();
  }

  private handleSwipe(): void {
    const swipeDistance = this.touchStartX - this.touchEndX;
    
    if (Math.abs(swipeDistance) > this.minSwipeDistance) {
      if (swipeDistance > 0) {
        this.nextSlide();
      } else {
        this.previousSlide();
      }
    }
  }

  // ===== MÉTHODES HELPER =====
  
  isNewProduct(product: Product): boolean {
    if (!product.created_at) return false;
    const createdDate = new Date(product.created_at);
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    return createdDate > thirtyDaysAgo;
  }

  formatPrice(price: number): string {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'XOF',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(price);
  }

  trackByProductId(index: number, product: Product): number {
    return product.id;
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

  // ===== ACTIONS PRODUITS =====
  
  addToCart(product: Product, event: Event): void {
    event.stopPropagation();
    if (product.stock > 0) {
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

  // ===== ÉVÉNEMENTS HOVER =====
  
  onCardHover(productId: number, isHovered: boolean, event: Event): void {
    const card = event.currentTarget as HTMLElement;
    
    if (isHovered) {
      card.style.transform = 'perspective(1000px) rotateX(-5deg) rotateY(5deg) translateY(-10px) scale(1.02)';
      card.style.boxShadow = '0 20px 40px rgba(63, 164, 238, 0.4), 0 0 20px rgba(233, 30, 99, 0.3)';
      
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
      button.style.transform = 'scale(1.05)';
      button.style.boxShadow = '0 8px 20px rgba(63, 164, 238, 0.4)';
    } else {
      button.style.transform = 'scale(1)';
      button.style.boxShadow = 'none';
    }
  }

  onCarouselCardHover(productId: number, isHovered: boolean, event: Event): void {
    const card = event.currentTarget as HTMLElement;
    
    if (isHovered) {
      this.stopAutoplay();
      card.style.transform = 'translateY(-10px) scale(1.05)';
      card.style.boxShadow = '0 15px 35px rgba(63, 164, 238, 0.4), 0 0 20px rgba(233, 30, 99, 0.3)';
    } else {
      if (this.autoplayEnabled) {
        this.startAutoplay();
      }
      card.style.transform = 'translateY(0) scale(1)';
      card.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
    }
  }

  // ===== ANIMATIONS =====
  
  private initializeAnimations(): void {
    this.createMatrixEffect();
    this.animateHeroSection();
    this.animateGlitchText();
    this.setupFeaturedTitleAnimation();
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
      element.style.transform = `translate(${Math.random() * 4 - 2}px, ${Math.random() * 4 - 2}px)`;
      element.style.filter = `hue-rotate(${Math.random() * 360}deg)`;
      
      setTimeout(() => {
        element.style.transform = 'translate(0, 0)';
        element.style.filter = 'hue-rotate(0deg)';
      }, 100);
    }, 3000);
  }

  private animateProductCards(): void {
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

  private setupFeaturedTitleAnimation(): void {
    if (!this.featuredTitleSection) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in");
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3, rootMargin: "-50px 0px" });

    observer.observe(this.featuredTitleSection.nativeElement);
  }

  ngOnDestroy(): void {
    this.animationSubscriptions.forEach(sub => sub.unsubscribe());
    if (this.glitchInterval) {
      clearInterval(this.glitchInterval);
    }
    if (this.autoplayInterval) {
      clearInterval(this.autoplayInterval);
    }
  }
}