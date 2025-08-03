import { Injectable, ComponentRef, ViewContainerRef, ApplicationRef, ComponentFactoryResolver, Injector } from '@angular/core';
import { Subject } from 'rxjs';

export interface CustomNotification {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message: string;
  duration?: number;
  actions?: NotificationAction[];
  showProgress?: boolean;
  showParticles?: boolean;
}

export interface NotificationAction {
  label: string;
  action: () => void;
  primary?: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class CustomNotificationService {
  private notifications: CustomNotification[] = [];
  private notificationSubject = new Subject<CustomNotification[]>();
  public notifications$ = this.notificationSubject.asObservable();

  constructor() {
    this.createNotificationContainer();
  }

  private createNotificationContainer(): void {
    // Créer le conteneur des notifications s'il n'existe pas
    if (!document.getElementById('custom-notification-container')) {
      const container = document.createElement('div');
      container.id = 'custom-notification-container';
      container.className = 'custom-notification-container';
      document.body.appendChild(container);
    }
  }

  show(notification: Omit<CustomNotification, 'id'>): string {
    const id = this.generateId();
    const fullNotification: CustomNotification = {
      id,
      duration: 4000,
      showProgress: true,
      showParticles: true,
      ...notification
    };

    this.notifications.push(fullNotification);
    this.notificationSubject.next([...this.notifications]);
    
    this.renderNotification(fullNotification);
    
    // Auto-remove après la durée spécifiée
    if (fullNotification.duration && fullNotification.duration > 0) {
      setTimeout(() => {
        this.remove(id);
      }, fullNotification.duration);
    }

    return id;
  }

  remove(id: string): void {
    const element = document.getElementById(`notification-${id}`);
    if (element) {
      element.classList.add('hide');
      setTimeout(() => {
        element.remove();
      }, 400);
    }

    this.notifications = this.notifications.filter(n => n.id !== id);
    this.notificationSubject.next([...this.notifications]);
  }

  clear(): void {
    this.notifications.forEach(notification => {
      this.remove(notification.id);
    });
  }

  // Méthodes de convenance
  success(title: string, message: string, actions?: NotificationAction[]): string {
    return this.show({
      type: 'success',
      title,
      message,
      actions
    });
  }

  error(title: string, message: string, actions?: NotificationAction[]): string {
    return this.show({
      type: 'error',
      title,
      message,
      actions
    });
  }

  warning(title: string, message: string, actions?: NotificationAction[]): string {
    return this.show({
      type: 'warning',
      title,
      message,
      actions
    });
  }

  info(title: string, message: string, actions?: NotificationAction[]): string {
    return this.show({
      type: 'info',
      title,
      message,
      actions
    });
  }

  // Notification spéciale pour l'ajout au panier
  addToCart(productName: string, onViewCart?: () => void): string {
    return this.show({
      type: 'success',
      title: '🛒 Produit ajouté !',
      message: `${productName} a été ajouté à votre panier avec succès.`,
      actions: onViewCart ? [
        {
          label: 'Continuer',
          action: () => {},
          primary: false
        },
        {
          label: 'Voir le panier',
          action: onViewCart,
          primary: true
        }
      ] : undefined,
      duration: 5000
    });
  }

  private renderNotification(notification: CustomNotification): void {
    const container = document.getElementById('custom-notification-container');
    if (!container) return;

    const element = document.createElement('div');
    element.id = `notification-${notification.id}`;
    element.className = `custom-notification ${notification.type}`;
    
    // Ajouter classe spéciale pour les notifications de panier
    if (notification.title.includes('🛒')) {
      element.classList.add('cart-notification');
    }

    element.innerHTML = this.getNotificationHTML(notification);

    // Ajouter les event listeners
    this.addEventListeners(element, notification);

    container.appendChild(element);

    // Déclencher l'animation d'entrée
    setTimeout(() => {
      element.classList.add('show');
    }, 10);

    // Ajouter les particules si activées
    if (notification.showParticles) {
      this.addParticles(element);
    }
  }

  private getNotificationHTML(notification: CustomNotification): string {
    const icon = this.getIcon(notification.type);
    const actionsHTML = notification.actions ? 
      notification.actions.map(action => 
        `<button class="notification-action ${action.primary ? 'primary' : ''}" data-action="${action.label}">
          ${action.label}
        </button>`
      ).join('') : '';

    return `
      <div class="notification-header">
        <div class="notification-icon">${icon}</div>
        <h4 class="notification-title">${notification.title}</h4>
        <button class="notification-close" data-action="close">×</button>
      </div>
      <div class="notification-body">
        <p class="notification-message">${notification.message}</p>
      </div>
      ${notification.actions ? `<div class="notification-actions">${actionsHTML}</div>` : ''}
      ${notification.showProgress ? '<div class="notification-progress"></div>' : ''}
      <div class="notification-shine"></div>
      <div class="notification-particles"></div>
    `;
  }

  private getIcon(type: string): string {
    const icons = {
      success: '✓',
      error: '✕',
      warning: '⚠',
      info: 'ℹ'
    };
    return icons[type as keyof typeof icons] || 'ℹ';
  }

  private addEventListeners(element: HTMLElement, notification: CustomNotification): void {
    // Bouton de fermeture
    const closeBtn = element.querySelector('[data-action="close"]');
    closeBtn?.addEventListener('click', () => {
      this.remove(notification.id);
    });

    // Boutons d'action
    if (notification.actions) {
      notification.actions.forEach(action => {
        const btn = element.querySelector(`[data-action="${action.label}"]`);
        btn?.addEventListener('click', () => {
          action.action();
          this.remove(notification.id);
        });
      });
    }
  }

  private addParticles(element: HTMLElement): void {
    const particlesContainer = element.querySelector('.notification-particles');
    if (!particlesContainer) return;

    // Créer 8 particules
    for (let i = 0; i < 8; i++) {
      const particle = document.createElement('div');
      particle.className = 'particle';
      particle.style.left = Math.random() * 100 + '%';
      particle.style.animationDelay = Math.random() * 2 + 's';
      particle.style.animationDuration = (2 + Math.random() * 2) + 's';
      particlesContainer.appendChild(particle);
    }
  }

  private generateId(): string {
    return Math.random().toString(36).substr(2, 9);
  }
}