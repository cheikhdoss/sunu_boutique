import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSelectModule } from '@angular/material/select';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatStepperModule } from '@angular/material/stepper';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService, RegisterRequest } from '../../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatSelectModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatCheckboxModule,
    MatStepperModule,
    MatProgressBarModule,
    RouterLink
  ],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {
  personalInfoForm: FormGroup;
  accountForm: FormGroup;
  hidePassword = true;
  hidePasswordConfirmation = true;
  isLoading = false;
  currentStep = 1;
  returnUrl: string = '/';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private route: ActivatedRoute,
    private snackBar: MatSnackBar
  ) {
    this.personalInfoForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      phone: [''],
      date_of_birth: [''],
      gender: ['']
    });

    this.accountForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required]],
      terms: [false, [Validators.requiredTrue]],
      newsletter: [false]
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit(): void {
    // Récupérer l'URL de retour depuis les paramètres de requête
    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';

    // Si l'utilisateur est déjà connecté, le rediriger
    if (this.authService.isAuthenticated()) {
      this.router.navigateByUrl(this.returnUrl);
    }
  }

  passwordMatchValidator(form: FormGroup) {
    const password = form.get('password');
    const confirmPassword = form.get('password_confirmation');
    
    if (password && confirmPassword && password.value !== confirmPassword.value) {
      confirmPassword.setErrors({ passwordMismatch: true });
      return { passwordMismatch: true };
    }
    return null;
  }

  nextStep(): void {
    if (this.currentStep === 1 && this.personalInfoForm.valid) {
      this.currentStep = 2;
    }
  }

  previousStep(): void {
    if (this.currentStep > 1) {
      this.currentStep--;
    }
  }

  onSubmit(): void {
    if (this.personalInfoForm.invalid || this.accountForm.invalid || this.isLoading) {
      this.markAllFieldsAsTouched();
      return;
    }

    this.isLoading = true;

    const registerData: RegisterRequest = {
      ...this.personalInfoForm.value,
      ...this.accountForm.value,
      date_of_birth: this.personalInfoForm.value.date_of_birth ? 
        new Date(this.personalInfoForm.value.date_of_birth).toISOString().split('T')[0] : undefined
    };

    // Supprimer les champs qui ne sont pas nécessaires pour l'API
    delete (registerData as any).terms;
    delete (registerData as any).newsletter;

    this.authService.register(registerData).subscribe({
      next: (response) => {
        this.snackBar.open(
          `Inscription réussie ! Bienvenue ${response.user.name}`,
          'Fermer',
          { 
            duration: 5000,
            panelClass: ['success-snackbar']
          }
        );
        this.router.navigateByUrl(this.returnUrl);
      },
      error: (error) => {
        console.error('Erreur d\'inscription:', error);
        
        this.snackBar.open(
          error.message || 'Erreur lors de l\'inscription. Veuillez réessayer.',
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

  private markAllFieldsAsTouched(): void {
    Object.keys(this.personalInfoForm.controls).forEach(key => {
      this.personalInfoForm.get(key)?.markAsTouched();
    });
    Object.keys(this.accountForm.controls).forEach(key => {
      this.accountForm.get(key)?.markAsTouched();
    });
  }

  getPasswordStrength(): number {
    const password = this.accountForm.get('password')?.value || '';
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    
    return strength;
  }

  getPasswordStrengthColor(): string {
    const strength = this.getPasswordStrength();
    if (strength < 25) return '#ef4444';
    if (strength < 50) return '#f59e0b';
    if (strength < 75) return '#3b82f6';
    return '#10b981';
  }

  getPasswordStrengthLabel(): string {
    const strength = this.getPasswordStrength();
    if (strength < 25) return 'Très faible';
    if (strength < 50) return 'Faible';
    if (strength < 75) return 'Moyen';
    if (strength < 100) return 'Fort';
    return 'Très fort';
  }

  getErrorMessage(formGroup: FormGroup, field: string): string {
    const control = formGroup.get(field);
    if (!control || !control.errors || !control.touched) return '';

    if (control.errors['required']) {
      return 'Ce champ est obligatoire';
    }
    if (control.errors['requiredTrue']) {
      return 'Vous devez accepter les conditions d\'utilisation';
    }
    if (control.errors['email']) {
      return 'Veuillez entrer une adresse email valide';
    }
    if (control.errors['minlength']) {
      const requiredLength = control.errors['minlength'].requiredLength;
      return `Minimum ${requiredLength} caractères requis`;
    }
    if (control.errors['passwordMismatch']) {
      return 'Les mots de passe ne correspondent pas';
    }
    return 'Valeur invalide';
  }
}