import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
// @ts-ignore
import { OrderService } from '../../services/order.service';
import { CartService } from '../../services/cart.service';
import { Order, PaymentMethod } from '../../models/order.interface';
import { firstValueFrom } from 'rxjs';
import { MatCard, MatCardContent, MatCardHeader, MatCardTitle } from '@angular/material/card';
import { MatFormField, MatLabel, MatError, MatInput } from '@angular/material/input';
import { MatRadioModule } from '@angular/material/radio';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

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
    MatIconModule
  ],
  providers: [OrderService],
  templateUrl: './checkout.component.html',
  styleUrls: ['./checkout.component.css']
})
export class CheckoutComponent implements OnInit {
  checkoutForm: FormGroup;
  paymentMethods = PaymentMethod;
  isSubmitting = false;

  constructor(
    private fb: FormBuilder,
    private orderService: OrderService,
    protected cartService: CartService,
    private router: Router,
    private snackBar: MatSnackBar
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
      paymentMethod: [PaymentMethod.ONLINE, Validators.required]
    });
  }

  ngOnInit(): void {
    const items = this.cartService.getCartItems();
    if (!items || items.length === 0) {
      this.snackBar.open('Votre panier est vide', 'OK', { duration: 3000 });
      this.router.navigate(['/cart']);
    }
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
      // @ts-ignore
      const totalAmount = this.cartService.getTotalAmount() || 0;

      const orderData: Omit<Order, 'orderId' | 'status' | 'createdAt'> = {
        customerInfo: formValue.customerInfo,
        deliveryAddress: formValue.deliveryAddress,
        paymentMethod: formValue.paymentMethod,
        items: cartItems.map(item => ({
          productId: item.id,
          quantity: item.quantity,
          price: item.price
        })),
        totalAmount
      };

      const order = await firstValueFrom(
        this.orderService.createOrder(orderData)
      );

      if (formValue.paymentMethod === PaymentMethod.ONLINE) {
        const paymentSuccess = await firstValueFrom(
          this.orderService.processPayment(order)
        );
        if (!paymentSuccess) {
          throw new Error('Échec du paiement');
        }
      }

      await firstValueFrom(
        this.orderService.sendOrderConfirmationEmail(order)
      );

      this.cartService.clearCart();
      this.snackBar.open('Commande confirmée avec succès !', 'OK', { duration: 5000 });
      // @ts-ignore
      const { orderId } = order;
      this.router.navigate(['/order-confirmation'], {
        queryParams: { orderId }
      });

    } catch (err) {
      console.error('Erreur de commande:', err);
      this.snackBar.open(
        'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.',
        'OK',
        { duration: 5000 }
      );
    } finally {
      this.isSubmitting = false;
    }
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
