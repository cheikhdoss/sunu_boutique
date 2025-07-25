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
  selector: 'app-login',
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
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  loginForm: FormGroup;
  hidePassword = true;
  isLoading = false;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private snackBar: MatSnackBar
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]]
    });
  }

  onSubmit(): void {
    if (this.loginForm.invalid || this.isLoading) {
      return;
    }

    this.isLoading = true;
    const { email, password } = this.loginForm.value;

    this.authService.login(email, password).subscribe({
      next: () => {
        this.snackBar.open('Connexion réussie', 'Fermer', { duration: 3000 });
        this.router.navigate(['/']);
      },
      error: (error) => {
        console.error('Erreur de connexion:', error);
        this.snackBar.open(
          error.error?.message || 'Erreur lors de la connexion. Veuillez réessayer.',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoading = false;
      }
    });
  }

  getErrorMessage(field: string): string {
    const control = this.loginForm.get(field);
    if (!control || !control.errors || !control.touched) return '';

    if (control.errors['required']) {
      return 'Ce champ est obligatoire';
    }
    if (control.errors['email']) {
      return 'Veuillez entrer une adresse email valide';
    }
    if (control.errors['minlength']) {
      return 'Le mot de passe doit contenir au moins 8 caractères';
    }
    return 'Valeur invalide';
  }
} 