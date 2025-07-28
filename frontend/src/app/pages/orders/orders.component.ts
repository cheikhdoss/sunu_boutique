import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

interface Order {
  id: number;
  orderNumber: string;
  date: Date;
  status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
  total: number;
  items: Array<{
    name: string;
    quantity: number;
    price: number;
  }>;
}

@Component({
  selector: 'app-orders',
  standalone: true,
  imports: [
    CommonModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule
  ],
  templateUrl: './orders.component.html',
  styleUrls: ['./orders.component.css']
})
export class OrdersComponent implements OnInit {
  orders: Order[] = [];
  isLoading = true;

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.authService.currentUser$.subscribe(user => {
      if (!user) {
        this.router.navigate(['/auth/login']);
        return;
      }
      this.loadOrders();
    });
  }

  loadOrders(): void {
    // Simulation de données de commandes
    // Dans une vraie application, vous feriez un appel API
    setTimeout(() => {
      this.orders = [
        {
          id: 1,
          orderNumber: 'CMD-2024-001',
          date: new Date('2024-01-15'),
          status: 'delivered',
          total: 89.99,
          items: [
            { name: 'T-shirt Premium', quantity: 2, price: 29.99 },
            { name: 'Jean Slim', quantity: 1, price: 59.99 }
          ]
        },
        {
          id: 2,
          orderNumber: 'CMD-2024-002',
          date: new Date('2024-01-20'),
          status: 'shipped',
          total: 149.99,
          items: [
            { name: 'Veste en cuir', quantity: 1, price: 149.99 }
          ]
        },
        {
          id: 3,
          orderNumber: 'CMD-2024-003',
          date: new Date('2024-01-25'),
          status: 'processing',
          total: 39.99,
          items: [
            { name: 'Casquette', quantity: 1, price: 19.99 },
            { name: 'Chaussettes', quantity: 2, price: 9.99 }
          ]
        }
      ];
      this.isLoading = false;
    }, 1000);
  }

  getStatusLabel(status: string): string {
    const statusLabels: { [key: string]: string } = {
      'pending': 'En attente',
      'processing': 'En cours de traitement',
      'shipped': 'Expédiée',
      'delivered': 'Livrée',
      'cancelled': 'Annulée'
    };
    return statusLabels[status] || status;
  }

  getStatusColor(status: string): string {
    const statusColors: { [key: string]: string } = {
      'pending': 'warn',
      'processing': 'accent',
      'shipped': 'primary',
      'delivered': 'primary',
      'cancelled': ''
    };
    return statusColors[status] || '';
  }

  viewOrderDetails(order: Order): void {
    // Navigation vers les détails de la commande
    console.log('Voir détails de la commande:', order);
  }

  reorderItems(order: Order): void {
    // Ajouter les articles au panier
    console.log('Recommander:', order);
  }
}