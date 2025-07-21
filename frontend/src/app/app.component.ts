import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, CommonModule],
  template: `
    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
          <div class="flex justify-between items-center py-4">
            <a routerLink="/" class="text-xl font-bold text-blue-600">E-Commerce</a>
          </div>
        </div>
      </nav>
      <router-outlet></router-outlet>
    </div>
  `
})
export class AppComponent {
  title = 'E-Commerce';
} 