import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  private searchTermSubject = new BehaviorSubject<string>('');
  private selectedCategorySubject = new BehaviorSubject<number | null>(null);

  searchTerm$ = this.searchTermSubject.asObservable();
  selectedCategory$ = this.selectedCategorySubject.asObservable();

  constructor() {}

  setSearchTerm(term: string): void {
    this.searchTermSubject.next(term);
  }

  setSelectedCategory(categoryId: number | null): void {
    this.selectedCategorySubject.next(categoryId);
  }

  getCurrentSearchTerm(): string {
    return this.searchTermSubject.value;
  }

  getCurrentCategory(): number | null {
    return this.selectedCategorySubject.value;
  }

  clearFilters(): void {
    this.searchTermSubject.next('');
    this.selectedCategorySubject.next(null);
  }
}