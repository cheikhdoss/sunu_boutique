import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ProductService } from '../../services/product.service';
import { Product } from '../../models/product.interface';

@Component({
  selector: 'app-product-catalog',
  templateUrl: './product-catalog.component.html',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
})
export class ProductCatalogComponent implements OnInit {
  products: Product[] = [];
  loading = true;
  error = '';
  searchQuery = '';
  selectedCategory: number | null = null;

  constructor(private productService: ProductService) {
    console.log('ProductCatalogComponent initialized');
  }

  ngOnInit(): void {
    console.log('ngOnInit called');
    this.loadProducts();
  }

  loadProducts(): void {
    console.log('Loading products...');
    this.loading = true;
    this.productService.getProducts().subscribe({
      next: (products) => {
        console.log('Products loaded successfully:', products);
        this.products = products;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading products:', err);
        this.error = 'Erreur lors du chargement des produits';
        this.loading = false;
      }
    });
  }

  onCategoryChange(categoryId: number | null): void {
    console.log('Category changed:', categoryId);
    this.selectedCategory = categoryId;
    if (categoryId) {
      this.loading = true;
      this.productService.getProductsByCategory(categoryId).subscribe({
        next: (products) => {
          console.log('Products by category loaded:', products);
          this.products = products;
          this.loading = false;
        },
        error: (err) => {
          console.error('Error loading products by category:', err);
          this.error = 'Erreur lors du filtrage par catégorie';
          this.loading = false;
        }
      });
    } else {
      this.loadProducts();
    }
  }

  onSearch(): void {
    console.log('Search query:', this.searchQuery);
    if (this.searchQuery.trim()) {
      this.loading = true;
      this.productService.searchProducts(this.searchQuery).subscribe({
        next: (products) => {
          console.log('Search results:', products);
          this.products = products;
          this.loading = false;
        },
        error: (err) => {
          console.error('Error searching products:', err);
          this.error = 'Erreur lors de la recherche';
          this.loading = false;
        }
      });
    } else {
      this.loadProducts();
    }
  }
} 