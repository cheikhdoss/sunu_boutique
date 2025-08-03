import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatDividerModule } from '@angular/material/divider';
import { MatTooltipModule } from '@angular/material/tooltip';
import { RouterLink, Router } from '@angular/router';
import { CartService, CartItem } from '../../services/cart.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [
    CommonModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatDividerModule,
    MatTooltipModule,
    RouterLink
  ],
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
  cartItems$: Observable<CartItem[]>;

  constructor(
    protected cartService: CartService,
    private snackBar: MatSnackBar,
    private router: Router
  ) {
    this.cartItems$ = this.cartService.cart$;
  }

  ngOnInit(): void {}

  updateQuantity(productId: number, quantity: number): void {
    if (quantity <= 0) {
      this.removeFromCart(productId);
    } else {
      this.cartService.updateQuantity(productId, quantity);
    }
  }

  removeFromCart(productId: number): void {
    this.cartService.removeFromCart(productId);
    this.snackBar.open('Produit retiré du panier', 'Fermer', {
      duration: 3000,
      horizontalPosition: 'right',
      verticalPosition: 'top'
    });
  }

  clearCart(): void {
    this.cartService.clearCart();
    this.snackBar.open('Panier vidé', 'Fermer', {
      duration: 3000,
      horizontalPosition: 'right',
      verticalPosition: 'top'
    });
  }

  getTotal(): number {
    return this.cartService.getTotalAmount();
  }

  getTotalItems(): number {
    return this.cartService.getCartItemsCount();
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

  increaseQuantity(item: CartItem): void {
    if (item.quantity < item.product.stock) {
      this.updateQuantity(item.product.id, item.quantity + 1);
    } else {
      this.snackBar.open('Stock insuffisant', 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    }
  }

  decreaseQuantity(item: CartItem): void {
    if (item.quantity > 1) {
      this.updateQuantity(item.product.id, item.quantity - 1);
    }
  }

  proceedToCheckout(): void {
    this.router.navigate(['/checkout']);
  }

  trackByProductId(index: number, item: CartItem): number {
    return item.product.id;
  }
}