import { Component, OnInit, Output, EventEmitter, ViewChild, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { CartService } from '../../services/cart.service';
import { ProductService, Category } from '../../services/product.service';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule],
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {
  cartItemsCount$: Observable<number>;
  searchTerm = '';

  @Output() searchChange = new EventEmitter<string>();

  constructor(
    private cartService: CartService,
    private productService: ProductService
  ) {
    this.cartItemsCount$ = this.cartService.cart$.pipe(
      map(items => items.reduce((count, item) => count + item.quantity, 0))
    );
  }

  ngOnInit(): void {}

  onSearchChange(searchTerm: string): void {
    this.searchTerm = searchTerm;
    this.searchChange.emit(searchTerm);
  }

  performSearch(): void {
    // Émettre la recherche
    this.searchChange.emit(this.searchTerm.trim());
  }
}