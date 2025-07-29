import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface OrderItem {
  name: string;
  quantity: number;
  unit_price: number;
  total_price: number;
}

export interface OrderDeliveryAddress {
  id: number;
  label: string;
  first_name: string;
  last_name: string;
  address: string;
  city: string;
  postal_code: string;
  country: string;
  phone: string;
}

export interface Order {
  id: number;
  order_number: string;
  date: string;
  status: 'pending' | 'confirmed' | 'processing' | 'shipped' | 'delivered' | 'cancelled' | 'en_attente' | 'expediee' | 'livree' | 'annulee';
  payment_status: 'pending' | 'processing' | 'paid' | 'failed' | 'refunded' | 'en_attente' | 'paye' | 'echec';
  payment_method: 'online' | 'cash_on_delivery' | 'avant_livraison' | 'apres_livraison';
  total: number;
  invoice_url?: string;
  items: OrderItem[];
  delivery_address: OrderDeliveryAddress;
}

@Injectable({
  providedIn: 'root'
})
export class OrderService {
  private apiUrl = `${environment.apiUrl}/orders`;

  constructor(private http: HttpClient) {}

  getOrders(): Observable<{ orders: Order[] }> {
    return this.http.get<{ orders: Order[] }>(this.apiUrl);
  }

  getOrder(id: number): Observable<{ order: Order }> {
    return this.http.get<{ order: Order }>(`${this.apiUrl}/${id}`);
  }

  cancelOrder(id: number): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}/cancel`, {});
  }

  downloadInvoice(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}/invoice`);
  }

  // Méthodes pour le checkout
  createOrder(orderData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, orderData);
  }

  processPayment(order: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${order.id}/payment`, {
      payment_method: order.paymentMethod,
      amount: order.total
    });
  }

  sendOrderConfirmationEmail(order: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${order.id}/confirmation-email`, {});
  }

  generateInvoice(orderId: number): Observable<any> {
    return this.http.post<any>(`${environment.apiUrl}/invoices/${orderId}/generate`, {});
  }
}