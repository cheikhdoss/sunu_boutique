import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { Order, CustomerInfo, DeliveryAddress, PaymentMethod } from '../models/order.interface';
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

  validateCustomerInfo(customerInfo: CustomerInfo): boolean {
    return !!(customerInfo.firstName && 
              customerInfo.lastName && 
              customerInfo.email && 
              customerInfo.phone &&
              this.validateEmail(customerInfo.email));
  }

  validateDeliveryAddress(address: DeliveryAddress): boolean {
    return !!(address.street && 
              address.city && 
              address.postalCode && 
              address.country);
  }

  private validateEmail(email: string): boolean {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
  }

  createOrder(order: Omit<Order, 'orderId' | 'status' | 'createdAt'>): Observable<Order> {
    const newOrder: Order = {
      ...order,
      orderId: this.generateOrderId(),
      status: 'PENDING',
      createdAt: new Date()
    };

    // Pour le moment, on simule l'appel API
    // TODO: Remplacer par un vrai appel API une fois le backend prêt
    return of(newOrder);
    // return this.http.post<Order>(this.apiUrl, newOrder);
  }

  sendOrderConfirmationEmail(order: Order): Observable<boolean> {
    // Pour le moment, on simule l'envoi d'email
    // TODO: Implémenter l'envoi réel d'email via le backend
    return of(true);
    // return this.http.post<boolean>(`${this.apiUrl}/send-confirmation`, { orderId: order.orderId });
  }

  processPayment(order: Order): Observable<boolean> {
    // Pour le moment, on simule le traitement du paiement
    // TODO: Implémenter l'intégration avec un vrai système de paiement
    return of(true);
    // return this.http.post<boolean>(`${this.apiUrl}/process-payment`, { orderId: order.orderId });
  }
}
