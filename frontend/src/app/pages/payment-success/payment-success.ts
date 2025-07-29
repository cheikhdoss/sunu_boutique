import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { environment } from '../../../environments/environment';

interface OrderDetails {
  id: number;
  order_number: string;
  total: number;
  payment_status: string;
  status: string;
  created_at: string;
  items: any[];
  invoice_url?: string;
}

@Component({
  selector: 'app-payment-success',
  standalone: true,
  imports: [
    CommonModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule
  ],
  templateUrl: './payment-success.html',
  styleUrls: ['./payment-success.css']
})
export class PaymentSuccessComponent implements OnInit {
  orderId: string | null = null;
  orderDetails: OrderDetails | null = null;
  loading = true;
  error = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.orderId = params['order_id'];
      if (this.orderId) {
        this.loadOrderDetails();
      } else {
        this.error = true;
        this.loading = false;
      }
    });
  }

  loadOrderDetails() {
    this.http.get<{success: boolean, data: OrderDetails}>(`${environment.apiUrl}/orders/${this.orderId}/details`)
      .subscribe({
        next: (response) => {
          if (response.success) {
            this.orderDetails = response.data;
            
            // Si le paiement n'est pas encore confirmé, vérifier le statut PayDunya
            if (this.orderDetails.payment_status === 'processing') {
              this.checkPaymentStatus();
            }
          } else {
            this.error = true;
          }
          this.loading = false;
        },
        error: (error) => {
          console.error('Erreur lors du chargement des détails:', error);
          this.error = true;
          this.loading = false;
        }
      });
  }

  checkPaymentStatus() {
    // Vérifier le statut du paiement auprès de PayDunya
    this.http.get(`${environment.apiUrl}/payments/paydunya/status?order_id=${this.orderId}`)
      .subscribe({
        next: (response: any) => {
          if (response.success && this.orderDetails) {
            // Mettre à jour les détails de la commande
            this.orderDetails.payment_status = response.data.payment_status;
            this.orderDetails.status = response.data.order_status;
          }
        },
        error: (error) => {
          console.error('Erreur lors de la vérification du statut:', error);
        }
      });
  }

  downloadInvoice() {
    if (this.orderDetails?.invoice_url) {
      // Télécharger directement la facture existante
      window.open(`${environment.apiUrl}/invoices/${this.orderId}/download`, '_blank');
    } else {
      // Générer et télécharger la facture
      this.http.post(`${environment.apiUrl}/invoices/${this.orderId}/generate`, {})
        .subscribe({
          next: (response: any) => {
            if (response.success && response.invoice_url) {
              // Mettre à jour les détails de la commande
              if (this.orderDetails) {
                this.orderDetails.invoice_url = response.invoice_url;
              }
              // Télécharger la facture
              window.open(`${environment.apiUrl}/invoices/${this.orderId}/download`, '_blank');
            }
          },
          error: (error) => {
            console.error('Erreur lors de la génération de la facture:', error);
            alert('Erreur lors de la génération de la facture. Veuillez réessayer.');
          }
        });
    }
  }

  viewInvoice() {
    if (this.orderDetails?.invoice_url || this.orderDetails?.payment_status === 'paid') {
      window.open(`${environment.apiUrl}/invoices/${this.orderId}/view`, '_blank');
    }
  }

  goToOrders() {
    this.router.navigate(['/orders']);
  }

  goToHome() {
    this.router.navigate(['/']);
  }

  getPaymentStatusLabel(status: string): string {
    const statusMap: { [key: string]: string } = {
      'paid': 'Payé',
      'processing': 'En cours',
      'pending': 'En attente',
      'failed': 'Échoué',
      'refunded': 'Remboursé'
    };
    return statusMap[status] || status;
  }

  getOrderStatusLabel(status: string): string {
    const statusMap: { [key: string]: string } = {
      'pending': 'En attente',
      'confirmed': 'Confirmée',
      'processing': 'En préparation',
      'shipped': 'Expédiée',
      'delivered': 'Livrée',
      'cancelled': 'Annulée'
    };
    return statusMap[status] || status;
  }
}