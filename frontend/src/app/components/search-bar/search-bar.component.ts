import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MaterialModule } from '../../material.module';
import { ProductService, Category } from '../../services/product.service';
import { SearchService } from '../../services/search.service';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-search-bar',
  standalone: true,
  imports: [CommonModule, FormsModule, MaterialModule],
  template: `
    <div class="search-container">
      <div class="search-wrapper">
        <mat-form-field appearance="outline" class="search-field">
          <div class="search-input-wrapper">
            <mat-icon matPrefix class="search-icon">search</mat-icon>
            <input matInput
                   [(ngModel)]="searchTerm"
                   (ngModelChange)="onSearchChange()"
                   placeholder="    Rechercher un produit"
                   class="search-input">
            <mat-icon *ngIf="searchTerm"
                     (click)="clearSearch()"
                     class="clear-icon">close</mat-icon>
          </div>
        </mat-form-field>

        <div class="filters-wrapper">
          <mat-form-field appearance="outline" class="category-field">
            <mat-label>
              <mat-icon class="filter-icon">category</mat-icon>
              Catégorie
            </mat-label>
            <mat-select [(ngModel)]="selectedCategory"
                       (selectionChange)="onCategoryChange()"
                       panelClass="custom-select-panel">
              <mat-option [value]="null" class="all-categories">
                <mat-icon>view_list</mat-icon>
                Toutes les catégories
              </mat-option>
              <mat-option *ngFor="let category of categories$ | async"
                         [value]="category.id"
                         class="category-option">
                {{category.name}}
              </mat-option>
            </mat-select>
          </mat-form-field>

          <mat-form-field appearance="outline" class="sort-field">
            <mat-label>
              <mat-icon class="filter-icon">sort</mat-icon>
              Trier par
            </mat-label>
            <mat-select [(ngModel)]="sortBy"
                       (selectionChange)="onSortChange()"
                       panelClass="custom-select-panel">
              <mat-option value="name" class="sort-option">
                <mat-icon>sort_by_alpha</mat-icon>
                Nom
              </mat-option>
              <mat-option value="price_asc" class="sort-option">
                <mat-icon>arrow_upward</mat-icon>
                Prix croissant
              </mat-option>
              <mat-option value="price_desc" class="sort-option">
                <mat-icon>arrow_downward</mat-icon>
                Prix décroissant
              </mat-option>
              <mat-option value="newest" class="sort-option">
                <mat-icon>update</mat-icon>
                Plus récent
              </mat-option>
            </mat-select>
          </mat-form-field>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .search-container {
      width: 100%;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      box-shadow:
        0 4px 15px rgba(0, 0, 0, 0.05),
        0 10px 30px rgba(0, 0, 0, 0.08);
      backdrop-filter: blur(20px);
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .search-container:hover {
      box-shadow:
        0 8px 25px rgba(0, 0, 0, 0.08),
        0 15px 40px rgba(0, 0, 0, 0.12);
      transform: translateY(-2px);
    }

    .search-wrapper {
      display: flex;
      flex-direction: row;
      gap: 1.5rem;
      align-items: center;
      padding: 0.5rem;
    }

    .search-input-wrapper {
      display: flex;
      align-items: center;
      width: 100%;
    }

    @media (max-width: 768px) {
      .search-wrapper {
        flex-direction: column;
        gap: 1rem;
      }
      .filters-wrapper {
        flex-direction: column;
        width: 100%;
      }
      .search-field {
        width: 100%;
      }
    }

    .search-field {
      flex: 2;
      min-width: 250px;
    }

    .filters-wrapper {
      display: flex;
      gap: 1rem;
      flex: 1;
    }

    .category-field, .sort-field {
      flex: 1;
      min-width: 180px;
    }

    :host ::ng-deep {
      .mat-form-field-wrapper {
        margin: 0;
        padding: 0;
      }

      .mat-form-field-outline {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
      }

      .mat-form-field-flex {
        padding: 0 1em;
        border-radius: 12px;
        transition: all 0.3s ease;
      }

      .mat-form-field-outline-start,
      .mat-form-field-outline-end,
      .mat-form-field-outline-gap {
        border-width: 2px;
        transition: all 0.3s ease;
      }

      .mat-focused .mat-form-field-outline-start,
      .mat-focused .mat-form-field-outline-end,
      .mat-focused .mat-form-field-outline-gap {
        border-color: #3f51b5;
      }

      .search-icon, .filter-icon {
        color: #666;
        margin-right: 8px;
        transition: color 0.3s ease;
      }

      .mat-focused .search-icon,
      .mat-focused .filter-icon {
        color: #3f51b5;
      }

      .clear-icon {
        color: #999;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s ease;

        &:hover {
          color: #666;
          transform: scale(1.1);
        }
      }

      .mat-form-field-infix {
        padding: 0.5em 0;
      }

      .mat-select-value {
        color: #333;
        font-weight: 500;
      }

      .mat-form-field-label {
        color: #666;
        display: flex;
        align-items: center;
        gap: 4px;
      }
    }

    .search-input {
      height: 40px;
      font-size: 16px;
      border: none;
      background: transparent;
      color: #333;
      width: 100%;

      &::placeholder {
        color: #999;
        font-weight: 400;
      }
    }

    :host ::ng-deep .custom-select-panel {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      margin-top: 8px;

      .mat-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        font-size: 14px;
        color: #333;
        transition: all 0.2s ease;

        .mat-icon {
          font-size: 20px;
          height: 20px;
          width: 20px;
          margin-right: 8px;
          color: #666;
        }

        &:hover {
          background: rgba(63, 81, 181, 0.04);
          transform: translateX(4px);
        }

        &.mat-selected {
          background: rgba(63, 81, 181, 0.1);
          color: #3f51b5;

          .mat-icon {
            color: #3f51b5;
          }
        }
      }

      .all-categories {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        margin-bottom: 4px;
      }
    }

    .category-option, .sort-option {
      position: relative;
      overflow: hidden;

      &::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: rgba(0, 0, 0, 0.04);
        transform: scaleX(0);
        transition: transform 0.3s ease;
      }

      &:hover::after {
        transform: scaleX(1);
      }
    }
  `]
})
export class SearchBarComponent implements OnInit {
  searchTerm: string = '';
  selectedCategory: number | null = null;
  sortBy: string = 'name';
  categories$: Observable<Category[]>;

  constructor(
    private productService: ProductService,
    private searchService: SearchService
  ) {
    this.categories$ = this.productService.getCategories();
  }

  ngOnInit(): void {
    const savedSearchTerm = this.searchService.getCurrentSearchTerm();
    const savedCategory = this.searchService.getCurrentCategory();

    if (savedSearchTerm) {
      this.searchTerm = savedSearchTerm;
    }
    if (savedCategory) {
      this.selectedCategory = savedCategory;
    }
  }

  clearSearch(): void {
    this.searchTerm = '';
    this.searchService.setSearchTerm('');
  }

  onSearchChange(): void {
    this.searchService.setSearchTerm(this.searchTerm);
  }

  onCategoryChange(): void {
    this.searchService.setSelectedCategory(this.selectedCategory);
  }

  onSortChange(): void {
    this.searchService.setSortBy(this.sortBy);
  }
}
