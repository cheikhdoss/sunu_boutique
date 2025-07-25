export interface DeliveryAddress {
  street: string;
  city: string;
  postalCode: string;
  country: string;
  additionalInfo?: string;
}

export interface CustomerInfo {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
}

export enum PaymentMethod {
  ONLINE = 'ONLINE',
  CASH_ON_DELIVERY = 'CASH_ON_DELIVERY'
}

export interface Order {
  orderId: string;
  customerInfo: CustomerInfo;
  deliveryAddress: DeliveryAddress;
  paymentMethod: PaymentMethod;
  items: Array<{
    productId: number;
    quantity: number;
    price: number;
  }>;
  totalAmount: number;
  status: 'PENDING' | 'CONFIRMED' | 'CANCELLED';
  createdAt: Date;
} 