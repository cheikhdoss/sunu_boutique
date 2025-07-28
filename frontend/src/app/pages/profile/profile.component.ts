import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { MatTabsModule } from '@angular/material/tabs';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSelectModule } from '@angular/material/select';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog } from '@angular/material/dialog';
import { MatDividerModule } from '@angular/material/divider';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { MatMenuModule } from '@angular/material/menu';
import { UserService, UserProfile, Address } from '../../services/user.service';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { environment } from '../../../environments/environment';

// Interface pour Order
interface Order {
  id: number;
  order_number: string;
  status: string;
  total: number;
  payment_method: string;
  created_at: string;
  items?: any[];
  tracking_number?: string;
}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    RouterModule,
    MatTabsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatSelectModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatDividerModule,
    MatChipsModule,
    MatProgressBarModule,
    MatMenuModule
  ],
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
  @ViewChild('avatarInput') avatarInput!: ElementRef;

  profileForm: FormGroup;
  passwordForm: FormGroup;
  userProfile: UserProfile | null = null;
  addresses: Address[] = [];
  recentOrders: Order[] = [];
  userStats: any = {};
  isLoadingProfile = false;
  isLoadingPassword = false;
  isUploadingAvatar = false;
  selectedTabIndex = 0;

  // Propriétés pour les animations
  animatedStats = {
    total_orders: 0,
    total_spent: 0,
    pending_orders: 0,
    completed_orders: 0
  };
  
  favoriteProducts: any[] = [];
  
  // Filtres pour les commandes
  orderSearchTerm = '';
  selectedOrderStatus = '';
  selectedOrderPeriod = '';
  filteredOrders: Order[] = [];
  
  // Indicateurs de sécurité
  needsPasswordUpdate = false;
  lastPasswordChange: Date | null = null;

  // Référence à Math pour le template
  Math = Math;

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private authService: AuthService,
    private http: HttpClient,
    private snackBar: MatSnackBar,
    private dialog: MatDialog,
    private router: Router
  ) {
    this.profileForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      phone: [''],
      date_of_birth: [''],
      gender: ['']
    });

    this.passwordForm = this.fb.group({
      current_password: ['', [Validators.required]],
      new_password: ['', [Validators.required, Validators.minLength(8)]],
      new_password_confirmation: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit(): void {
    // Vérifier si l'utilisateur est authentifié
    if (!this.authService.isAuthenticated()) {
      this.snackBar.open('Vous devez être connecté pour acc��der à cette page', 'Fermer', { duration: 3000 });
      this.router.navigate(['/auth/login']);
      return;
    }

    // Charger les données directement sans test préalable
    this.loadUserProfile();
    
    // Attendre un peu avant de charger les autres données
    setTimeout(() => {
      this.loadAddresses();
      this.loadUserOrders();
      this.loadUserStats();
      this.loadFavoriteProducts();
    }, 500);
  }

  passwordMatchValidator(form: FormGroup) {
    const newPassword = form.get('new_password');
    const confirmPassword = form.get('new_password_confirmation');
    
    if (newPassword && confirmPassword && newPassword.value !== confirmPassword.value) {
      confirmPassword.setErrors({ passwordMismatch: true });
      return { passwordMismatch: true };
    }
    return null;
  }

  loadUserProfile(): void {
    const currentUser = this.authService.currentUser;

    if (currentUser) {
      // Convertir User en UserProfile en s'assurant que tous les champs requis sont présents
      this.userProfile = {
        id: currentUser.id,
        name: currentUser.name,
        email: currentUser.email,
        phone: currentUser.phone,
        date_of_birth: currentUser.date_of_birth,
        gender: currentUser.gender,
        avatar: currentUser.avatar,
        email_verified_at: currentUser.email_verified_at,
        created_at: currentUser.created_at,
        updated_at: currentUser.updated_at || currentUser.created_at // Fallback si updated_at n'existe pas
      };

      this.profileForm.patchValue({
        name: currentUser.name,
        email: currentUser.email,
        phone: currentUser.phone || '',
        date_of_birth: currentUser.date_of_birth ? new Date(currentUser.date_of_birth) : '',
        gender: currentUser.gender || ''
      });

      // We can also check password status if needed
      // this.checkPasswordStatus(currentUser);

    } else {
        // If no user is found, something is wrong, maybe redirect
        this.snackBar.open('Données utilisateur non trouvées, veuillez vous reconnecter.', 'Fermer', { duration: 3000 });
        this.router.navigate(['/auth/login']);
    }

    // The redundant API call is now correctly commented out.
    /*
    this.http.get<any>(`${environment.apiUrl}/auth/me`).subscribe({
      next: (response: any) => {
        const user = response.user || response;
        this.userProfile = {
          id: user.id,
          name: user.name,
          email: user.email,
          phone: user.phone,
          date_of_birth: user.date_of_birth,
          gender: user.gender,
          avatar: user.avatar,
          email_verified_at: user.email_verified_at,
          created_at: user.created_at,
          updated_at: user.updated_at
        };
        
        this.profileForm.patchValue({
          name: user.name,
          email: user.email,
          phone: user.phone || '',
          date_of_birth: user.date_of_birth ? new Date(user.date_of_birth) : '',
          gender: user.gender || ''
        });

        // this.checkPasswordStatus(user);
      },
      error: (err) => {
        console.error('Failed to load user profile from API', err);
      }
    });
    */
  }

  loadAddresses(): void {
    this.userService.getAddresses().subscribe({
      next: (addresses: Address[]) => {
        this.addresses = addresses;
      },
      error: (error: any) => {
        console.error('Erreur lors du chargement des adresses:', error);
      }
    });
  }

  loadUserOrders(): void {
    this.userService.getUserOrders().subscribe({
      next: (orders: Order[]) => {
        this.recentOrders = orders;
        this.filteredOrders = [...this.recentOrders];
      },
      error: (error: any) => {
        console.error('Erreur lors du chargement des commandes:', error);
        // Fallback avec données vides
        this.recentOrders = [];
        this.filteredOrders = [];
      }
    });
  }

  loadUserStats(): void {
    this.userService.getUserStats().subscribe({
      next: (stats: any) => {
        this.userStats = stats;
        this.animateStats();
      },
      error: (error: any) => {
        console.error('Erreur lors du chargement des statistiques:', error);
        // Fallback avec des valeurs par défaut
        this.userStats = {
          total_orders: 0,
          total_spent: 0,
          pending_orders: 0,
          completed_orders: 0
        };
        this.animateStats();
      }
    });
  }

  loadFavoriteProducts(): void {
    this.userService.getUserFavorites().subscribe({
      next: (favorites: any[]) => {
        this.favoriteProducts = favorites;
      },
      error: (error: any) => {
        console.error('Erreur lors du chargement des favoris:', error);
        // Fallback avec liste vide
        this.favoriteProducts = [];
      }
    });
  }

  // Animations simplifiées
  animateStats(): void {
    if (!this.userStats) return;

    const duration = 1000;
    const steps = 30;
    const stepDuration = duration / steps;

    const targets = {
      total_orders: this.userStats.total_orders || 0,
      total_spent: this.userStats.total_spent || 0,
      pending_orders: this.userStats.pending_orders || 0,
      completed_orders: this.userStats.completed_orders || 0
    };

    let currentStep = 0;

    const animate = () => {
      currentStep++;
      const progress = currentStep / steps;

      this.animatedStats = {
        total_orders: Math.floor(targets.total_orders * progress),
        total_spent: Math.floor(targets.total_spent * progress),
        pending_orders: Math.floor(targets.pending_orders * progress),
        completed_orders: Math.floor(targets.completed_orders * progress)
      };

      if (currentStep < steps) {
        setTimeout(animate, stepDuration);
      }
    };

    animate();
  }

  // Gestion de l'avatar avec persistance
  openAvatarUpload(): void {
    this.avatarInput.nativeElement.click();
  }

  onAvatarChange(event: any): void {
    const file = event.target.files[0];
    if (file) {
      // Validation du fichier
      if (!file.type.startsWith('image/')) {
        this.snackBar.open('Veuillez sélectionner une image valide', 'Fermer', { duration: 3000 });
        return;
      }

      if (file.size > 5 * 1024 * 1024) { // 5MB max
        this.snackBar.open('L\'image ne doit pas dépasser 5MB', 'Fermer', { duration: 3000 });
        return;
      }

      // Prévisualisation immédiate
      const reader = new FileReader();
      reader.onload = (e: any) => {
        if (this.userProfile) {
          this.userProfile.avatar = e.target.result;
        }
      };
      reader.readAsDataURL(file);

      // Upload réel vers l'API
      this.uploadAvatar(file);
    }
  }

  private uploadAvatar(file: File): void {
    this.isUploadingAvatar = true;
    this.snackBar.open('Upload en cours...', '', { duration: 2000 });
    
    this.userService.uploadAvatar(file).subscribe({
      next: (response) => {
        if (this.userProfile) {
          this.userProfile.avatar = response.avatar_url;
        }
        this.snackBar.open('Photo de profil mise à jour avec succès', 'Fermer', { duration: 3000 });
        this.isUploadingAvatar = false;
      },
      error: (error) => {
        console.error('Erreur lors de l\'upload:', error);
        this.snackBar.open(
          error.error?.message || 'Erreur lors de l\'upload de l\'image',
          'Fermer',
          { duration: 5000 }
        );
        this.isUploadingAvatar = false;
        // Recharger le profil en cas d'erreur pour restaurer l'avatar précédent
        this.loadUserProfile();
      }
    });
  }

  removeAvatar(): void {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
      this.userService.removeAvatar().subscribe({
        next: () => {
          if (this.userProfile) {
            this.userProfile.avatar = undefined;
          }
          this.snackBar.open('Photo de profil supprimée', 'Fermer', { duration: 3000 });
        },
        error: (error) => {
          console.error('Erreur lors de la suppression:', error);
          this.snackBar.open(
            error.error?.message || 'Erreur lors de la suppression de l\'image',
            'Fermer',
            { duration: 5000 }
          );
        }
      });
    }
  }

  updateProfile(): void {
    if (this.profileForm.invalid) {
      this.snackBar.open('Veuillez remplir tous les champs requis', 'Fermer', { duration: 3000 });
      return;
    }

    this.isLoadingProfile = true;
    const profileData = this.profileForm.value;

    this.userService.updateProfile(profileData).subscribe({
      next: (response) => {
        if (this.userProfile) {
          this.userProfile = {
            ...this.userProfile,
            ...response.user
          };
          this.snackBar.open(response.message, 'Fermer', { duration: 3000 });
        }
        this.isLoadingProfile = false;
      },
      error: (error) => {
        console.error('Erreur lors de la mise à jour du profil:', error);
        this.snackBar.open(
          error.error?.message || 'Erreur lors de la mise à jour du profil',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoadingProfile = false;
      }
    });
  }

  shareProfile(): void {
    this.snackBar.open('Partage de profil en cours de développement', 'Fermer', { duration: 3000 });
  }

  // Calculs
  getCompletionRate(): number {
    const total = this.userStats.total_orders || 0;
    const completed = this.userStats.completed_orders || 0;
    return total > 0 ? Math.round((completed / total) * 100) : 0;
  }

  getProfileCompleteness(): number {
    if (!this.userProfile) return 0;
    
    let completed = 0;
    const fields = ['name', 'email', 'phone', 'date_of_birth', 'gender'];
    
    fields.forEach(field => {
      if (this.userProfile && (this.userProfile as any)[field]) {
        completed++;
      }
    });
    
    return Math.round((completed / fields.length) * 100);
  }

  // Gestion des commandes
  filterOrders(): void {
    this.filteredOrders = this.recentOrders.filter(order => {
      const matchesSearch = !this.orderSearchTerm || 
        order.order_number.toLowerCase().includes(this.orderSearchTerm.toLowerCase());
      
      const matchesStatus = !this.selectedOrderStatus || 
        order.status === this.selectedOrderStatus;
      
      return matchesSearch && matchesStatus;
    });
  }

  clearOrderFilters(): void {
    this.orderSearchTerm = '';
    this.selectedOrderStatus = '';
    this.selectedOrderPeriod = '';
    this.filterOrders();
  }

  trackByOrderId(index: number, order: Order): number {
    return order.id;
  }

  getOrderStatusIcon(status: string): string {
    switch (status) {
      case 'pending': return 'schedule';
      case 'processing': return 'inventory';
      case 'shipped': return 'local_shipping';
      case 'delivered': return 'check_circle';
      case 'cancelled': return 'cancel';
      default: return 'help';
    }
  }

  isStepCompleted(order: Order, step: string): boolean {
    const steps = ['pending', 'processing', 'shipped', 'delivered'];
    const currentIndex = steps.indexOf(order.status);
    const stepIndex = steps.indexOf(step);
    return stepIndex <= currentIndex;
  }

  canReorder(order: Order): boolean {
    return order.status === 'delivered';
  }

  canCancelOrder(order: Order): boolean {
    return ['pending', 'processing'].includes(order.status);
  }

  canTrackOrder(order: Order): boolean {
    return ['shipped', 'delivered'].includes(order.status) && !!order.tracking_number;
  }

  reorderItems(orderId: number): void {
    this.snackBar.open('Recommande en cours de développement', 'Fermer', { duration: 3000 });
  }

  cancelOrder(orderId: number): void {
    this.snackBar.open('Annulation de commande en cours de développement', 'Fermer', { duration: 3000 });
  }

  trackOrder(orderId: number): void {
    this.snackBar.open('Suivi de commande en cours de développement', 'Fermer', { duration: 3000 });
  }

  // Gestion des favoris avec API
  addToCart(product: any): void {
    this.snackBar.open(`${product.name} ajouté au panier`, 'Fermer', { duration: 3000 });
  }

  removeFromFavorites(productId: number): void {
    this.userService.removeFavorite(productId).subscribe({
      next: () => {
        this.favoriteProducts = this.favoriteProducts.filter(p => p.id !== productId);
        this.snackBar.open('Produit retiré des favoris', 'Fermer', { duration: 3000 });
      },
      error: (error) => {
        console.error('Erreur lors de la suppression du favori:', error);
        this.snackBar.open('Erreur lors de la suppression', 'Fermer', { duration: 3000 });
      }
    });
  }

  viewProduct(productId: number): void {
    this.router.navigate(['/products', productId]);
  }

  shareFavorites(): void {
    this.snackBar.open('Partage de favoris en cours de développement', 'Fermer', { duration: 3000 });
  }

  clearAllFavorites(): void {
    if (confirm('Êtes-vous sûr de vouloir vider votre liste de favoris ?')) {
      // Supprimer tous les favoris un par un
      const deletePromises = this.favoriteProducts.map(product => 
        this.userService.removeFavorite(product.id).toPromise()
      );
      
      Promise.all(deletePromises).then(() => {
        this.favoriteProducts = [];
        this.snackBar.open('Liste de favoris vidée', 'Fermer', { duration: 3000 });
      }).catch((error) => {
        console.error('Erreur lors de la suppression des favoris:', error);
        this.snackBar.open('Erreur lors de la suppression', 'Fermer', { duration: 3000 });
      });
    }
  }

  // Sécurité du mot de passe
  getPasswordStrength(): number {
    const password = this.passwordForm.get('new_password')?.value || '';
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    
    return strength;
  }

  getPasswordStrengthColor(): string {
    const strength = this.getPasswordStrength();
    if (strength < 50) return 'warn';
    if (strength < 75) return 'accent';
    return 'primary';
  }

  getPasswordStrengthLabel(): string {
    const strength = this.getPasswordStrength();
    if (strength < 25) return 'Très faible';
    if (strength < 50) return 'Faible';
    if (strength < 75) return 'Moyen';
    if (strength < 100) return 'Fort';
    return 'Très fort';
  }

  // Méthodes principales
  onUpdateProfile(): void {
    if (this.profileForm.invalid || this.isLoadingProfile) {
      return;
    }

    this.isLoadingProfile = true;
    const formValue = this.profileForm.value;
    
    const profileData = {
      ...formValue,
      date_of_birth: formValue.date_of_birth ? 
        new Date(formValue.date_of_birth).toISOString().split('T')[0] : null
    };

    this.userService.updateProfile(profileData).subscribe({
      next: (response: any) => {
        this.userProfile = response.user || response;
        this.snackBar.open('Profil mis à jour avec succès', 'Fermer', { duration: 3000 });
        this.isLoadingProfile = false;
      },
      error: (error: any) => {
        console.error('Erreur lors de la mise à jour du profil:', error);
        this.snackBar.open(
          error.error?.message || 'Erreur lors de la mise à jour du profil',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoadingProfile = false;
      }
    });
  }

  onChangePassword(): void {
    if (this.passwordForm.invalid || this.isLoadingPassword) {
      return;
    }

    this.isLoadingPassword = true;
    const passwordData = this.passwordForm.value;

    this.userService.changePassword(passwordData).subscribe({
      next: () => {
        this.snackBar.open('Mot de passe modifié avec succès', 'Fermer', { duration: 3000 });
        this.passwordForm.reset();
        this.isLoadingPassword = false;
        this.lastPasswordChange = new Date();
      },
      error: (error: any) => {
        console.error('Erreur lors du changement de mot de passe:', error);
        this.snackBar.open(
          error.error?.message || 'Erreur lors du changement de mot de passe',
          'Fermer',
          { duration: 5000 }
        );
        this.isLoadingPassword = false;
      }
    });
  }

  onDeleteAccount(): void {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
      this.userService.deleteAccount().subscribe({
        next: () => {
          this.snackBar.open('Compte supprimé avec succès', 'Fermer', { duration: 3000 });
          this.authService.logout();
          this.router.navigate(['/']);
        },
        error: (error: any) => {
          console.error('Erreur lors de la suppression du compte:', error);
          this.snackBar.open(
            error.error?.message || 'Erreur lors de la suppression du compte',
            'Fermer',
            { duration: 5000 }
          );
        }
      });
    }
  }

  setDefaultAddress(addressId: number): void {
    this.userService.setDefaultAddress(addressId).subscribe({
      next: () => {
        this.snackBar.open('Adresse par défaut mise à jour', 'Fermer', { duration: 3000 });
        this.loadAddresses();
      },
      error: (error: any) => {
        console.error('Erreur lors de la mise à jour de l\'adresse par défaut:', error);
        this.snackBar.open('Erreur lors de la mise à jour', 'Fermer', { duration: 3000 });
      }
    });
  }

  deleteAddress(addressId: number): void {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?')) {
      this.userService.deleteAddress(addressId).subscribe({
        next: () => {
          this.snackBar.open('Adresse supprimée avec succès', 'Fermer', { duration: 3000 });
          this.loadAddresses();
        },
        error: (error: any) => {
          console.error('Erreur lors de la suppression de l\'adresse:', error);
          this.snackBar.open('Erreur lors de la suppression', 'Fermer', { duration: 3000 });
        }
      });
    }
  }

  addNewAddress(): void {
    this.snackBar.open('Formulaire adresse en cours de développement', 'Fermer', { duration: 3000 });
  }

  editAddress(addressId: number): void {
    this.snackBar.open('Edition adresse en cours de développement', 'Fermer', { duration: 3000 });
  }

  viewAllOrders(): void {
    this.snackBar.open('Fonctionnalité en cours de développement', 'Fermer', { duration: 3000 });
  }

  viewOrderDetails(orderId: number): void {
    this.snackBar.open('Fonctionnalité en cours de développement', 'Fermer', { duration: 3000 });
  }

  downloadInvoice(orderId: number): void {
    this.snackBar.open('Fonctionnalité en cours de développement', 'Fermer', { duration: 3000 });
  }

  getErrorMessage(formGroup: FormGroup, field: string): string {
    const control = formGroup.get(field);
    if (!control || !control.errors || !control.touched) return '';

    if (control.errors['required']) {
      return 'Ce champ est obligatoire';
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

  getStatusColor(status: string): string {
    switch (status) {
      case 'pending': return '#f59e0b';
      case 'processing': return '#3b82f6';
      case 'shipped': return '#8b5cf6';
      case 'delivered': return '#10b981';
      case 'cancelled': return '#ef4444';
      default: return '#6b7280';
    }
  }

  getStatusLabel(status: string): string {
    switch (status) {
      case 'pending': return 'En attente';
      case 'processing': return 'En traitement';
      case 'shipped': return 'Expédiée';
      case 'delivered': return 'Livrée';
      case 'cancelled': return 'Annulée';
      default: return status;
    }
  }
}