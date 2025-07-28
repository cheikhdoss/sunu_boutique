import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface ProfileUpdateData {
  name: string;
  email: string;
  phone?: string;
}

export interface PasswordUpdateData {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}

@Injectable({
  providedIn: 'root'
})
export class ProfileService {
  private apiUrl = `${environment.apiUrl}/profile`;

  constructor(private http: HttpClient) {}

  getProfile(): Observable<any> {
    return this.http.get(`${this.apiUrl}`);
  }

  updateProfile(data: ProfileUpdateData): Observable<any> {
    return this.http.put(`${this.apiUrl}`, data);
  }

  updatePassword(data: PasswordUpdateData): Observable<any> {
    return this.http.put(`${this.apiUrl}/password`, data);
  }

  uploadAvatar(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('avatar', file);
    return this.http.post(`${this.apiUrl}/avatar`, formData);
  }

  deleteAvatar(): Observable<any> {
    return this.http.delete(`${this.apiUrl}/avatar`);
  }
}