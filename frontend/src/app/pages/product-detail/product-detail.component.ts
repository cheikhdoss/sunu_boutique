import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { FormsModule } from '@angular/forms';
import { ProductService, Product } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { NotificationService } from '../../services/notification.service';
import { Observable } from 'rxjs';
import { switchMap } from 'rxjs/operators';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule, FormsModule],
  templateUrl: './product-detail.component.html',
  styleUrls: ['./product-detail.component.css']
})
export class ProductDetailComponent implements OnInit {
  product$: Observable<Product>;
  quantity = 1;
  loading = true;
  selectedImageIndex = 0;
  imageError = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private productService: ProductService,
    private cartService: CartService,
    private notificationService: NotificationService
  ) {
    this.product$ = this.route.params.pipe(
      switchMap(params => {
        const id = +params['id'];
        return this.productService.getProduct(id);
      })
    );
  }

  ngOnInit(): void {
    this.product$.subscribe({
      next: (product) => {
        this.loading = false;
        if (!product) {
          this.router.navigate(['/']);
        }
      },
      error: (error) => {
        this.loading = false;
        this.notificationService.error(
          'Produit introuvable',
          'Le produit demandé n\'existe pas ou n\'est plus disponible.'
        );
        this.router.navigate(['/']);
      }
    });
  }

  addToCart(product: Product): void {
    if (product.stock >= this.quantity) {
      this.cartService.addToCart(product, this.quantity);
      
      this.notificationService.cart(
        'Produit ajouté !',
        `${this.quantity} x ${product.name} ajouté${this.quantity > 1 ? 's' : ''} au panier`,
        {
          label: 'Voir le panier',
          callback: () => {
            this.router.navigate(['/cart']);
          }
        }
      );
    } else {
      this.notificationService.warning(
        'Stock insuffisant',
        'La quantité demandée n\'est pas disponible en stock.'
      );
    }
  }

  increaseQuantity(maxStock: number): void {
    if (this.quantity < maxStock) {
      this.quantity++;
    } else {
      this.notificationService.info(
        'Limite atteinte',
        `Maximum ${maxStock} article${maxStock > 1 ? 's' : ''} disponible${maxStock > 1 ? 's' : ''} en stock.`
      );
    }
  }

  decreaseQuantity(): void {
    if (this.quantity > 1) {
      this.quantity--;
    }
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

  onImageError(event: any): void {
    this.imageError = true;
    event.target.style.display = 'none';
  }

  onImageLoad(): void {
    this.imageError = false;
  }

  goBack(): void {
    this.router.navigate(['/']);
  }
}