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
      price: 79999,
      stock: 50,
      image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:43:34.000000Z'
    },
    {
      id: 2,
      name: 'T-shirt Coton Bio',
      description: 'Un t-shirt confortable et écologique.',
      price: 2550,
      stock: 120,
      image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=300&fit=crop',
      category_id: 2,
      category: this.categories[1],
      created_at: '2025-07-22T04:04:47.000000Z',
      updated_at: '2025-07-22T04:45:23.000000Z'
    },
    {
      id: 3,
      name: 'Casque Audio Bluetooth',
      description: 'Casque avec réduction de bruit active.',
      price: 14999,
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
      price: 60000,
      stock: 7,
      image: 'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T04:49:24.000000Z',
      updated_at: '2025-07-22T04:49:24.000000Z'
    },
    {
      id: 5,
      name: 'Le Labyrinthe Quantique',
      description: 'Un thriller de science-fiction qui explore les paradoxes du temps.',
      price: 1999,
      stock: 150,
      image: 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400&h=300&fit=crop',
      category_id: 3,
      category: this.categories[2],
      created_at: '2025-07-22T21:10:00.000000Z',
      updated_at: '2025-07-22T21:10:00.000000Z'
    },
    {
      id: 6,
      name: 'Veste en cuir synthétique',
      description: 'Style classique, éthique moderne. Parfait pour toutes les saisons.',
      price: 8990,
      stock: 60,
      image: 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=300&fit=crop',
      category_id: 2,
      category: this.categories[1],
      created_at: '2025-07-22T21:11:00.000000Z',
      updated_at: '2025-07-22T21:11:00.000000Z'
    },
    {
      id: 7,
      name: 'Plante d\'intérieur auto-arrosante',
      description: 'Ne vous souciez plus jamais de l\'arrosage. Idéal pour les bureaux.',
      price: 3450,
      stock: 80,
      image: 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&h=300&fit=crop',
      category_id: 4,
      category: this.categories[3],
      created_at: '2025-07-22T21:12:00.000000Z',
      updated_at: '2025-07-22T21:12:00.000000Z'
    },
    {
      id: 8,
      name: 'Drône de surveillance compact',
      description: 'Capturez des vidéos 4K époustouflantes depuis le ciel. Léger et pliable.',
      price: 45000,
      stock: 30,
      image: 'https://images.unsplash.com/photo-1507582020474-9a334a76194b?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T21:13:00.000000Z',
      updated_at: '2025-07-22T21:13:00.000000Z'
    },
    {
      id: 9,
      name: 'Chroniques d\'un futur passé',
      description: 'Recueil de nouvelles sur des futurs alternatifs et des sociétés utopiques.',
      price: 1500,
      stock: 200,
      image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=300&fit=crop',
      category_id: 3,
      category: this.categories[2],
      created_at: '2025-07-22T21:14:00.000000Z',
      updated_at: '2025-07-22T21:14:00.000000Z'
    },
    {
      id: 10,
      name: 'Baskets à lévitation magnétique',
      description: 'Marchez sur un coussin d\'air. Le futur de la chaussure.',
      price: 19999,
      stock: 40,
      image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=300&fit=crop',
      category_id: 2,
      category: this.categories[1],
      created_at: '2025-07-22T21:15:00.000000Z',
      updated_at: '2025-07-22T21:15:00.000000Z'
    },
    {
      id: 11,
      name: 'Tablette graphique Pro',
      description: 'Libérez votre créativité avec une précision et une sensibilité inégalées.',
      price: 32000,
      stock: 55,
      image: 'https://images.unsplash.com/photo-1558346547-44375f415332?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T21:16:00.000000Z',
      updated_at: '2025-07-22T21:16:00.000000Z'
    },
    {
      id: 12,
      name: 'Kit de jardinage hydroponique',
      description: 'Cultivez vos propres herbes et légumes frais, sans terre.',
      price: 7500,
      stock: 65,
      image: 'https://images.unsplash.com/photo-1612320448395-639a4898d346?w=400&h=300&fit=crop',
      category_id: 4,
      category: this.categories[3],
      created_at: '2025-07-22T21:17:00.000000Z',
      updated_at: '2025-07-22T21:17:00.000000Z'
    },
    {
      id: 13,
      name: 'Sweat à capuche thermochromique',
      description: 'Change de couleur en fonction de la température. Un vêtement interactif.',
      price: 9500,
      stock: 90,
      image: 'https://images.unsplash.com/photo-1556103623-733695238234?w=400&h=300&fit=crop',
      category_id: 2,
      category: this.categories[1],
      created_at: '2025-07-22T21:18:00.000000Z',
      updated_at: '2025-07-22T21:18:00.000000Z'
    },
    {
      id: 14,
      name: 'Montre connectée V2',
      description: 'Suivi de la santé, notifications et style. Tout en un.',
      price: 22500,
      stock: 110,
      image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=300&fit=crop',
      category_id: 1,
      category: this.categories[0],
      created_at: '2025-07-22T21:19:00.000000Z',
      updated_at: '2025-07-22T21:19:00.000000Z'
    },
    {
      id: 15,
      name: 'L\'Aube de l\'IA',
      description: 'Un essai sur l\'avenir de l\'intelligence artificielle et son impact sur la société.',
      price: 2999,
      stock: 130,
      image: 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=400&h=300&fit=crop',
      category_id: 3,
      category: this.categories[2],
      created_at: '2025-07-22T21:20:00.000000Z',
      updated_at: '2025-07-22T21:20:00.000000Z'
    },
    {
      id: 16,
      name: 'Lampe de bureau intelligente',
      description: 'Adapte sa luminosité et sa température de couleur à votre environnement.',
      price: 5500,
      stock: 70,
      image: 'https://images.unsplash.com/photo-1507434316538-635913530182?w=400&h=300&fit=crop',
      category_id: 4,
      category: this.categories[3],
      created_at: '2025-07-22T21:21:00.000000Z',
      updated_at: '2025-07-22T21:21:00.000000Z'
    }
  ];

  getProducts(): Observable<Product[]> {
    console.log('🔍 MockDataService: Retour des produits de test', this.products);
    return of(this.products).pipe(delay(100));
  }

  getProduct(id: number): Observable<Product> {
    const product = this.products.find(p => p.id === id);
    console.log('🔍 MockDataService: Recherche produit ID', id, 'trouvé:', product);
    if (!product) {
      throw new Error('Produit non trouvé');
    }
    return of(product).pipe(delay(100));
  }

  getCategories(): Observable<Category[]> {
    console.log('🔍 MockDataService: Retour des catégories de test', this.categories);
    return of(this.categories).pipe(delay(100));
  }

  getProductsByCategory(categoryId: number): Observable<Product[]> {
    const filteredProducts = this.products.filter(p => p.category_id === categoryId);
    console.log('🔍 MockDataService: Produits pour catégorie', categoryId, ':', filteredProducts);
    return of(filteredProducts).pipe(delay(100));
  }
}