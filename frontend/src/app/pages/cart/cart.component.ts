import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { FormsModule } from '@angular/forms';
import { CartService, CartItem } from '../../services/cart.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule, FormsModule],
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
  cartItems$: Observable<CartItem[]>;
  
  constructor(
    private cartService: CartService,
    private snackBar: MatSnackBar
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
    return this.cartService.getCartTotal();
  }

  getTotalItems(): number {
    return this.cartService.getCartItemsCount();
  }

  getImageUrl(imagePath: string): string {
    if (!imagePath) return '/assets/images/placeholder.svg';
    if (imagePath.startsWith('http')) return imagePath;
    
    // Gérer les différents formats de chemin d'image du backend
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
    // Ici vous pouvez implémenter la logique de commande
    this.snackBar.open('Fonctionnalité de commande à implémenter', 'Fermer', {
      duration: 3000,
      horizontalPosition: 'right',
      verticalPosition: 'top'
    });
  }

  trackByProductId(index: number, item: CartItem): number {
    return item.product.id;
  }
}