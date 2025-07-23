export interface FeaturedProduct {
  id: number;
  name: string;
  description: string;
  features: string[];
  currentPrice: number;
  oldPrice?: number;
  imageUrl: string;
  promotion?: number;
  inStock: boolean;
}
