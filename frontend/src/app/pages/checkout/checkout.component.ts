import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
// @ts-ignore
import { OrderService } from '../../services/order.service';
import { CartService } from '../../services/cart.service';
import { PayDunyaService } from '../../services/paydunya.service';
import { AuthService } from '../../services/auth.service';
import { Order, PaymentMethod } from '../../models/order.interface';
import { firstValueFrom } from 'rxjs';
import { MatCard, MatCardContent, MatCardHeader, MatCardTitle } from '@angular/material/card';
import { MatFormField, MatLabel, MatError, MatInput } from '@angular/material/input';
import { MatRadioModule } from '@angular/material/radio';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSelectModule } from '@angular/material/select';
import { MatChipsModule } from '@angular/material/chips';
import { MatTooltipModule } from '@angular/material/tooltip';
import { environment } from '../../../environments/environment';

// Options de paiement simplifiées
export enum ExtendedPaymentMethod {
  CARD = 'card',
  WAVE = 'wave',
  ORANGE_MONEY = 'orange_money',
  FREE_MONEY = 'free_money',
  CASH_ON_DELIVERY = 'cash_on_delivery'
}

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardTitle,
    MatCardHeader,
    MatCard,
    MatCardContent,
    MatFormField,
    MatLabel,
    MatError,
    MatInput,
    MatRadioModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatSelectModule,
    MatChipsModule,
    MatTooltipModule
  ],
  providers: [OrderService],
  templateUrl: './checkout.component.html',
  styleUrls: ['./checkout.component.css']
})
export class CheckoutComponent implements OnInit {
  backendUrl = environment.apiUrl.replace('/api', ''); // Obtenir l'URL de base du backend

  checkoutForm: FormGroup;
  paymentMethods = ExtendedPaymentMethod;
  isSubmitting = false;

  constructor(
    private fb: FormBuilder,
    private orderService: OrderService,
    protected cartService: CartService,
    private payDunyaService: PayDunyaService,
    private router: Router,
    private snackBar: MatSnackBar,
    private authService: AuthService
  ) {
    this.checkoutForm = this.fb.group({
      customerInfo: this.fb.group({
        firstName: ['', Validators.required],
        lastName: ['', Validators.required],
        email: ['', [Validators.required, Validators.email]],
        phone: ['', Validators.required]
      }),
      deliveryAddress: this.fb.group({
        street: ['', Validators.required],
        city: ['', Validators.required],
        postalCode: ['', Validators.required],
        country: ['', Validators.required],
        additionalInfo: ['']
      }),
      paymentMethod: [ExtendedPaymentMethod.CARD, Validators.required]
    });
  }

  ngOnInit(): void {
    const items = this.cartService.getCartItems();
    if (!items || items.length === 0) {
      this.snackBar.open('Votre panier est vide', 'OK', { duration: 3000 });
      this.router.navigate(['/cart']);
      return;
    }

    // Restaurer les données du formulaire après une redirection de connexion/inscription
    const savedFormData = localStorage.getItem('checkout_form_data');
    if (savedFormData) {
      this.checkoutForm.patchValue(JSON.parse(savedFormData));
      localStorage.removeItem('checkout_form_data');
    } else {
      // Sinon, charger les données client habituelles
      this.loadSavedCustomerData();
    }

    // Rediriger si l'utilisateur non authentifié choisit le paiement en ligne
    this.checkoutForm.get('paymentMethod')?.valueChanges.subscribe(value => {
      if (value !== ExtendedPaymentMethod.CASH_ON_DELIVERY && !this.authService.isAuthenticated()) {
        // Sauvegarder les données du formulaire pour les restaurer après la connexion
        localStorage.setItem('checkout_form_data', JSON.stringify(this.checkoutForm.value));

        this.snackBar.open('Veuillez vous connecter ou créer un compte pour payer en ligne.', 'OK', {
          duration: 5000,
          verticalPosition: 'top'
        });
        this.router.navigate(['/auth/login'], { queryParams: { returnUrl: '/checkout' } });
      }
    });
  }

