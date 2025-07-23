import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from './components/header/header.component';
import { SearchService } from './services/search.service';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, HeaderComponent],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('Sunu Boutique');

  constructor(private searchService: SearchService) {}

  onSearchChange(searchTerm: string): void {
    this.searchService.setSearchTerm(searchTerm);
  }
}