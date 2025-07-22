import { Injectable } from '@angular/core';
import { Observable, of, delay } from 'rxjs';
import { Product, Category } from './product.service';

@Injectable({
  providedIn: 'root'
})
export class MockDataService {

  private categories: Category[] = [
    { 
      id: 1, 
      name: 'Électronique', 
      description: 'Appareils électroniques et gadgets',
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:04:47.000000Z'
    },
    { 
      id: 2, 
      name: 'Vêtements', 
      description: 'Mode et accessoires',
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:04:47.000000Z'
    },
    { 
      id: 3, 
      name: 'Livres', 
      description: 'Livres et publications',
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:04:47.000000Z'
    },
    { 
      id: 4, 
      name: 'Maison & Jardin', 
      description: 'Articles pour la maison et le jardin',
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:04:47.000000Z'
    }
  ];

  private products: Product[] = [
    {
      id: 1,
      name: 'Smartphone XYZ',
      description: 'Un smartphone dernier cri avec un appareil photo de 108MP.',
      price: 79999, // Converti de 799.99 en centimes pour l'affichage
      stock: 50,
      image: '01K0R73CR11X0AMN3VFEN3XA80.png',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:43:34.000000Z'
    },
    {
      id: 2,
      name: 'T-shirt Coton Bio',
      description: 'Un t-shirt confortable et écologique.',
      price: 2550, // Converti de 25.50 en centimes
      stock: 120,
      image: '01K0R76QET34RF2QMYPE2YB8KD.jpeg',
      category_id: 2,
      category: this.categories[1],
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:45:23.000000Z'
    },
    {
      id: 3,
      name: 'Casque Audio Bluetooth',
      description: 'Casque avec réduction de bruit active.',
      price: 14999, // Converti de 149.99 en centimes
      stock: 75,
      image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:04:47.000000Z'
    },
    {
      id: 4,
      name: 's25',
      description: 'Un smartphone dernier cri avec un appareil photo de 108MP.',
      price: 60000, // Converti de 600.00 en centimes
      stock: 7,
      image: 'products/01K0R7E2C7H4GQFAZM7TEFA4BG.jpeg',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T04:49:24.000000Z',
      updated_at: '2025-07-22T04:49:24.000000Z'
    }
  ];

  getProducts(): Observable<Product[]> {
    return of(this.products).pipe(delay(800)); // Simule un délai réseau
  }

  getProduct(id: number): Observable<Product> {
    const product = this.products.find(p => p.id === id);
    if (!product) {
      throw new Error('Produit non trouvé');
    }
    return of(product).pipe(delay(500));
  }

  getCategories(): Observable<Category[]> {
    return of(this.categories).pipe(delay(300));
  }

  getProductsByCategory(categoryId: number): Observable<Product[]> {
    const filteredProducts = this.products.filter(p => p.category_id === categoryId);
    return of(filteredProducts).pipe(delay(600));
  }
}