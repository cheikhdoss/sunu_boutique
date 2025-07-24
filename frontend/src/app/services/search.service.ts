import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  private searchTermSubject = new BehaviorSubject<string>('');
  private selectedCategorySubject = new BehaviorSubject<number | null>(null);
  private sortBySubject = new BehaviorSubject<string>('name');

  searchTerm$ = this.searchTermSubject.asObservable();
  selectedCategory$ = this.selectedCategorySubject.asObservable();
  sortBy$ = this.sortBySubject.asObservable();

  constructor() {}

  setSearchTerm(term: string): void {
    this.searchTermSubject.next(term);
  }

  setSelectedCategory(categoryId: number | null): void {
    this.selectedCategorySubject.next(categoryId);
  }

  setSortBy(sortBy: string): void {
    this.sortBySubject.next(sortBy);
  }

  getCurrentSearchTerm(): string {
    return this.searchTermSubject.value;
  }

  getCurrentCategory(): number | null {
    return this.selectedCategorySubject.value;
  }

  getCurrentSortBy(): string {
    return this.sortBySubject.value;
  }

  clearFilters(): void {
    this.searchTermSubject.next('');
    this.selectedCategorySubject.next(null);
    this.sortBySubject.next('name');
  }
}