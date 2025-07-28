import { Component, Inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar } from '@angular/material/snack-bar';
import { UserService, Address, CreateAddressRequest } from '../../services/user.service';

@Component({
  selector: 'app-address-form',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatSelectModule,
    MatCheckboxModule,
    MatIconModule
  ],
  template: `
    <div class="address-form-container">
      <h2 mat-dialog-title>
        <mat-icon>{{ isEdit ? 'edit_location' : 'add_location' }}</mat-icon>
        {{ isEdit ? 'Modifier l\'adresse' : 'Ajouter une adresse' }}
      </h2>

      <mat-dialog-content>
        <form [formGroup]="addressForm" class="address-form">
          <div class="form-row">
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Type d'adresse</mat-label>
              <mat-select formControlName="type" required>
                <mat-option value="shipping">Livraison</mat-option>
                <mat-option value="billing">Facturation</mat-option>
              </mat-select>
              <mat-error>{{ getErrorMessage('type') }}</mat-error>
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="half-width">
              <mat-label>Prénom</mat-label>
              <input matInput formControlName="first_name" required>
              <mat-error>{{ getErrorMessage('first_name') }}</mat-error>
            </mat-form-field>

            <mat-form-field appearance="outline" class="half-width">
              <mat-label>Nom</mat-label>
              <input matInput formControlName="last_name" required>
              <mat-error>{{ getErrorMessage('last_name') }}</mat-error>
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Entreprise (optionnel)</mat-label>
              <input matInput formControlName="company">
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Adresse ligne 1</mat-label>
              <input matInput formControlName="address_line_1" required>
              <mat-error>{{ getErrorMessage('address_line_1') }}</mat-error>
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Adresse ligne 2 (optionnel)</mat-label>
              <input matInput formControlName="address_line_2">
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="half-width">
              <mat-label>Ville</mat-label>
              <input matInput formControlName="city" required>
              <mat-error>{{ getErrorMessage('city') }}</mat-error>
            </mat-form-field>

            <mat-form-field appearance="outline" class="half-width">
              <mat-label>Code postal</mat-label>
              <input matInput formControlName="postal_code" required>
              <mat-error>{{ getErrorMessage('postal_code') }}</mat-error>
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="half-width">
              <mat-label>Région/État (optionnel)</mat-label>
              <input matInput formControlName="state">
            </mat-form-field>

            <mat-form-field appearance="outline" class="half-width">
              <mat-label>Pays</mat-label>
              <mat-select formControlName="country" required>
                <mat-option value="Sénégal">Sénégal</mat-option>
                <mat-option value="Mali">Mali</mat-option>
                <mat-option value="Burkina Faso">Burkina Faso</mat-option>
                <mat-option value="Côte d'Ivoire">Côte d'Ivoire</mat-option>
                <mat-option value="Ghana">Ghana</mat-option>
                <mat-option value="Niger">Niger</mat-option>
                <mat-option value="Guinée">Guinée</mat-option>
                <mat-option value="Mauritanie">Mauritanie</mat-option>
              </mat-select>
              <mat-error>{{ getErrorMessage('country') }}</mat-error>
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Téléphone (optionnel)</mat-label>
              <input matInput type="tel" formControlName="phone" placeholder="+221 77 123 45 67">
            </mat-form-field>
          </div>

          <div class="form-row">
            <mat-checkbox formControlName="is_default">
              Définir comme adresse par défaut
            </mat-checkbox>
          </div>
        </form>
      </mat-dialog-content>

      <mat-dialog-actions align="end">
        <button mat-button (click)="onCancel()">
          <mat-icon>cancel</mat-icon>
          Annuler
        </button>
        <button mat-raised-button color="primary" 
                [disabled]="addressForm.invalid || isLoading"
                (click)="onSave()">
          <mat-icon>{{ isLoading ? 'hourglass_empty' : 'save' }}</mat-icon>
          {{ isLoading ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </mat-dialog-actions>
    </div>
  `,
  styles: [`
    .address-form-container {
      width: 100%;
      max-width: 600px;
    }

    .address-form {
      padding: 1rem 0;
    }

    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .full-width {
      width: 100%;
    }

    .half-width {
      flex: 1;
    }

    h2 {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin: 0;
      color: #1e293b;
    }

    mat-dialog-actions {
      padding: 1rem 0;
      gap: 1rem;
    }

    @media (max-width: 600px) {
      .form-row {
        flex-direction: column;
        gap: 0;
      }

      .half-width {
        width: 100%;
      }
    }
  `]
})
export class AddressFormComponent implements OnInit {
  addressForm: FormGroup;
  isLoading = false;
  isEdit = false;

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private snackBar: MatSnackBar,
    private dialogRef: MatDialogRef<AddressFormComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { address?: Address }
  ) {
    this.isEdit = !!data?.address;
    
    this.addressForm = this.fb.group({
      type: ['shipping', [Validators.required]],
      first_name: ['', [Validators.required, Validators.minLength(2)]],
      last_name: ['', [Validators.required, Validators.minLength(2)]],
      company: [''],
      address_line_1: ['', [Validators.required]],
      address_line_2: [''],
      city: ['', [Validators.required]],
      state: [''],
      postal_code: ['', [Validators.required]],
      country: ['Sénégal', [Validators.required]],
      phone: [''],
      is_default: [false]
    });
  }

  ngOnInit(): void {
    if (this.isEdit && this.data.address) {
      this.addressForm.patchValue(this.data.address);
    }
  }

  onSave(): void {
    if (this.addressForm.invalid || this.isLoading) {
      return;
    }

    this.isLoading = true;
    const addressData = this.addressForm.value as CreateAddressRequest;

    const operation = this.isEdit 
      ? this.userService.updateAddress(this.data.address!.id, addressData)
      : this.userService.createAddress(addressData);

    operation.subscribe({
      next: (response) => {
        this.snackBar.open(
          this.isEdit ? 'Adresse modifiée avec succès' : 'Adresse ajoutée avec succès',
          'Fermer',
          { duration: 3000 }
        );
        this.dialogRef.close(true);
      },
      error: (error) => {
        console.error('Erreur lors de l\'enregistrement de l\'adresse:', error);
        this.snackBar.open(
          error.error?.message || 'Erreur lors de l\'enregistrement',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoading = false;
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close(false);
  }

  getErrorMessage(field: string): string {
    const control = this.addressForm.get(field);
    if (!control || !control.errors || !control.touched) return '';

    if (control.errors['required']) {
      return 'Ce champ est obligatoire';
    }
    if (control.errors['minlength']) {
      const requiredLength = control.errors['minlength'].requiredLength;
      return `Minimum ${requiredLength} caractères requis`;
    }
    return 'Valeur invalide';
  }
}