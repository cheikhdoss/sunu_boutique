import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class ImageUrlService {
  private readonly baseUrl = 'http://localhost:8000';
  private readonly placeholderUrl = '/assets/images/placeholder.svg';

  constructor() { }

  /**
   * Génère l'URL complète pour une image de produit
   * @param imagePath Chemin de l'image depuis la base de données
   * @returns URL complète de l'image ou placeholder si non disponible
   */
  getImageUrl(imagePath: string): string {
    // Si pas d'image, retourner le placeholder
    if (!imagePath || imagePath.trim() === '') {
      return this.placeholderUrl;
    }

    // Si l'URL est déjà complète (http/https), la retourner telle quelle
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }

    // Gérer les différents formats de chemin d'image du backend Laravel
    if (imagePath.startsWith('storage/')) {
      // Format: storage/products/image.jpg
      return `${this.baseUrl}/${imagePath}`;
    } else if (imagePath.startsWith('products/')) {
      // Format: products/image.jpg
      return `${this.baseUrl}/storage/${imagePath}`;
    } else {
      // Format: image.jpg (ajouter le préfixe complet)
      return `${this.baseUrl}/storage/products/${imagePath}`;
    }
  }

  /**
   * Génère l'URL pour une image de catégorie
   * @param imagePath Chemin de l'image de catégorie
   * @returns URL complète de l'image
   */
  getCategoryImageUrl(imagePath: string): string {
    if (!imagePath || imagePath.trim() === '') {
      return this.placeholderUrl;
    }

    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }

    if (imagePath.startsWith('storage/')) {
      return `${this.baseUrl}/${imagePath}`;
    } else if (imagePath.startsWith('categories/')) {
      return `${this.baseUrl}/storage/${imagePath}`;
    } else {
      return `${this.baseUrl}/storage/categories/${imagePath}`;
    }
  }

  /**
   * Génère l'URL pour une image d'avatar utilisateur
   * @param imagePath Chemin de l'image d'avatar
   * @returns URL complète de l'image
   */
  getAvatarImageUrl(imagePath: string): string {
    if (!imagePath || imagePath.trim() === '') {
      return '/assets/images/default-avatar.svg';
    }

    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }

    if (imagePath.startsWith('storage/')) {
      return `${this.baseUrl}/${imagePath}`;
    } else if (imagePath.startsWith('avatars/')) {
      return `${this.baseUrl}/storage/${imagePath}`;
    } else {
      return `${this.baseUrl}/storage/avatars/${imagePath}`;
    }
  }

  /**
   * Vérifie si une URL d'image est valide
   * @param imageUrl URL de l'image à vérifier
   * @returns Promise<boolean> true si l'image est accessible
   */
  async isImageValid(imageUrl: string): Promise<boolean> {
    try {
      const response = await fetch(imageUrl, { method: 'HEAD' });
      return response.ok;
    } catch (error) {
      return false;
    }
  }

  /**
   * Génère une URL d'image avec fallback
   * @param primaryPath Chemin principal de l'image
   * @param fallbackPath Chemin de fallback (optionnel)
   * @returns URL de l'image ou placeholder
   */
  getImageUrlWithFallback(primaryPath: string, fallbackPath?: string): string {
    const primaryUrl = this.getImageUrl(primaryPath);
    
    if (primaryUrl === this.placeholderUrl && fallbackPath) {
      return this.getImageUrl(fallbackPath);
    }
    
    return primaryUrl;
  }

  /**
   * Génère une URL d'image optimisée pour une taille donnée
   * @param imagePath Chemin de l'image
   * @param width Largeur souhaitée
   * @param height Hauteur souhaitée
   * @returns URL de l'image redimensionnée
   */
  getOptimizedImageUrl(imagePath: string, width?: number, height?: number): string {
    const baseImageUrl = this.getImageUrl(imagePath);
    
    if (baseImageUrl === this.placeholderUrl) {
      return baseImageUrl;
    }

    // Si le backend supporte le redimensionnement automatique
    if (width || height) {
      const params = new URLSearchParams();
      if (width) params.append('w', width.toString());
      if (height) params.append('h', height.toString());
      
      return `${baseImageUrl}?${params.toString()}`;
    }

    return baseImageUrl;
  }
}