  async onSubmit(): Promise<void> {
    if (this.checkoutForm.invalid || this.isSubmitting) {
      this.checkoutForm.markAllAsTouched();
      return;
    }

    this.isSubmitting = true;

    try {
      const formValue = this.checkoutForm.value;
      const cartItems = this.cartService.getCartItems();
      const totalAmount = this.cartService.getTotalAmount() || 0;

      // Créer la commande d'abord
      const orderData: Omit<Order, 'orderId' | 'status' | 'createdAt'> = {
        customerInfo: formValue.customerInfo,
        deliveryAddress: formValue.deliveryAddress,
        paymentMethod: this.mapPaymentMethod(formValue.paymentMethod),
        items: cartItems.map(item => ({
          productId: item.product.id,
          quantity: item.quantity,
          price: item.product.price
        })),
        totalAmount
      };

      console.log('Données envoyées au serveur:', orderData);

      const order = await firstValueFrom(
        this.orderService.createOrder(orderData)
      );

      console.log('Commande créée:', order);

      // Sauvegarder les données du client pour la prochaine fois
      this.saveCustomerDataAfterOrder(formValue);

      // Traitement selon le mode de paiement
      if (formValue.paymentMethod === ExtendedPaymentMethod.CASH_ON_DELIVERY) {
        await this.processCashOnDelivery(order);
      } else {
        await this.processPayDunyaPayment(order);
      }

    } catch (err: any) {
      console.error('Erreur de commande:', err);
      
      // Afficher les erreurs de validation spécifiques
      if (err.status === 422 && err.error?.errors) {
        console.error('Erreurs de validation:', err.error.errors);
        const errorMessages = Object.values(err.error.errors).flat().join(', ');
        this.snackBar.open(
          `Erreurs de validation: ${errorMessages}`,
          'OK',
          { duration: 8000 }
        );
      } else {
        this.snackBar.open(
          err.error?.message || 'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.',
          'OK',
          { duration: 5000 }
        );
      }
    } finally {
      this.isSubmitting = false;
    }
  }

  
  /**
   * Charger les données sauvegardées du client
   */
  private loadSavedCustomerData(): void {
    try {
      const savedCustomerInfo = localStorage.getItem('customerInfo');
      const savedDeliveryAddress = localStorage.getItem('deliveryAddress');
      const savedPaymentMethod = localStorage.getItem('preferredPaymentMethod');

      if (savedCustomerInfo) {
        const customerInfo = JSON.parse(savedCustomerInfo);
        this.checkoutForm.get('customerInfo')?.patchValue(customerInfo);
      }

      if (savedDeliveryAddress) {
        const deliveryAddress = JSON.parse(savedDeliveryAddress);
        this.checkoutForm.get('deliveryAddress')?.patchValue(deliveryAddress);
      }

      if (savedPaymentMethod) {
        this.checkoutForm.get('paymentMethod')?.setValue(savedPaymentMethod);
      }

      // Écouter les changements du formulaire pour sauvegarder automatiquement
      this.setupAutoSave();

    } catch (error) {
      console.error('Erreur lors du chargement des données sauvegardées:', error);
    }
  }

  /**
   * Configurer la sauvegarde automatique des données
   */
  private setupAutoSave(): void {
    // Sauvegarder les informations client
    this.checkoutForm.get('customerInfo')?.valueChanges.subscribe(value => {
      if (value && Object.values(value).some(v => v)) {
        localStorage.setItem('customerInfo', JSON.stringify(value));
      }
    });

    // Sauvegarder l'adresse de livraison
    this.checkoutForm.get('deliveryAddress')?.valueChanges.subscribe(value => {
      if (value && Object.values(value).some(v => v)) {
        localStorage.setItem('deliveryAddress', JSON.stringify(value));
      }
    });

    // Sauvegarder la méthode de paiement préférée
    this.checkoutForm.get('paymentMethod')?.valueChanges.subscribe(value => {
      if (value) {
        localStorage.setItem('preferredPaymentMethod', value);
      }
    });
  }

