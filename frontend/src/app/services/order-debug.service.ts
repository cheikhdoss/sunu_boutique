import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
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
export class OrderDebugService {
  private apiUrl = `${environment.apiUrl}/orders`;

  constructor(private http: HttpClient) {}

  getOrders(): Observable<{ orders: Order[] }> {
    console.log('🔍 [DEBUG] Récupération des commandes depuis:', this.apiUrl);
    console.log('🔍 [DEBUG] Token présent:', !!localStorage.getItem('token'));
    console.log('🔍 [DEBUG] Utilisateur connecté:', JSON.parse(localStorage.getItem('currentUser') || 'null'));
    
    return this.http.get<{ orders: Order[] }>(this.apiUrl).pipe(
      tap(response => {
        console.log('✅ [DEBUG] Commandes reçues:', response);
        console.log('✅ [DEBUG] Nombre de commandes:', response.orders?.length || 0);
      }),
      catchError(error => {
        console.error('❌ [DEBUG] Erreur lors de la récupération des commandes:', error);
        console.error('❌ [DEBUG] Status:', error.status);
        console.error('❌ [DEBUG] Message:', error.message);
        console.error('❌ [DEBUG] Error body:', error.error);
        throw error;
      })
    );
  }

  // Test direct de l'API
  testApiConnection(): Observable<any> {
    console.log('🧪 [TEST] Test de connexion API...');
    return this.http.get(`${environment.apiUrl}/user`).pipe(
      tap(response => {
        console.log('�� [TEST] Connexion API réussie:', response);
      }),
      catchError(error => {
        console.error('❌ [TEST] Échec de connexion API:', error);
        throw error;
      })
    );
  }
}