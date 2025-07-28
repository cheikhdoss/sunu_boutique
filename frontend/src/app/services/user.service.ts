import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface UserProfile {
  id: number;
  name: string;
  email: string;
  phone?: string;
  date_of_birth?: string;
  gender?: 'male' | 'female' | 'other';
  avatar?: string;
  email_verified_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Address {
  id: number;
  user_id: number;
  type: string;
  first_name: string;
  last_name: string;
  company?: string;
  address_line_1: string;
  address_line_2?: string;
  city: string;
  state: string;
  postal_code: string;
  country: string;
  phone?: string;
  is_default: boolean;
  created_at: string;
  updated_at: string;
}

export interface CreateAddressRequest {
  type: string;
  first_name: string;
  last_name: string;
  company?: string;
  address_line_1: string;
  address_line_2?: string;
  city: string;
  state?: string;
  postal_code: string;
  country: string;
  phone?: string;
  is_default: boolean;
}

export interface Order {
  id: number;
  order_number: string;
  status: string;
  total: number;
  payment_method: string;
  created_at: string;
  items?: any[];
  tracking_number?: string;
}

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = `${environment.apiUrl}/user`;

  constructor(private http: HttpClient) {}

  // Profil utilisateur
  getProfile(): Observable<UserProfile> {
    return this.http.get<UserProfile>(`${this.apiUrl}/profile`).pipe(
      catchError(error => {
        console.error('Erreur lors de la récupération du profil:', error);
        throw error;
      })
    );
  }

  updateProfile(profileData: Partial<UserProfile>): Observable<{ user: UserProfile; message: string }> {
    return this.http.put<{ user: UserProfile; message: string }>(`${this.apiUrl}/profile`, profileData).pipe(
      catchError(error => {
        console.error('Erreur lors de la mise à jour du profil:', error);
        throw error;
      })
    );
  }

  changePassword(passwordData: { current_password: string; new_password: string; new_password_confirmation: string }): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.apiUrl}/change-password`, passwordData).pipe(
      catchError(error => {
        console.error('Erreur lors du changement de mot de passe:', error);
        throw error;
      })
    );
  }

  deleteAccount(): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/account`).pipe(
      catchError(error => {
        console.error('Erreur lors de la suppression du compte:', error);
        throw error;
      })
    );
  }

  // Gestion de l'avatar
  uploadAvatar(file: File): Observable<{ avatar_url: string; message: string }> {
    const formData = new FormData();
    formData.append('avatar', file);

    return this.http.post<{ avatar_url: string; message: string }>(`${this.apiUrl}/avatar`, formData).pipe(
      catchError(error => {
        console.error('Erreur lors de l\'upload de l\'avatar:', error);
        throw error;
      })
    );
  }

  removeAvatar(): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/avatar`).pipe(
      catchError(error => {
        console.error('Erreur lors de la suppression de l\'avatar:', error);
        throw error;
      })
    );
  }

  // Gestion des adresses
  getAddresses(): Observable<Address[]> {
    return this.http.get<Address[]>(`${this.apiUrl}/addresses`).pipe(
      catchError(error => {
        console.error('Erreur lors de la récupération des adresses:', error);
        return of([]); // Retourner un tableau vide en cas d'erreur
      })
    );
  }

  createAddress(addressData: CreateAddressRequest): Observable<{ address: Address; message: string }> {
    return this.http.post<{ address: Address; message: string }>(`${this.apiUrl}/addresses`, addressData).pipe(
      catchError(error => {
        console.error('Erreur lors de la création de l\'adresse:', error);
        throw error;
      })
    );
  }

  updateAddress(addressId: number, addressData: CreateAddressRequest): Observable<{ address: Address; message: string }> {
    return this.http.put<{ address: Address; message: string }>(`${this.apiUrl}/addresses/${addressId}`, addressData).pipe(
      catchError(error => {
        console.error('Erreur lors de la mise à jour de l\'adresse:', error);
        throw error;
      })
    );
  }

  deleteAddress(addressId: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/addresses/${addressId}`).pipe(
      catchError(error => {
        console.error('Erreur lors de la suppression de l\'adresse:', error);
        throw error;
      })
    );
  }

  setDefaultAddress(addressId: number): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.apiUrl}/addresses/${addressId}/set-default`, {}).pipe(
      catchError(error => {
        console.error('Erreur lors de la définition de l\'adresse par défaut:', error);
        throw error;
      })
    );
  }

  // Gestion des commandes
  getUserOrders(): Observable<Order[]> {
    return this.http.get<Order[]>(`${this.apiUrl}/orders`).pipe(
      catchError(error => {
        console.error('Erreur lors de la récupération des commandes:', error);
        return of([]); // Retourner un tableau vide en cas d'erreur
      })
    );
  }

  getOrderDetails(orderId: number): Observable<Order> {
    return this.http.get<Order>(`${this.apiUrl}/orders/${orderId}`).pipe(
      catchError(error => {
        console.error('Erreur lors de la récupération des détails de la commande:', error);
        throw error;
      })
    );
  }

  cancelOrder(orderId: number): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.apiUrl}/orders/${orderId}/cancel`, {}).pipe(
      catchError(error => {
        console.error('Erreur lors de l\'annulation de la commande:', error);
        throw error;
      })
    );
  }

  downloadInvoice(orderId: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/orders/${orderId}/invoice`, { responseType: 'blob' }).pipe(
      catchError(error => {
        console.error('Erreur lors du téléchargement de la facture:', error);
        throw error;
      })
    );
  }

  // Statistiques utilisateur
  getUserStats(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/stats`).pipe(
      catchError(error => {
        console.error('Erreur lors de la récupération des statistiques:', error);
        // Retourner des statistiques par défaut en cas d'erreur
        return of({
          total_orders: 0,
          total_spent: 0,
          pending_orders: 0,
          completed_orders: 0
        });
      })
    );
  }

  // Gestion des favoris
  getUserFavorites(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/favorites`).pipe(
      catchError(error => {
        console.error('Erreur lors de la récupération des favoris:', error);
        return of([]); // Retourner un tableau vide en cas d'erreur
      })
    );
  }

  addToFavorites(productId: number): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.apiUrl}/favorites/${productId}`, {}).pipe(
      catchError(error => {
        console.error('Erreur lors de l\'ajout aux favoris:', error);
        throw error;
      })
    );
  }

  removeFavorite(productId: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/favorites/${productId}`).pipe(
      catchError(error => {
        console.error('Erreur lors de la suppression du favori:', error);
        throw error;
      })
    );
  }

  toggleFavorite(productId: number): Observable<{ message: string; is_favorite: boolean }> {
    return this.http.post<{ message: string; is_favorite: boolean }>(`${this.apiUrl}/favorites/${productId}`, {}).pipe(
      catchError(error => {
        console.error('Erreur lors du toggle du favori:', error);
        throw error;
      })
    );
  }
}