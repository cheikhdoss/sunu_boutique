import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface PayDunyaPaymentRequest {
  order_id: number;
}

export interface PayDunyaPaymentResponse {
  success: boolean;
  data?: {
    payment_url: string;
    invoice_token: string;
    response_code: string;
    description: string;
  };
  message?: string;
}

export interface PayDunyaStatusResponse {
  success: boolean;
  data?: {
    order_status: string;
    payment_status: string;
    paydunya_status: string;
    invoice_data: any;
  };
  message?: string;
}

@Injectable({
  providedIn: 'root'
})
export class PayDunyaService {
  private apiUrl = `${environment.apiUrl}/payments/paydunya`;

  constructor(private http: HttpClient) {}

  /**
   * Initier un paiement PayDunya
   */
  initiatePayment(request: PayDunyaPaymentRequest): Observable<PayDunyaPaymentResponse> {
    return this.http.post<PayDunyaPaymentResponse>(`${this.apiUrl}/initiate`, request);
  }

  /**
   * Vérifier le statut d'un paiement
   */
  checkPaymentStatus(orderId: number): Observable<PayDunyaStatusResponse> {
    return this.http.get<PayDunyaStatusResponse>(`${this.apiUrl}/status`, {
      params: { order_id: orderId.toString() }
    });
  }

  /**
   * Rediriger vers PayDunya pour le paiement
   */
  redirectToPayDunya(paymentUrl: string): void {
    // Ouvrir PayDunya dans le même onglet pour une meilleure UX
    window.location.href = paymentUrl;
  }

  /**
   * Vérifier si PayDunya est disponible
   */
  isPayDunyaAvailable(): boolean {
    // PayDunya est toujours disponible via le web
    return true;
  }

  /**
   * Obtenir les méthodes de paiement supportées par PayDunya
   */
  getSupportedPaymentMethods(): string[] {
    return [
      'Carte bancaire',
      'Mobile Money',
      'Virement bancaire',
      'Orange Money',
      'MTN Mobile Money',
      'Moov Money'
    ];
  }

  /**
   * Formater le montant pour l'affichage
   */
  formatAmount(amount: number, currency: string = 'XOF'): string {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount);
  }
}