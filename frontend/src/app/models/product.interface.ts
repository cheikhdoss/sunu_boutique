import { Category } from './category.interface';

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