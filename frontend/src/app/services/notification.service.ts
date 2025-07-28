import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export interface Notification {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message: string;
  duration?: number;
}

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  private notificationsSubject = new BehaviorSubject<Notification[]>([]);
  public notifications$ = this.notificationsSubject.asObservable();

  constructor() {}

  private generateId(): string {
    return Math.random().toString(36).substr(2, 9);
  }

  show(notification: Omit<Notification, 'id'>): void {
    const duration = notification.duration || 5000;
    const newNotification: Notification = {
      ...notification,
      id: this.generateId(),
      duration: duration
    };

    const currentNotifications = this.notificationsSubject.value;
    this.notificationsSubject.next([...currentNotifications, newNotification]);

    // Auto-remove after duration
    if (duration > 0) {
      setTimeout(() => {
        this.remove(newNotification.id);
      }, duration);
    }
  }

  remove(id: string): void {
    const currentNotifications = this.notificationsSubject.value;
    const filteredNotifications = currentNotifications.filter(n => n.id !== id);
    this.notificationsSubject.next(filteredNotifications);
  }

  success(title: string, message: string, duration?: number): void {
    this.show({ type: 'success', title, message, duration });
  }

  error(title: string, message: string, duration?: number): void {
    this.show({ type: 'error', title, message, duration });
  }

  warning(title: string, message: string, duration?: number): void {
    this.show({ type: 'warning', title, message, duration });
  }

  info(title: string, message: string, duration?: number): void {
    this.show({ type: 'info', title, message, duration });
  }

  clear(): void {
    this.notificationsSubject.next([]);
  }
}