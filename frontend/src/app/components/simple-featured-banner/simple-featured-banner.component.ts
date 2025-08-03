import { Component, Input, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CurrencyPipe } from '@angular/common';
import { Router } from '@angular/router';
import { CartService } from '../../services/cart.service';
import { Product } from '../../services/product.service';
import { FeaturedProduct } from '../../models/featured-product.interface';
import { CustomNotificationService } from '../../services/custom-notification.service';

@Component({
  selector: 'app-simple-featured-banner',
  standalone: true,
  imports: [
    CommonModule,
    CurrencyPipe
  ],
  template: `
    <div class="featured-banner" *ngIf="product">
      <div class="content-container">
        <div class="text-content">
          <div class="promo-badge" *ngIf="product.promotion">
            <span>🏷️ -{{ product.promotion }}% de réduction</span>
          </div>
          
          <h1 class="product-title">{{ product.name }}</h1>
          
          <ul class="key-features">
            <li *ngFor="let feature of product.features">
              ✅ {{ feature }}
            </li>
          </ul>
          
          <div class="pricing">
            <span class="current-price">{{ product.currentPrice | currency:'XOF':'symbol':'1.0-0' }}</span>
            <div class="old-price" *ngIf="product.oldPrice">
              Avant: {{ product.oldPrice | currency:'XOF':'symbol':'1.0-0' }}
            </div>
          </div>
          
          <!-- Bouton principal -->
          <button 
            type="button"
            class="cta-button"
            [disabled]="!product.inStock"
            (click)="addToCart(convertToProduct(product), $event)">
            🛒 {{ product.inStock ? 'COMMANDER MAINTENANT' : 'RUPTURE DE STOCK' }}
          </button>
        </div>
        
        <div class="image-container">
          <img 
            [src]="product.imageUrl" 
            [alt]="product.name"
            class="product-image">
        </div>
      </div>
    </div>
    
    <div class="no-product" *ngIf="!product">
      <p>Aucun produit vedette trouvé dans la base de données.</p>
    </div>
  `,
  styles: [`
    .featured-banner {
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 30%, #16213e 70%, #0f3460 100%);
      padding: 40px 20px;
      margin: 20px 0;
      border-radius: 24px;
      color: white;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 1px 3px rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      position: relative;
      overflow: hidden;
      pointer-events: auto !important;
      z-index: 100 !important;
    }
    
    .content-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
      align-items: center;
    }
    
    .text-content {
      padding: 20px;
      pointer-events: auto;
      position: relative;
      z-index: 10;
    }
    
    .promo-badge {
      display: inline-block;
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 15px;
      box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .product-title {
      font-size: 2.5rem;
      font-weight: 800;
      margin: 15px 0;
      line-height: 1.2;
    }
    
    .key-features {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }
    
    .key-features li {
      margin: 8px 0;
      font-size: 16px;
      font-weight: 500;
    }
    
    .pricing {
      margin: 25px 0;
      padding: 15px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }
    
    .current-price {
      font-size: 2rem;
      font-weight: 900;
      color: #ffd700;
      display: block;
    }
    
    .old-price {
      font-size: 14px;
      text-decoration: line-through;
      opacity: 0.8;
      margin-top: 5px;
      color: #f8bbd9;
    }
    
    .cta-button {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      border: none;
      border-radius: 50px;
      box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-weight: 700;
      text-transform: uppercase;
      width: 100%;
      padding: 16px 24px;
      font-size: 16px;
      color: white;
      cursor: pointer;
      pointer-events: auto !important;
      position: relative;
      z-index: 9999 !important;
      letter-spacing: 0.5px;
      overflow: hidden;
    }
    
    .cta-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.6s;
    }
    
    .cta-button:hover::before {
      left: 100%;
    }
    
    .cta-button:hover:not(:disabled) {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
      background: linear-gradient(135deg, #5855f7 0%, #7c3aed 100%);
    }
    
    .cta-button:active:not(:disabled) {
      transform: translateY(0);
    }
    
    .cta-button:disabled {
      background: #666 !important;
      color: #ccc !important;
      box-shadow: none !important;
      cursor: not-allowed !important;
      transform: none !important;
    }
    
    .image-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    
    .product-image {
      max-width: 100%;
      height: auto;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .no-product {
      text-align: center;
      padding: 40px;
      color: #666;
      font-style: italic;
    }
    
    @media (max-width: 768px) {
      .content-container {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      
      .product-title {
        font-size: 2rem;
      }
      
      .current-price {
        font-size: 1.5rem;
      }
      
      .featured-banner {
        padding: 30px 15px;
        margin: 15px 0;
      }
    }
  `]
})
export class SimpleFeaturedBannerComponent implements OnInit {
  @Input() product: FeaturedProduct | null = null;

  constructor(
    private cartService: CartService,
    private router: Router,
    private customNotification: CustomNotificationService
  ) {}

  ngOnInit() {
    console.log('🔄 SimpleFeaturedBannerComponent initialisé');
    console.log('📦 Produit reçu:', this.product);
  }

  // Convertir FeaturedProduct en Product pour le panier
  convertToProduct(featuredProduct: FeaturedProduct): Product {
    return {
      id: featuredProduct.id,
      name: featuredProduct.name,
      description: featuredProduct.description,
      price: featuredProduct.currentPrice,
      image: featuredProduct.imageUrl,
      stock: featuredProduct.inStock ? 10 : 0, // Stock simulé
      category_id: 1, // Catégorie par défaut
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };
  }

  // COPIE EXACTE de la méthode addToCart qui fonctionne dans home.component.ts
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
}