import { Component, OnInit } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { MatMenuModule } from '@angular/material/menu';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatBadgeModule } from '@angular/material/badge';
import { CommonModule } from '@angular/common';
import { CartService } from '../../services/cart.service';
import { AuthService } from '../../services/auth.service';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [
    RouterLink,
    MatMenuModule,
    MatButtonModule,
    MatIconModule,
    MatBadgeModule,
    CommonModule
  ],
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {
  cartItemsCount$: Observable<number>;

  constructor(
    private cartService: CartService,
    protected authService: AuthService,
    private router: Router,
    private snackBar: MatSnackBar
  ) {
    this.cartItemsCount$ = this.cartService.cart$.pipe(
      map(items => items.reduce((count, item) => count + item.quantity, 0))
    );
  }

  ngOnInit(): void {}

  logout(): void {
    this.authService.logout().subscribe({
      next: () => {
        this.snackBar.open('Déconnexion réussie', 'Fermer', { duration: 3000 });
        this.router.navigate(['/']);
      },
      error: (error) => {
        console.error('Erreur lors de la déconnexion:', error);
        this.snackBar.open('Une erreur est survenue lors de la déconnexion.', 'Fermer', { duration: 5000 });
      }
    });
  }
}
