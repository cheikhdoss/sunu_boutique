import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError } from 'rxjs';
import { environment } from '../../environments/environment';
import { MockDataService } from './mock-data.service';

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  stock: number;
  image: string;
  category_id: number;
  category?: Category;
  created_at?: string;
  updated_at?: string;
}

export interface Category {
  id: number;
  name: string;
  description?: string;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root'
})
export class ProductService {
  private apiUrl = environment.apiUrl;

  constructor(
    private http: HttpClient,
    private mockDataService: MockDataService
  ) { }

  getProducts(): Observable<Product[]> {
    return this.http.get<Product[]>(`${this.apiUrl}/products`).pipe(
      catchError(() => {
        console.warn('Backend non disponible, utilisation des données de test');
        return this.mockDataService.getProducts();
      })
    );
  }

  getProduct(id: number): Observable<Product> {
    return this.http.get<Product>(`${this.apiUrl}/products/${id}`).pipe(
      catchError(() => {
        console.warn('Backend non disponible, utilisation des données de test');
        return this.mockDataService.getProduct(id);
      })
    );
  }

  getCategories(): Observable<Category[]> {
    return this.http.get<Category[]>(`${this.apiUrl}/categories`).pipe(
      catchError(() => {
        console.warn('Backend non disponible, utilisation des données de test');
        return this.mockDataService.getCategories();
      })
    );
  }

  getProductsByCategory(categoryId: number): Observable<Product[]> {
    return this.http.get<Product[]>(`${this.apiUrl}/products?category_id=${categoryId}`).pipe(
      catchError(() => {
        console.warn('Backend non disponible, utilisation des données de test');
        return this.mockDataService.getProductsByCategory(categoryId);
      })
    );
  }
}