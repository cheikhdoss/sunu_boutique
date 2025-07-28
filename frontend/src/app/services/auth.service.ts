import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BehaviorSubject, Observable, throwError } from 'rxjs';
import { catchError, map, tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
  date_of_birth?: string;
  gender?: 'male' | 'female' | 'other';
  avatar?: string;
  is_admin?: boolean;
  email_verified_at?: string;
  created_at: string;
  updated_at?: string;
}

export interface AuthResponse {
  success: boolean;
  message: string;
  user: User;
  token: string;
  token_type: string;
}

export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  errors?: any;
}

export interface LoginRequest {
  email: string;
  password: string;
  remember?: boolean;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone?: string;
  date_of_birth?: string;
  gender?: 'male' | 'female' | 'other';
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject: BehaviorSubject<User | null>;
  public currentUser$: Observable<User | null>;
  private apiUrl = `${environment.apiUrl}/auth`;

  constructor(private http: HttpClient) {
    const savedUser = localStorage.getItem('currentUser');
    this.currentUserSubject = new BehaviorSubject<User | null>(
      savedUser ? JSON.parse(savedUser) : null
    );
    this.currentUser$ = this.currentUserSubject.asObservable();
  }

  public get currentUser(): User | null {
    return this.currentUserSubject.value;
  }

  /**
   * Connexion utilisateur
   */
  login(credentials: LoginRequest): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials)
      .pipe(
        tap(response => {
          if (response.success) {
            localStorage.setItem('currentUser', JSON.stringify(response.user));
            localStorage.setItem('token', response.token);
            this.currentUserSubject.next(response.user);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Inscription utilisateur
   */
  register(userData: RegisterRequest): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/register`, userData)
      .pipe(
        tap(response => {
          if (response.success) {
            localStorage.setItem('currentUser', JSON.stringify(response.user));
            localStorage.setItem('token', response.token);
            this.currentUserSubject.next(response.user);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Déconnexion
   */
  logout(): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/logout`, {})
      .pipe(
        tap(() => {
          this.clearAuthData();
        }),
        catchError((error) => {
          // Même en cas d'erreur, on nettoie les données locales
          this.clearAuthData();
          return throwError(() => error);
        })
      );
  }

  /**
   * Déconnexion de tous les appareils
   */
  logoutAll(): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/logout-all`, {})
      .pipe(
        tap(() => {
          this.clearAuthData();
        }),
        catchError((error) => {
          this.clearAuthData();
          return throwError(() => error);
        })
      );
  }

  /**
   * Obtenir les informations de l'utilisateur connecté
   */
  me(): Observable<{ success: boolean; user: User }> {
    return this.http.get<{ success: boolean; user: User }>(`${this.apiUrl}/me`)
      .pipe(
        tap(response => {
          if (response.success) {
            localStorage.setItem('currentUser', JSON.stringify(response.user));
            this.currentUserSubject.next(response.user);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Rafraîchir le token
   */
  refreshToken(): Observable<{ success: boolean; token: string; token_type: string }> {
    return this.http.post<{ success: boolean; token: string; token_type: string }>(`${this.apiUrl}/refresh`, {})
      .pipe(
        tap(response => {
          if (response.success) {
            localStorage.setItem('token', response.token);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Vérifier la validité du token
   */
  checkToken(): Observable<{ success: boolean; user: User }> {
    return this.http.get<{ success: boolean; user: User }>(`${this.apiUrl}/check-token`)
      .pipe(
        catchError(this.handleError)
      );
  }

  /**
   * Demande de réinitialisation de mot de passe
   */
  forgotPassword(email: string): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/forgot-password`, { email })
      .pipe(
        catchError(this.handleError)
      );
  }

  /**
   * Réinitialisation du mot de passe
   */
  resetPassword(data: {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
  }): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/reset-password`, data)
      .pipe(
        catchError(this.handleError)
      );
  }

  /**
   * Changer le mot de passe
   */
  changePassword(data: {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
  }): Observable<ApiResponse> {
    return this.http.put<ApiResponse>(`${this.apiUrl}/change-password`, data)
      .pipe(
        catchError(this.handleError)
      );
  }

  /**
   * Vérifier si l'utilisateur est authentifié
   */
  isAuthenticated(): boolean {
    return !!this.currentUser && !!this.getToken();
  }

  /**
   * Vérifier si l'utilisateur est admin
   */
  isAdmin(): boolean {
    return this.currentUser?.is_admin === true;
  }

  /**
   * Obtenir le token d'authentification
   */
  getToken(): string | null {
    return localStorage.getItem('token');
  }

  /**
   * Nettoyer les données d'authentification
   */
  private clearAuthData(): void {
    localStorage.removeItem('currentUser');
    localStorage.removeItem('token');
    localStorage.removeItem('remember_user');
    this.currentUserSubject.next(null);
  }

  /**
   * Gestion des erreurs HTTP
   */
  private handleError(error: HttpErrorResponse) {
    let errorMessage = 'Une erreur est survenue';

    if (error.error instanceof ErrorEvent) {
      // Erreur côté client
      errorMessage = error.error.message;
    } else {
      // Erreur côté serveur
      if (error.error?.message) {
        errorMessage = error.error.message;
      } else if (error.error?.errors) {
        // Erreurs de validation Laravel
        const validationErrors = error.error.errors;
        const firstError = Object.values(validationErrors)[0];
        if (Array.isArray(firstError) && firstError.length > 0) {
          errorMessage = firstError[0] as string;
        }
      } else {
        switch (error.status) {
          case 401:
            errorMessage = 'Non autorisé. Veuillez vous connecter.';
            break;
          case 403:
            errorMessage = 'Accès interdit.';
            break;
          case 404:
            errorMessage = 'Ressource non trouvée.';
            break;
          case 422:
            errorMessage = 'Données invalides.';
            break;
          case 500:
            errorMessage = 'Erreur interne du serveur.';
            break;
          default:
            errorMessage = `Erreur ${error.status}: ${error.message}`;
        }
      }
    }

    return throwError(() => ({
      ...error,
      message: errorMessage
    }));
  }

  /**
   * Mettre à jour les informations de l'utilisateur dans le service
   */
  updateCurrentUser(user: User): void {
    localStorage.setItem('currentUser', JSON.stringify(user));
    this.currentUserSubject.next(user);
  }
}