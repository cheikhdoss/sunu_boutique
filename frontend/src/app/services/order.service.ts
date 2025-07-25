import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { Order, PaymentMethod } from '../models/order.interface';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class OrderService {
  private apiUrl = `${environment.apiUrl}/orders`;

  constructor(private http: HttpClient) {}

  generateOrderId(): string {
    const timestamp = new Date().getTime();
    const random = Math.floor(Math.random() * 1000);
    return `ORD-${timestamp}-${random}`;
  }

  createOrder(orderData: Omit<Order, 'orderId' | 'status' | 'createdAt'>): Observable<Order> {
    const newOrder: Order = {
      ...orderData,
      orderId: this.generateOrderId(),
      status: 'PENDING',
      createdAt: new Date()
    };

    // Simulation d'appel API
    return of(newOrder);
  }

  processPayment(order: Order): Observable<boolean> {
    // Simulation de traitement de paiement
    return of(true);
  }

  sendOrderConfirmationEmail(order: Order): Observable<boolean> {
    // Simulation d'envoi d'email
    return of(true);
  }
} 