import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface Category {
  id: number;
  name: string;
  description?: string;
}

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  image: string;
  stock: number;
  category?: Category;
  created_at?: string;
  updated_at?: string;
}

export interface CartItem {
  product: Product;
  quantity: number;
  id: number;
  price: number;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private cartItems: CartItem[] = [];
  private cartSubject = new BehaviorSubject<CartItem[]>([]);
  cart$: Observable<CartItem[]> = this.cartSubject.asObservable();

  constructor() {
    // Charger le panier depuis le localStorage au démarrage
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
      this.cartItems = JSON.parse(savedCart);
      this.cartSubject.next(this.cartItems);
    }
  }

  private saveCart(): void {
    localStorage.setItem('cart', JSON.stringify(this.cartItems));
    this.cartSubject.next(this.cartItems);
  }

  addToCart(product: Product, quantity: number = 1): void {
    const existingItem = this.cartItems.find(item => item.product.id === product.id);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.cartItems.push({
        product,
        quantity,
        id: product.id,
        price: product.price
      });
    }

    this.saveCart();
  }

  removeFromCart(productId: number): void {
    this.cartItems = this.cartItems.filter(item => item.product.id !== productId);
    this.saveCart();
  }

  updateQuantity(productId: number, quantity: number): void {
    const item = this.cartItems.find(item => item.product.id === productId);
    if (item) {
      item.quantity = quantity;
      this.saveCart();
    }
  }

  clearCart(): void {
    this.cartItems = [];
    this.saveCart();
  }

  getCartItems(): CartItem[] {
    return this.cartItems;
  }

  getCartItemsCount(): number {
    return this.cartItems.reduce((count, item) => count + item.quantity, 0);
  }

  getTotalAmount(): number {
    return this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
  }
}
