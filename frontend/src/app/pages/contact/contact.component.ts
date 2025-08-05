import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule, ReactiveFormsModule],
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.css']
})
export class ContactComponent {
  contactForm: FormGroup;
  isLoading = false;

  contactMethods = [
    {
      icon: 'phone',
      title: 'Téléphone',
      value: '+221 77 123 45 67',
      description: 'Lun-Ven 8h-18h, Sam 9h-15h'
    },
    {
      icon: 'email',
      title: 'Email',
      value: 'contact@sunuboutique.com',
      description: 'Réponse sous 24h'
    },
    {
      icon: 'location_on',
      title: 'Adresse',
      value: 'Dakar, Sénégal',
      description: 'Plateau, Avenue Léopold Sédar Senghor'
    },
    {
      icon: 'schedule',
      title: 'Horaires',
      value: 'Lun-Sam 8h-19h',
      description: 'Fermé le dimanche'
    }
  ];

  faqQuickLinks = [
    { question: 'Comment passer une commande ?', link: '/help#commandes' },
    { question: 'Quels sont les modes de paiement ?', link: '/help#paiement' },
    { question: 'Combien de temps prend la livraison ?', link: '/help#livraison' },
    { question: 'Comment créer un compte ?', link: '/help#compte' }
  ];

  constructor(
    private fb: FormBuilder,
    private snackBar: MatSnackBar
  ) {
    this.contactForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.pattern(/^(\+221|221)?[0-9]{9}$/)]],
      subject: ['', [Validators.required]],
      category: ['general', [Validators.required]],
      message: ['', [Validators.required, Validators.minLength(10)]]
    });
  }

  onSubmit() {
    if (this.contactForm.valid) {
      this.isLoading = true;
      
      // Simulation d'envoi
      setTimeout(() => {
        this.isLoading = false;
        this.snackBar.open(
          'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.',
          'Fermer',
          { 
            duration: 5000,
            panelClass: ['success-snackbar']
          }
        );
        this.contactForm.reset();
        this.contactForm.patchValue({ category: 'general' });
      }, 2000);
    } else {
      this.markFormGroupTouched();
    }
  }

  private markFormGroupTouched() {
    Object.keys(this.contactForm.controls).forEach(key => {
      const control = this.contactForm.get(key);
      control?.markAsTouched();
    });
  }

  getErrorMessage(fieldName: string): string {
    const control = this.contactForm.get(fieldName);
    if (control?.hasError('required')) {
      return `${this.getFieldLabel(fieldName)} est requis`;
    }
    if (control?.hasError('email')) {
      return 'Email invalide';
    }
    if (control?.hasError('minlength')) {
      const minLength = control.errors?.['minlength']?.requiredLength;
      return `Minimum ${minLength} caractères requis`;
    }
    if (control?.hasError('pattern')) {
      return 'Format de téléphone invalide (+221XXXXXXXXX)';
    }
    return '';
  }

  private getFieldLabel(fieldName: string): string {
    const labels: { [key: string]: string } = {
      name: 'Le nom',
      email: 'L\'email',
      phone: 'Le téléphone',
      subject: 'Le sujet',
      category: 'La catégorie',
      message: 'Le message'
    };
    return labels[fieldName] || fieldName;
  }
}