  /**
   * Sauvegarder les données après une commande réussie
   */
  private saveCustomerDataAfterOrder(formValue: any): void {
    try {
      // Sauvegarder les données pour la prochaine commande
      localStorage.setItem('customerInfo', JSON.stringify(formValue.customerInfo));
      localStorage.setItem('deliveryAddress', JSON.stringify(formValue.deliveryAddress));
      localStorage.setItem('preferredPaymentMethod', formValue.paymentMethod);
      
      // Optionnel: Sauvegarder l'historique des adresses
      this.saveAddressToHistory(formValue.deliveryAddress);
    } catch (error) {
      console.error('Erreur lors de la sauvegarde des données:', error);
    }
  }

  /**
   * Sauvegarder l'adresse dans l'historique
   */
  private saveAddressToHistory(address: any): void {
    try {
      const addressHistory = JSON.parse(localStorage.getItem('addressHistory') || '[]');
      
      // Vérifier si l'adresse existe déjà
      const existingIndex = addressHistory.findIndex((addr: any) => 
        addr.street === address.street && 
        addr.city === address.city && 
        addr.postalCode === address.postalCode
      );

      if (existingIndex === -1) {
        // Ajouter la nouvelle adresse
        addressHistory.unshift({
          ...address,
          savedAt: new Date().toISOString()
        });

        // Garder seulement les 5 dernières adresses
        if (addressHistory.length > 5) {
          addressHistory.splice(5);
        }

        localStorage.setItem('addressHistory', JSON.stringify(addressHistory));
      }
    } catch (error) {
      console.error('Erreur lors de la sauvegarde de l\'historique d\'adresses:', error);
    }
  }

  /**
   * Effacer les données sauvegardées
   */
  clearSavedData(): void {
    localStorage.removeItem('customerInfo');
    localStorage.removeItem('deliveryAddress');
    localStorage.removeItem('preferredPaymentMethod');
    this.checkoutForm.reset();
    this.checkoutForm.get('paymentMethod')?.setValue(ExtendedPaymentMethod.CARD);
    this.snackBar.open('Données effacées', 'OK', { duration: 2000 });
  }

  /**
   * Obtenir l'historique des adresses
   */
  getAddressHistory(): any[] {
    try {
      return JSON.parse(localStorage.getItem('addressHistory') || '[]');
    } catch {
      return [];
    }
  }

  /**
   * Utiliser une adresse de l'historique
   */
  useAddressFromHistory(address: any): void {
    this.checkoutForm.get('deliveryAddress')?.patchValue({
      street: address.street,
      city: address.city,
      postalCode: address.postalCode,
      country: address.country,
      additionalInfo: address.additionalInfo || ''
    });
    this.snackBar.open('Adresse chargée', 'OK', { duration: 2000 });
  }

  /**
   * Traiter le paiement PayDunya
   */
  private async processPayDunyaPayment(order: any): Promise<void> {
    try {
      this.snackBar.open('Initialisation du paiement PayDunya...', '', { duration: 2000 });

      // Récupérer l'ID de la commande depuis la réponse
      const orderId = order.data?.id || order.id;
      
      console.log('Order ID pour PayDunya:', orderId);
      console.log('Order complet:', order);

      if (!orderId) {
        throw new Error('ID de commande manquant');
      }

      const payDunyaResponse = await firstValueFrom(
        this.payDunyaService.initiatePayment({ order_id: orderId })
      );

      if (payDunyaResponse.success && payDunyaResponse.data) {
        localStorage.setItem('pending_order_id', orderId.toString());
        localStorage.setItem('paydunya_invoice_token', payDunyaResponse.data.invoice_token);

        this.snackBar.open('Redirection vers PayDunya...', '', { duration: 2000 });
        this.payDunyaService.redirectToPayDunya(payDunyaResponse.data.payment_url);

      } else {
        throw new Error(payDunyaResponse.message || 'Erreur lors de l\'initialisation du paiement PayDunya');
      }

    } catch (error: any) {
      console.error('Erreur paiement PayDunya:', error);
      this.snackBar.open(
        error.message || 'Erreur lors du paiement PayDunya',
        'OK',
        { duration: 5000 }
      );
      throw error;
    }
  }

