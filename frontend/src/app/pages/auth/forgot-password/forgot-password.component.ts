import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService } from '../../../services/auth.service';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    RouterLink
  ],
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent {
  forgotPasswordForm: FormGroup;
  isLoading = false;
  emailSent = false;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private snackBar: MatSnackBar
  ) {
    this.forgotPasswordForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });
  }

  onSubmit(): void {
    if (this.forgotPasswordForm.invalid || this.isLoading) {
      return;
    }

    this.isLoading = true;
    const { email } = this.forgotPasswordForm.value;

    this.authService.forgotPassword(email).subscribe({
      next: (response) => {
        this.emailSent = true;
        this.snackBar.open(
          'Un email de réinitialisation a été envoyé à votre adresse email',
          'Fermer',
          { duration: 5000 }
        );
      },
      error: (error) => {
        console.error('Erreur de réinitialisation:', error);
        this.snackBar.open(
          error.error?.message || 'Une erreur est survenue. Veuillez réessayer.',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoading = false;
      }
    });
  }

  resendEmail(): void {
    if (this.isLoading) {
      return;
    }

    this.isLoading = true;
    const { email } = this.forgotPasswordForm.value;

    this.authService.forgotPassword(email).subscribe({
      next: (response) => {
        this.snackBar.open(
          'Email de réinitialisation renvoyé avec succès',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors du renvoi:', error);
        this.snackBar.open(
          error.error?.message || 'Une erreur est survenue lors du renvoi.',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoading = false;
      }
    });
  }

  getErrorMessage(field: string): string {
    const control = this.forgotPasswordForm.get(field);
    if (!control || !control.errors || !control.touched) return '';

    if (control.errors['required']) {
      return 'Ce champ est obligatoire';
    }
    if (control.errors['email']) {
      return 'Veuillez entrer une adresse email valide';
    }
    return 'Valeur invalide';
  }
} 