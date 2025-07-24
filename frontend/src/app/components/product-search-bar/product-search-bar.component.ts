import { Component, EventEmitter, Output } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { NgForOf } from '@angular/common';
import { MatIcon } from '@angular/material/icon';
import { MatSelect } from '@angular/material/select';
import { MatOption } from '@angular/material/core';

@Component({
  selector: 'app-product-search-bar',
  templateUrl: './product-search-bar.component.html',
  imports: [
    ReactiveFormsModule,
    MatIcon,
    NgForOf,
    MatSelect,
    MatOption  // Ajout important
  ],
  styleUrls: ['./product-search-bar.component.css']
})
export class ProductSearchBarComponent {
  @Output() searchChange = new EventEmitter<string>();
  @Output() categoryChange = new EventEmitter<string>();
  @Output() sortChange = new EventEmitter<string>();

  searchControl = new FormControl('');
  selectedCategory: string = 'all';
  selectedSort: string = 'name';

  categories = ['Toutes les catégories', 'Électronique', 'Vêtements', 'Accessoires'];
  sortOptions = [
    { value: 'name', label: 'Nom' },
    { value: 'price_asc', label: 'Prix croissant' },
    { value: 'price_desc', label: 'Prix décroissant' }
  ];

  constructor() {
    this.searchControl.valueChanges
      .pipe(
        debounceTime(300),
        distinctUntilChanged()
      )
      .subscribe(value => {
        this.searchChange.emit(value || '');
      });
  }

  onCategoryChange(category: string) {
    this.selectedCategory = category;
    this.categoryChange.emit(category);
  }

  onSortChange(sortOption: string) {
    this.selectedSort = sortOption;
    this.sortChange.emit(sortOption);
  }
}
