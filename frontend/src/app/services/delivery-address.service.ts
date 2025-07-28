import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface DeliveryAddress {
  id?: number;
  label: string;
  first_name: string;
  last_name: string;
  address: string;
  city: string;
  postal_code: string;
  country: string;
  phone: string;
  is_default: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class DeliveryAddressService {
  private apiUrl = `${environment.apiUrl}/delivery-addresses`;

  constructor(private http: HttpClient) {}

  getAddresses(): Observable<{ addresses: DeliveryAddress[] }> {
    return this.http.get<{ addresses: DeliveryAddress[] }>(this.apiUrl);
  }

  createAddress(address: Omit<DeliveryAddress, 'id'>): Observable<any> {
    return this.http.post(this.apiUrl, address);
  }

  updateAddress(id: number, address: Omit<DeliveryAddress, 'id'>): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, address);
  }

  deleteAddress(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  setDefaultAddress(id: number): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}/set-default`, {});
  }
}