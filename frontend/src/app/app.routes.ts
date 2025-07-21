import { Routes } from '@angular/router';
import { ProductCatalogComponent } from './components/product-catalog/product-catalog.component';
import { ProductDetailComponent } from './components/product-detail/product-detail.component';

export const routes: Routes = [
  { path: '', redirectTo: '/products', pathMatch: 'full' },
  { path: 'products', component: ProductCatalogComponent },
  { path: 'products/:id', component: ProductDetailComponent }
];
