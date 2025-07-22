import { Injectable } from '@angular/core';
import { Observable, of, delay } from 'rxjs';
import { Product, Category } from './product.service';

@Injectable({
  providedIn: 'root'
})
export class MockDataService {

  private categories: Category[] = [
    { id: 1, name: 'Électronique', description: 'Appareils électroniques et gadgets' },
    { id: 2, name: 'Vêtements', description: 'Mode et accessoires' },
    { id: 3, name: 'Maison & Jardin', description: 'Articles pour la maison et le jardin' },
    { id: 4, name: 'Sports & Loisirs', description: 'Équipements sportifs et loisirs' },
    { id: 5, name: 'Livres', description: 'Livres et publications' }
  ];

  private products: Product[] = [
    {
      id: 1,
      name: 'Smartphone Galaxy Pro',
      description: 'Smartphone haut de gamme avec écran AMOLED 6.7", processeur octa-core, 128GB de stockage et appareil photo 108MP. Design élégant et performances exceptionnelles.',
      price: 299000,
      stock: 15,
      image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0]
    },
    {
      id: 2,
      name: 'Casque Audio Bluetooth',
      description: 'Casque sans fil avec réduction de bruit active, autonomie 30h, son haute fidélité et design confortable. Parfait pour la musique et les appels.',
      price: 45000,
      stock: 8,
      image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0]
    },
    {
      id: 3,
      name: 'T-shirt Premium Coton',
      description: 'T-shirt en coton bio de qualité supérieure, coupe moderne et confortable. Disponible en plusieurs couleurs, idéal pour un look décontracté.',
      price: 12500,
      stock: 25,
      image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=300&fit=crop',
      category_id: 2,
      category: this.categories[1]
    },
    {
      id: 4,
      name: 'Chaussures de Sport',
      description: 'Chaussures de running avec technologie d\'amortissement avancée, semelle respirante et design moderne. Parfaites pour le sport et la marche.',
      price: 67500,
      stock: 12,
      image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=300&fit=crop',
      category_id: 4,
      category: this.categories[3]
    },
    {
      id: 5,
      name: 'Ordinateur Portable',
      description: 'Laptop 15.6" avec processeur Intel i7, 16GB RAM, SSD 512GB, carte graphique dédiée. Idéal pour le travail et les loisirs numériques.',
      price: 850000,
      stock: 5,
      image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0]
    },
    {
      id: 6,
      name: 'Montre Connectée',
      description: 'Smartwatch avec suivi de santé, GPS intégré, étanchéité IP68, écran tactile couleur et autonomie 7 jours. Compatible iOS et Android.',
      price: 125000,
      stock: 18,
      image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0]
    },
    {
      id: 7,
      name: 'Veste en Cuir',
      description: 'Veste en cuir véritable, coupe ajustée, doublure satin, fermeture éclair YKK. Style intemporel et qualité artisanale pour un look sophistiqué.',
      price: 89000,
      stock: 7,
      image: 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=300&fit=crop',
      category_id: 2,
      category: this.categories[1]
    },
    {
      id: 8,
      name: 'Cafetière Électrique',
      description: 'Cafetière programmable 12 tasses, plaque chauffante, arr��t automatique, filtre permanent inclus. Pour des cafés parfaits à tout moment.',
      price: 35000,
      stock: 20,
      image: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=300&fit=crop',
      category_id: 3,
      category: this.categories[2]
    },
    {
      id: 9,
      name: 'Livre de Cuisine',
      description: 'Guide complet de cuisine moderne avec 200 recettes illustrées, techniques de base et conseils de chefs. Pour cuisiner comme un professionnel.',
      price: 18500,
      stock: 30,
      image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=300&fit=crop',
      category_id: 5,
      category: this.categories[4]
    },
    {
      id: 10,
      name: 'Vélo de Ville',
      description: 'Vélo urbain 21 vitesses, cadre aluminium léger, freins à disque, éclairage LED intégré. Parfait pour les déplacements en ville.',
      price: 245000,
      stock: 3,
      image: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=300&fit=crop',
      category_id: 4,
      category: this.categories[3]
    },
    {
      id: 11,
      name: 'Tablette Graphique',
      description: 'Tablette de dessin numérique avec stylet sensible à la pression, surface active 10x6", compatible avec tous les logiciels de création.',
      price: 78000,
      stock: 0,
      image: 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0]
    },
    {
      id: 12,
      name: 'Plante d\'Intérieur',
      description: 'Monstera Deliciosa en pot décoratif, plante dépolluante facile d\'entretien, parfaite pour décorer votre intérieur avec style.',
      price: 15000,
      stock: 22,
      image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=300&fit=crop',
      category_id: 3,
      category: this.categories[2]
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