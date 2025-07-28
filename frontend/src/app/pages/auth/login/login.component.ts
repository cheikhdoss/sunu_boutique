import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService, LoginRequest } from '../../../services/auth.service';

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
    MatCheckboxModule,
    RouterLink
  ],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  loginForm: FormGroup;
  hidePassword = true;
  isLoading = false;
  returnUrl: string = '/';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private route: ActivatedRoute,
    private snackBar: MatSnackBar
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      remember: [false]
    });
  }

  ngOnInit(): void {
    // Récupérer l'URL de retour depuis les paramètres de requête
    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
    
    // Si l'utilisateur est déjà connecté, le rediriger
    this.authService.currentUser$.subscribe(user => {
      if (user) {
        this.router.navigate([this.returnUrl]);
      }
    });
  }

  onSubmit(): void {
    if (this.loginForm.invalid || this.isLoading) {
      return;
    }

    this.isLoading = true;
    const credentials: LoginRequest = {
      email: this.loginForm.value.email,
      password: this.loginForm.value.password,
      remember: this.loginForm.value.remember
    };

    this.authService.login(credentials).subscribe({
      next: (response) => {
        this.snackBar.open(
          `Bienvenue ${response.user.name} !`, 
          'Fermer', 
          { 
            duration: 3000,
            panelClass: ['success-snackbar']
          }
        );
        
        // Rediriger vers l'URL demandée ou la page d'accueil
        this.router.navigate([this.returnUrl]);
      },
      error: (error) => {
        console.error('Erreur de connexion:', error);
        
        this.snackBar.open(
          error.message || 'Erreur lors de la connexion. Veuillez réessayer.',
          'Fermer',
          { 
            duration: 5000,
            panelClass: ['error-snackbar']
          }
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