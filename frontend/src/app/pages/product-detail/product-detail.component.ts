import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { FormsModule } from '@angular/forms';
import { ProductService, Product } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable } from 'rxjs';
import { switchMap } from 'rxjs/operators';
import {MatChip} from '@angular/material/chips';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule, FormsModule, MatChip],
  templateUrl: './product-detail.component.html',
  styleUrls: ['./product-detail.component.css']
})
export class ProductDetailComponent implements OnInit {
  product$: Observable<Product>;
  quantity = 1;
  loading = true;
  selectedImageIndex = 0;
  imageError = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private productService: ProductService,
    private cartService: CartService,
    private snackBar: MatSnackBar
  ) {
    this.product$ = this.route.params.pipe(
      switchMap(params => {
        const id = +params['id'];
        return this.productService.getProduct(id);
      })
    );
  }

  ngOnInit(): void {
    this.product$.subscribe({
      next: (product) => {
        this.loading = false;
        if (!product) {
          this.router.navigate(['/']);
        }
      },
      error: (error) => {
        this.loading = false;
        this.snackBar.open('Produit non trouvé', 'Fermer', {
          duration: 3000,
          horizontalPosition: 'right',
          verticalPosition: 'top'
        });
        this.router.navigate(['/']);
      }
    });
  }

  addToCart(product: Product): void {
    if (product.stock >= this.quantity) {
      this.cartService.addToCart(product, this.quantity);
      this.snackBar.open(`${this.quantity} x ${product.name} ajouté(s) au panier`, 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    } else {
      this.snackBar.open('Quantité non disponible en stock', 'Fermer', {
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      });
    }
  }

  increaseQuantity(maxStock: number): void {
    if (this.quantity < maxStock) {
      this.quantity++;
    }
  }

  decreaseQuantity(): void {
    if (this.quantity > 1) {
      this.quantity--;
    }
  }

  getImageUrl(imagePath: string): string {
    if (!imagePath) return '/assets/images/placeholder.svg';
    if (imagePath.startsWith('http')) return imagePath;

    // Gérer les différents formats de chemin d'image du backend
    if (imagePath.startsWith('products/')) {
      return `http://localhost:8000/storage/${imagePath}`;
    } else {
      return `http://localhost:8000/storage/products/${imagePath}`;
    }
  }

  onImageError(event: any): void {
    this.imageError = true;
    event.target.style.display = 'none';
  }

  onImageLoad(): void {
    this.imageError = false;
  }

  goBack(): void {
    this.router.navigate(['/']);
  }
}
