import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { MatMenuModule } from '@angular/material/menu';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatBadgeModule } from '@angular/material/badge';
import { CommonModule } from '@angular/common';
import { CartService } from '../../services/cart.service';
import { AuthService } from '../../services/auth.service';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

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
    protected authService: AuthService
  ) {
    this.cartItemsCount$ = this.cartService.cart$.pipe(
      map(items => items.reduce((count, item) => count + item.quantity, 0))
    );
  }

  ngOnInit(): void {}

  logout(): void {
    this.authService.logout();
  }
}
