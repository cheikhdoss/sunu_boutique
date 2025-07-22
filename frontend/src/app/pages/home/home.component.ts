import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { ProductService, Product, Category } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable, BehaviorSubject, combineLatest } from 'rxjs';
import { map, startWith } from 'rxjs/operators';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  products$: Observable<Product[]>;
  categories$: Observable<Category[]>;
  filteredProducts$: Observable<Product[]>;
  selectedCategory$ = new BehaviorSubject<number | null>(null);
  searchTerm$ = new BehaviorSubject<string>('');
  loading = true;

  constructor(
    private productService: ProductService,
    private cartService: CartService,
    private snackBar: MatSnackBar
  ) {
    this.products$ = this.productService.getProducts();
    this.categories$ = this.productService.getCategories();
    
    this.filteredProducts$ = combineLatest([
      this.products$,
      this.selectedCategory$.pipe(startWith(null)),
      this.searchTerm$.pipe(startWith(''))
    ]).pipe(
      map(([products, categoryId, searchTerm]) => {
        let filtered = products;
        
        if (categoryId) {
          filtered = filtered.filter(product => product.category_id === categoryId);
        }
        
        if (searchTerm) {
          filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            product.description.toLowerCase().includes(searchTerm.toLowerCase())
          );
        }
        
        return filtered;
      })
    );
  }

  ngOnInit(): void {
    this.products$.subscribe(() => {
      this.loading = false;
    });
  }

  onCategoryChange(categoryId: number | null): void {
    this.selectedCategory$.next(categoryId);
  }

  onSearchChange(searchTerm: string): void {
    this.searchTerm$.next(searchTerm);
  }

  addToCart(product: Product): void {
    if (product.stock > 0) {
      this.cartService.addToCart(product, 1);
      this.snackBar.open(`${product.name} ajouté au panier`, 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    } else {
      this.snackBar.open('Produit en rupture de stock', 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    }
  }

  getImageUrl(imagePath: string): string {
    if (!imagePath) return '/assets/images/placeholder.svg';
    if (imagePath.startsWith('http')) return imagePath;
    return `http://localhost:8000/storage/${imagePath}`;
  }

  trackByProductId(index: number, product: Product): number {
    return product.id;
  }
}