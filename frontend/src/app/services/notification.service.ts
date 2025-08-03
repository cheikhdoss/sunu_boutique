import { Injectable, ComponentRef, ViewContainerRef, ApplicationRef, createComponent, EnvironmentInjector } from '@angular/core';
import { Subject, Observable } from 'rxjs';

export interface NotificationData {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info' | 'cart';
  title: string;
  message: string;
  duration?: number;
  icon?: string;
  action?: {
    label: string;
    callback: () => void;
  };
}

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  private notifications: NotificationData[] = [];
  private notificationSubject = new Subject<NotificationData[]>();
  private container: ViewContainerRef | null = null;

  constructor(
    private appRef: ApplicationRef,
    private injector: EnvironmentInjector
  ) {}

  setContainer(container: ViewContainerRef) {
    this.container = container;
  }

  getNotifications(): Observable<NotificationData[]> {
    return this.notificationSubject.asObservable();
  }

  show(notification: Omit<NotificationData, 'id'>): string {
    const id = this.generateId();
    const fullNotification: NotificationData = {
      id,
      duration: 5000,
      ...notification
    };

    this.notifications.push(fullNotification);
    this.notificationSubject.next([...this.notifications]);

    // Auto-remove after duration
    if (fullNotification.duration && fullNotification.duration > 0) {
      setTimeout(() => {
        this.remove(id);
      }, fullNotification.duration);
    }

    return id;
  }

  success(title: string, message: string, duration?: number): string {
    return this.show({
      type: 'success',
      title,
      message,
      duration,
      icon: 'check_circle'
    });
  }

  error(title: string, message: string, duration?: number): string {
    return this.show({
      type: 'error',
      title,
      message,
      duration: duration || 7000,
      icon: 'error'
    });
  }

  warning(title: string, message: string, duration?: number): string {
    return this.show({
      type: 'warning',
      title,
      message,
      duration,
      icon: 'warning'
    });
  }

  info(title: string, message: string, duration?: number): string {
    return this.show({
      type: 'info',
      title,
      message,
      duration,
      icon: 'info'
    });
  }

  cart(title: string, message: string, action?: { label: string; callback: () => void }): string {
    return this.show({
      type: 'cart',
      title,
      message,
      duration: 6000,
      icon: 'shopping_cart',
      action
    });
  }

  remove(id: string): void {
    this.notifications = this.notifications.filter(n => n.id !== id);
    this.notificationSubject.next([...this.notifications]);
  }

  clear(): void {
    this.notifications = [];
    this.notificationSubject.next([]);
  }

  private generateId(): string {
    return Math.random().toString(36).substr(2, 9);
  }
}