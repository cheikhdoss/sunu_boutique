import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormArray, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatTabsModule } from '@angular/material/tabs';
import { MatSelectModule } from '@angular/material/select';
import { MatChipsModule } from '@angular/material/chips';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatDividerModule } from '@angular/material/divider';
import { AuthService, User } from '../../services/auth.service';
import { ProfileService } from '../../services/profile.service';
import { DeliveryAddressService, DeliveryAddress } from '../../services/delivery-address.service';
import { OrderService, Order } from '../../services/order.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatTabsModule,
    MatSelectModule,
    MatChipsModule,
    MatExpansionModule,
    MatDividerModule
  ],
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
  profileForm: FormGroup;
  passwordForm: FormGroup;
  addressForm: FormGroup;
  currentUser: User | null = null;
  isLoading = false;
  isAddingAddress = false;
  
  // Données
  deliveryAddresses: DeliveryAddress[] = [];
  orderHistory: Order[] = [];
  avatarFile: File | null = null;
  avatarPreview: string | null = null;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private profileService: ProfileService,
    private deliveryAddressService: DeliveryAddressService,
    private orderService: OrderService,
    private snackBar: MatSnackBar,
    private router: Router
  ) {
    this.profileForm = this.fb.group({
      name: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.pattern(/^[0-9+\-\s()]+$/)]]
    });

    this.passwordForm = this.fb.group({
      currentPassword: ['', [Validators.required]],
      newPassword: ['', [Validators.required, Validators.minLength(8)]],
      confirmPassword: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });

    this.addressForm = this.fb.group({
      label: ['', [Validators.required]],
      first_name: ['', [Validators.required]],
      last_name: ['', [Validators.required]],
      address: ['', [Validators.required]],
      city: ['', [Validators.required]],
      postal_code: ['', [Validators.required, Validators.pattern(/^[0-9]{5}$/)]],
      country: ['France', [Validators.required]],
      phone: ['', [Validators.required, Validators.pattern(/^[0-9+\-\s()]+$/)]],
      is_default: [false]
    });
  }

  ngOnInit(): void {
    this.authService.currentUser$.subscribe(user => {
      if (user) {
        this.currentUser = user;
        this.loadProfile();
        this.loadDeliveryAddresses();
        this.loadOrderHistory();
      } else {
        this.router.navigate(['/auth/login']);
      }
    });
  }

  // Charger le profil complet depuis l'API
  loadProfile(): void {
    this.profileService.getProfile().subscribe({
      next: (response) => {
        const user = response.user;
        this.profileForm.patchValue({
          name: user.name,
          email: user.email,
          phone: user.phone
        });
        this.avatarPreview = user.avatar;
      },
      error: (error) => {
        console.error('Erreur lors du chargement du profil:', error);
        this.snackBar.open('Erreur lors du chargement du profil', 'Fermer', { duration: 3000 });
      }
    });
  }

  // Gestion des adresses de livraison
  loadDeliveryAddresses(): void {
    this.deliveryAddressService.getAddresses().subscribe({
      next: (response) => {
        this.deliveryAddresses = response.addresses;
      },
      error: (error) => {
        console.error('Erreur lors du chargement des adresses:', error);
        this.snackBar.open('Erreur lors du chargement des adresses', 'Fermer', { duration: 3000 });
      }
    });
  }

  addAddress(): void {
    if (this.addressForm.invalid) {
      return;
    }

    this.isLoading = true;
    const addressData = this.addressForm.value;

    this.deliveryAddressService.createAddress(addressData).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.loadDeliveryAddresses(); // Recharger la liste
        this.addressForm.reset();
        this.addressForm.patchValue({ country: 'France', is_default: false });
        this.isAddingAddress = false;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors de l\'ajout de l\'adresse:', error);
        this.snackBar.open('Erreur lors de l\'ajout de l\'adresse', 'Fermer', { duration: 3000 });
        this.isLoading = false;
      }
    });
  }

  editAddress(address: DeliveryAddress): void {
    this.addressForm.patchValue(address);
    this.isAddingAddress = true;
  }

  deleteAddress(addressId: number): void {
    if (!addressId) return;

    this.deliveryAddressService.deleteAddress(addressId).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.loadDeliveryAddresses(); // Recharger la liste
      },
      error: (error) => {
        console.error('Erreur lors de la suppression de l\'adresse:', error);
        this.snackBar.open('Erreur lors de la suppression de l\'adresse', 'Fermer', { duration: 3000 });
      }
    });
  }

  setDefaultAddress(addressId: number): void {
    if (!addressId) return;

    this.deliveryAddressService.setDefaultAddress(addressId).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.loadDeliveryAddresses(); // Recharger la liste
      },
      error: (error) => {
        console.error('Erreur lors de la mise à jour de l\'adresse par défaut:', error);
        this.snackBar.open('Erreur lors de la mise à jour', 'Fermer', { duration: 3000 });
      }
    });
  }

  // Gestion de l'historique des commandes
  loadOrderHistory(): void {
    this.orderService.getOrders().subscribe({
      next: (response) => {
        this.orderHistory = response.orders;
      },
      error: (error) => {
        console.error('Erreur lors du chargement des commandes:', error);
        this.snackBar.open('Erreur lors du chargement des commandes', 'Fermer', { duration: 3000 });
      }
    });
  }

  getStatusLabel(status: string): string {
    const statusLabels: { [key: string]: string } = {
      // Statuts français (anciens)
      'en_attente': 'En attente',
      'expediee': 'Expédiée',
      'livree': 'Livrée',
      'annulee': 'Annulée',
      // Statuts anglais (nouveaux)
      'pending': 'En attente',
      'confirmed': 'Confirmée',
      'processing': 'En préparation',
      'shipped': 'Expédiée',
      'delivered': 'Livrée',
      'cancelled': 'Annulée'
    };
    return statusLabels[status] || status;
  }

  getPaymentStatusLabel(status: string): string {
    const statusLabels: { [key: string]: string } = {
      // Statuts français (anciens)
      'en_attente': 'En attente',
      'paye': 'Payé',
      'echec': 'Échec',
      // Statuts anglais (nouveaux)
      'pending': 'En attente',
      'processing': 'En cours',
      'paid': 'Payé',
      'failed': 'Échec',
      'refunded': 'Remboursé'
    };
    return statusLabels[status] || status;
  }

  getPaymentMethodLabel(method: string): string {
    const methodLabels: { [key: string]: string } = {
      // Méthodes françaises (anciennes)
      'avant_livraison': 'Paiement en ligne',
      'apres_livraison': 'Paiement à la livraison',
      // Méthodes anglaises (nouvelles)
      'online': 'Paiement en ligne',
      'cash_on_delivery': 'Paiement à la livraison'
    };
    return methodLabels[method] || method;
  }

  getStatusColor(status: string): string {
    const statusColors: { [key: string]: string } = {
      // Statuts français (anciens)
      'en_attente': 'warn',
      'expediee': 'accent',
      'livree': 'primary',
      'annulee': '',
      // Statuts anglais (nouveaux)
      'pending': 'warn',
      'confirmed': 'accent',
      'processing': 'accent',
      'shipped': 'accent',
      'delivered': 'primary',
      'cancelled': ''
    };
    return statusColors[status] || '';
  }

  downloadInvoice(order: Order): void {
    // Utiliser le nouveau système de factures
    window.open(`http://localhost:8000/api/invoices/${order.id}/download`, '_blank');
  }

  viewInvoice(order: Order): void {
    // Voir la facture dans le navigateur
    window.open(`http://localhost:8000/api/invoices/${order.id}/view`, '_blank');
  }

  generateInvoice(order: Order): void {
    // Générer une nouvelle facture
    this.orderService.generateInvoice(order.id).subscribe({
      next: (response) => {
        this.snackBar.open('Facture générée avec succès', 'Fermer', { duration: 3000 });
        this.loadOrderHistory(); // Recharger pour mettre à jour l'URL de facture
      },
      error: (error) => {
        console.error('Erreur lors de la génération de la facture:', error);
        this.snackBar.open('Erreur lors de la génération de la facture', 'Fermer', { duration: 3000 });
      }
    });
  }

  canDownloadInvoice(order: Order): boolean {
    // Vérifier les statuts de paiement (français et anglais)
    const isPaid = order.payment_status === 'paid' || order.payment_status === 'paye';
    
    // Vérifier les statuts de commande (français et anglais)
    const isDelivered = order.status === 'delivered' || order.status === 'livree';
    
    // Vérifier si une facture existe déjà
    const hasInvoice = order.invoice_url !== null && order.invoice_url !== undefined;
    
    return isPaid || isDelivered || hasInvoice;
  }

  reorderItems(order: Order): void {
    // Ajouter les articles au panier - à implémenter avec le service panier
    this.snackBar.open('Articles ajoutés au panier', 'Fermer', { duration: 3000 });
    console.log('Recommander:', order);
  }

  cancelOrder(order: Order): void {
    if (order.status !== 'en_attente') {
      this.snackBar.open('Cette commande ne peut plus être annulée', 'Fermer', { duration: 3000 });
      return;
    }

    this.orderService.cancelOrder(order.id).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.loadOrderHistory(); // Recharger la liste
      },
      error: (error) => {
        console.error('Erreur lors de l\'annulation de la commande:', error);
        this.snackBar.open('Erreur lors de l\'annulation de la commande', 'Fermer', { duration: 3000 });
      }
    });
  }

  passwordMatchValidator(form: FormGroup) {
    const newPassword = form.get('newPassword');
    const confirmPassword = form.get('confirmPassword');
    
    if (newPassword && confirmPassword && newPassword.value !== confirmPassword.value) {
      confirmPassword.setErrors({ passwordMismatch: true });
      return { passwordMismatch: true };
    }
    
    return null;
  }

  updateProfile(): void {
    if (this.profileForm.invalid || this.isLoading) {
      return;
    }

    this.isLoading = true;
    const profileData = this.profileForm.value;

    this.profileService.updateProfile(profileData).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        // Mettre à jour les données utilisateur dans le service auth
        this.authService.updateCurrentUser(response.user);
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors de la mise à jour du profil:', error);
        this.snackBar.open('Erreur lors de la mise à jour du profil', 'Fermer', { duration: 3000 });
        this.isLoading = false;
      }
    });
  }

  updatePassword(): void {
    if (this.passwordForm.invalid || this.isLoading) {
      return;
    }

    this.isLoading = true;
    const passwordData = {
      current_password: this.passwordForm.value.currentPassword,
      new_password: this.passwordForm.value.newPassword,
      new_password_confirmation: this.passwordForm.value.confirmPassword
    };

    this.profileService.updatePassword(passwordData).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.passwordForm.reset();
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors de la mise à jour du mot de passe:', error);
        this.snackBar.open('Erreur lors de la mise à jour du mot de passe', 'Fermer', { duration: 3000 });
        this.isLoading = false;
      }
    });
  }

  // Gestion de l'avatar
  onAvatarSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      // Vérifier le type de fichier
      if (!file.type.startsWith('image/')) {
        this.snackBar.open('Veuillez sélectionner une image', 'Fermer', { duration: 3000 });
        return;
      }

      // Vérifier la taille (max 2MB)
      if (file.size > 2 * 1024 * 1024) {
        this.snackBar.open('L\'image ne doit pas dépasser 2MB', 'Fermer', { duration: 3000 });
        return;
      }

      this.avatarFile = file;

      // Créer un aperçu
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.avatarPreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  uploadAvatar(): void {
    if (!this.avatarFile) {
      this.snackBar.open('Veuillez sélectionner une image', 'Fermer', { duration: 3000 });
      return;
    }

    this.isLoading = true;

    this.profileService.uploadAvatar(this.avatarFile).subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.avatarPreview = response.avatar_url;
        this.avatarFile = null;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors de l\'upload de l\'avatar:', error);
        this.snackBar.open('Erreur lors de l\'upload de l\'avatar', 'Fermer', { duration: 3000 });
        this.isLoading = false;
      }
    });
  }

  deleteAvatar(): void {
    this.profileService.deleteAvatar().subscribe({
      next: (response) => {
        this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        this.avatarPreview = null;
        this.avatarFile = null;
      },
      error: (error) => {
        console.error('Erreur lors de la suppression de l\'avatar:', error);
        this.snackBar.open('Erreur lors de la suppression de l\'avatar', 'Fermer', { duration: 3000 });
      }
    });
  }

  getErrorMessage(form: FormGroup, field: string): string {
    const control = form.get(field);
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
    if (control.errors['passwordMismatch']) {
      return 'Les mots de passe ne correspondent pas';
    }
    return 'Valeur invalide';
  }
}