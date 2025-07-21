import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, tap } from 'rxjs';
import { Product } from '../models/product.interface';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ProductService {
  private apiUrl = `${environment.apiUrl}/products`;

  constructor(private http: HttpClient) {
    console.log('API URL:', this.apiUrl); // Log l'URL de l'API
  }

  getProducts(): Observable<Product[]> {
    console.log('Fetching products...'); // Log avant l'appel
    return this.http.get<Product[]>(this.apiUrl).pipe(
      tap(products => console.log('Products received:', products)), // Log les produits reçus
      catchError(error => {
        console.error('Error fetching products:', error); // Log les erreurs
        throw error;
      })
    );
  }

  getProduct(id: number): Observable<Product> {
    return this.http.get<Product>(`${this.apiUrl}/${id}`).pipe(
      tap(product => console.log('Product received:', product)),
      catchError(error => {
        console.error('Error fetching product:', error);
        throw error;
      })
    );
  }

  getProductsByCategory(categoryId: number): Observable<Product[]> {
    return this.http.get<Product[]>(`${this.apiUrl}/category/${categoryId}`).pipe(
      tap(products => console.log('Products by category received:', products)),
      catchError(error => {
        console.error('Error fetching products by category:', error);
        throw error;
      })
    );
  }

  searchProducts(query: string): Observable<Product[]> {
    return this.http.get<Product[]>(`${this.apiUrl}/search/${query}`).pipe(
      tap(products => console.log('Search results:', products)),
      catchError(error => {
        console.error('Error searching products:', error);
        throw error;
      })
    );
  }
} 