  /**
   * Traiter le paiement à la livraison
   */
  private async processCashOnDelivery(order: any): Promise<void> {
    try {
      // Récupérer l'ID de la commande depuis la réponse
      const orderId = order.data?.id || order.id;
      
      console.log('Order ID pour confirmation:', orderId);
      console.log('Order complet:', order);

      if (!orderId) {
        throw new Error('ID de commande manquant');
      }

      // L'email de confirmation est maintenant envoyé automatiquement lors de la création
      // Pas besoin d'appel supplémentaire

      this.cartService.clearCart();
      this.snackBar.open('Commande confirmée avec succès !', 'OK', { duration: 5000 });
      
      this.router.navigate(['/order-confirmation'], {
        queryParams: { orderId: orderId }
      });

    } catch (error: any) {
      console.error('Erreur confirmation commande:', error);
      this.snackBar.open(
        'Erreur lors de la confirmation de la commande',
        'OK',
        { duration: 5000 }
      );
      throw error;
    }
  }

  /**
   * Mapper les méthodes de paiement étendues vers l'enum original
   */
  private mapPaymentMethod(extendedMethod: ExtendedPaymentMethod): PaymentMethod {
    switch (extendedMethod) {
      case ExtendedPaymentMethod.CASH_ON_DELIVERY:
        return 'cash_on_delivery' as PaymentMethod;
      default:
        // Toutes les autres méthodes sont des paiements en ligne
        return 'online' as PaymentMethod;
    }
  }

  /**
   * Obtenir le libellé d'une méthode de paiement
   */
  getPaymentMethodLabel(method: ExtendedPaymentMethod): string {
    const labels = {
      [ExtendedPaymentMethod.CARD]: 'Carte Bancaire',
      [ExtendedPaymentMethod.WAVE]: 'Wave',
      [ExtendedPaymentMethod.ORANGE_MONEY]: 'Orange Money',
      [ExtendedPaymentMethod.FREE_MONEY]: 'Free Money',
      [ExtendedPaymentMethod.CASH_ON_DELIVERY]: 'Paiement à la livraison',
    };
    return labels[method] || 'Paiement en ligne';
  }

  /**
   * Obtenir l'icône d'une méthode de paiement
   */
  getPaymentMethodLogo(method: ExtendedPaymentMethod): string | null {
    const logos: { [key: string]: string } = {
      [ExtendedPaymentMethod.WAVE]: '/storage/wave.png',
      [ExtendedPaymentMethod.ORANGE_MONEY]: '/storage/orangemoney.png',
      [ExtendedPaymentMethod.FREE_MONEY]: '/storage/freemoney.png',
    };

    const path = logos[method];
    return path ? `${this.backendUrl}${path}` : null;
  }

  getPaymentMethodIcon(method: ExtendedPaymentMethod): string {
    const icons = {
      [ExtendedPaymentMethod.CARD]: 'credit_card',
      [ExtendedPaymentMethod.WAVE]: 'phone_iphone',
      [ExtendedPaymentMethod.ORANGE_MONEY]: 'phone_iphone',
      [ExtendedPaymentMethod.FREE_MONEY]: 'phone_iphone',
      [ExtendedPaymentMethod.CASH_ON_DELIVERY]: 'local_shipping',
    };
    return icons[method] || 'payment';
  }

  /**
   * Obtenir la description d'une méthode de paiement
   */
  getPaymentMethodDescription(method: ExtendedPaymentMethod): string {
    const descriptions = {
      [ExtendedPaymentMethod.CARD]: 'Paiement sécurisé par carte via PayDunya.',
      [ExtendedPaymentMethod.WAVE]: 'Paiement sécurisé avec votre compte Wave.',
      [ExtendedPaymentMethod.ORANGE_MONEY]: 'Paiement sécurisé avec Orange Money.',
      [ExtendedPaymentMethod.FREE_MONEY]: 'Paiement sécurisé avec Free Money.',
      [ExtendedPaymentMethod.CASH_ON_DELIVERY]: 'Payez en espèces à la réception de votre commande.',
    };
    return descriptions[method] || 'Paiement en ligne sécurisé';
  }

  getErrorMessage(formGroupName: string, controlName: string): string {
    const control = this.checkoutForm.get(`${formGroupName}.${controlName}`);
    if (!control || !control.errors || !control.touched) return '';

    if (control.errors['required']) {
      return 'Ce champ est obligatoire';
    } else if (control.errors['email']) {
      return 'Veuillez entrer une adresse email valide';
    }
    return 'Valeur invalide';
  }
}