import { Component, OnInit, AfterViewInit, ElementRef, ViewChild, ViewChildren, QueryList, OnDestroy, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { ProductService, Product, Category } from '../../services/product.service';
import { FeaturedProduct } from '../../models/featured-product.interface';
import { SimpleFeaturedBannerComponent } from '../../components/simple-featured-banner/simple-featured-banner.component';
import { CartService } from '../../services/cart.service';
import { SearchService } from '../../services/search.service';
import { NotificationService } from '../../services/notification.service';
import { Observable, combineLatest, interval, Subscription } from 'rxjs';
import { map } from 'rxjs/operators';
import { ProductSearchBarComponent } from '../../components/product-search-bar/product-search-bar.component';
import { MatChip } from '@angular/material/chips';
import { MatSnackBar } from '@angular/material/snack-bar';
import { CustomNotificationService } from '../../services/custom-notification.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    MaterialModule,
    SimpleFeaturedBannerComponent,
    ProductSearchBarComponent,
    MatChip
  ],
  templateUrl: './home.component.html',
  styleUrls: [
    './home.component.css', 
    './home-futuristic.css', 
    './carousel-improvements.css', 
    './featured-title.css'
  ]
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
  slideWidth = 0;
  maxSlides = 0;
  totalProducts = 0;
  visibleProducts = 3;
  autoplayEnabled = true;
  private autoplayInterval: any;
  isCarouselInitialized = false;

  // Touch support properties
  private touchStartX = 0;
  private touchEndX = 0;
  private minSwipeDistance = 50;

  // Animation subscriptions
  private animationSubscriptions: Subscription[] = [];
  private matrixParticles: any[] = [];
  private glitchInterval: any;

  // Produit vedette récupéré depuis la base de données
  featuredProduct: FeaturedProduct | null = null;

  constructor(
    private productService: ProductService,
    private cartService: CartService,
    private searchService: SearchService,
    private notificationService: NotificationService,
    private snackBar: MatSnackBar,
    private customNotification: CustomNotificationService,
    private router: Router
  ) {
    this.products$ = this.productService.getProducts();
    this.categories$ = this.productService.getCategories();

    this.featuredProducts$ = this.products$.pipe(
      map(products => products.slice(0, 8))
    );

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
      
      // Récupérer l'iPhone 16 Pro Max depuis la base de données
      const iphone = products.find(p => p.name.includes('iPhone 16 Pro Max'));
      if (iphone) {
        console.log('📱 iPhone trouvé dans la base:', iphone);
        
        this.featuredProduct = {
          id: iphone.id,
          name: iphone.name,
          description: iphone.description,
          features: ['Écran 6.7" Super Retina XDR', 'Puce A18 Pro', 'Système photo Pro 48 Mpx', 'Résistance à l\'eau IP68'],
          currentPrice: iphone.price,
          oldPrice: iphone.price * 1.2, // Prix ancien simulé (20% plus cher)
          imageUrl: this.getImageUrl(iphone.image),
          promotion: 15,
          inStock: true // Forcé à true pour les tests
        };
        
        console.log('✅ Produit vedette créé depuis la base:', this.featuredProduct);
      } else {
        console.warn('⚠️ Aucun iPhone 16 Pro Max trouvé dans les produits');
        
        // Si aucun iPhone trouvé, chercher le premier produit disponible
        if (products.length > 0) {
          const firstProduct = products[0];
          console.log('📱 Utilisation du premier produit disponible:', firstProduct);
          
          this.featuredProduct = {
            id: firstProduct.id,
            name: firstProduct.name,
            description: firstProduct.description,
            features: ['Produit de qualité', 'Livraison rapide', 'Garantie incluse', 'Support client'],
            currentPrice: firstProduct.price,
            oldPrice: firstProduct.price * 1.15,
            imageUrl: this.getImageUrl(firstProduct.image),
            promotion: 10,
            inStock: true // Forcé à true pour les tests
          };
          
          console.log('✅ Produit vedette créé avec le premier produit:', this.featuredProduct);
        }
      }
    });
  }

  ngAfterViewInit(): void {
    setTimeout(() => {
      this.initializeAnimations();
      this.initializeCarousel();
      this.calculateSlideWidth();
    }, 100);
  }

  // Méthodes simplifiées pour éviter les erreurs
  nextSlide(): void {
    this.stopAutoplay();
    if (this.currentSlide < this.maxSlides) {
      this.currentSlide++;
    } else {
      this.currentSlide = 0;
    }
    this.moveToSlide();
  }

  previousSlide(): void {
    this.stopAutoplay();
    if (this.currentSlide > 0) {
      this.currentSlide--;
    } else {
      this.currentSlide = this.maxSlides;
    }
    this.moveToSlide();
  }

  goToSlide(slideIndex: number): void {
    this.stopAutoplay();
    this.currentSlide = Math.max(0, Math.min(slideIndex, this.maxSlides));
    this.moveToSlide();
  }

  @HostListener('window:resize', ['$event'])
  onResize(event?: Event) {
    this.calculateSlideWidth();
    this.moveToSlide(false);
  }

  private calculateSlideWidth(): void {
    if (this.carouselTrack && this.carouselTrack.nativeElement) {
      const track = this.carouselTrack.nativeElement;
      const firstSlide = track.querySelector('.carousel-slide');
      if (firstSlide) {
        const slideStyle = window.getComputedStyle(firstSlide);
        const marginRight = parseFloat(slideStyle.marginRight) || 0;
        this.slideWidth = firstSlide.offsetWidth + marginRight;
      }
    }
  }

  private moveToSlide(animate = true): void {
    if (!this.carouselTrack || !this.carouselTrack.nativeElement) {
      return;
    }
    const track = this.carouselTrack.nativeElement;
    const translateX = -(this.currentSlide * this.slideWidth);
    track.style.transition = animate ? 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)' : 'none';
    track.style.transform = `translateX(${translateX}px)`;
  }

  getSlideIndicators(): number[] {
    return Array(this.maxSlides + 1).fill(0).map((_, i) => i);
  }

  private initializeCarousel(): void {
    this.featuredProducts$.subscribe(products => {
      if (products.length > 0 && !this.isCarouselInitialized) {
        this.totalProducts = products.length;
        this.maxSlides = Math.max(0, this.totalProducts - this.visibleProducts);
        this.isCarouselInitialized = true;
      }
    });
  }

  startAutoplay(): void {
    this.stopAutoplay();
    if (this.autoplayEnabled && this.maxSlides > 0) {
      this.autoplayInterval = setInterval(() => {
        this.nextSlide();
      }, 3000);
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
    
    if (imagePath.startsWith('storage/')) {
      return `http://localhost:8000/${imagePath}`;
    } else if (imagePath.startsWith('products/')) {
      return `http://localhost:8000/storage/${imagePath}`;
    } else {
      return `http://localhost:8000/storage/products/${imagePath}`;
    }
  }

  addToCart(product: Product, event?: Event): void {
    if (product.stock > 0) {
      this.cartService.addToCart(product, 1);
      this.customNotification.addToCart(product.name, () => {
        this.router.navigate(['/cart']);
      });
    } else {
      this.customNotification.error(
        'Produit indisponible',
        `Désolé, ${product.name} est actuellement en rupture de stock.`
      );
    }
  }

  scrollToProducts(): void {
    if (this.productsSection && this.productsSection.nativeElement) {
      const element = this.productsSection.nativeElement;
      const headerOffset = 80;
      const elementPosition = element.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  }

  onCardHover(productId: number, isHovered: boolean, event: Event): void {
    // Simplified hover effect
  }

  onButtonHover(productId: number, isHovered: boolean, event: Event): void {
    // Simplified hover effect
  }

  onCarouselCardHover(productId: number, isHovered: boolean, event: Event): void {
    // Simplified hover effect
  }

  private initializeAnimations(): void {
    // Simplified animations
  }

  onSearch(searchTerm: string) {
    this.searchService.setSearchTerm(searchTerm);
  }

  onCategoryChange(category: string) {
    const categoryId = this.categories$.pipe(
      map(categories => categories.find(c => c.name === category)?.id)
    ).subscribe(id => {
      this.searchService.setSelectedCategory(id || null);
    });
  }

  onSortChange(sortOption: string) {
    this.filteredProducts$ = this.filteredProducts$.pipe(
      map(products => {
        const sortedProducts = [...products];
        switch (sortOption) {
          case 'name':
            return sortedProducts.sort((a, b) => a.name.localeCompare(b.name));
          case 'price_asc':
            return sortedProducts.sort((a, b) => a.price - b.price);
          case 'price_desc':
            return sortedProducts.sort((a, b) => b.price - a.price);
          default:
            return sortedProducts;
        }
      })
    );
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