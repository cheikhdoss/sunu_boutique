import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-payment-error',
  standalone: true,
  imports: [
    CommonModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule
  ],
  templateUrl: './payment-error.html',
  styleUrls: ['./payment-error.css']
})
export class PaymentErrorComponent implements OnInit {
  orderId: string | null = null;
  errorMessage: string = 'Une erreur est survenue lors du traitement de votre paiement.';

  constructor(
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.orderId = params['order_id'];
      const message = params['message'];
      if (message) {
        this.errorMessage = decodeURIComponent(message);
      }
    });
  }

  retryPayment() {
    if (this.orderId) {
      this.router.navigate(['/checkout'], { 
        queryParams: { order_id: this.orderId } 
      });
    } else {
      this.router.navigate(['/cart']);
    }
  }

  goToHome() {
    this.router.navigate(['/']);
  }

  contactSupport() {
    // Rediriger vers la page de contact ou ouvrir un email
    window.location.href = 'mailto:support@sunuboutique.com?subject=Problème de paiement&body=Bonjour, j\'ai rencontré un problème avec ma commande ' + (this.orderId || '') + '.';
  }
}