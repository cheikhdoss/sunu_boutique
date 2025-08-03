import { Component, Input, Output, EventEmitter, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { NotificationData } from '../../services/notification.service';

@Component({
  selector: 'app-notification',
  standalone: true,
  imports: [CommonModule, MatIconModule, MatButtonModule],
  templateUrl: './notification.component.html',
  styleUrls: ['./notification.component.css']
})
export class NotificationComponent implements OnInit, OnDestroy {
  @Input() notification!: NotificationData;
  @Output() close = new EventEmitter<string>();
  @Output() actionClick = new EventEmitter<void>();

  isVisible = false;
  progressWidth = 100;
  private progressInterval?: number;

  ngOnInit() {
    // Trigger entrance animation
    setTimeout(() => {
      this.isVisible = true;
    }, 50);

    // Start progress bar animation if duration is set
    if (this.notification.duration && this.notification.duration > 0) {
      this.startProgressAnimation();
    }
  }

  ngOnDestroy() {
    if (this.progressInterval) {
      clearInterval(this.progressInterval);
    }
  }

  onClose() {
    this.isVisible = false;
    setTimeout(() => {
      this.close.emit(this.notification.id);
    }, 300);
  }

  onAction() {
    if (this.notification.action) {
      this.notification.action.callback();
      this.actionClick.emit();
    }
  }

  private startProgressAnimation() {
    const duration = this.notification.duration!;
    const interval = 50; // Update every 50ms
    const decrement = (100 * interval) / duration;

    this.progressInterval = window.setInterval(() => {
      this.progressWidth -= decrement;
      if (this.progressWidth <= 0) {
        this.progressWidth = 0;
        if (this.progressInterval) {
          clearInterval(this.progressInterval);
        }
      }
    }, interval);
  }

  getNotificationClass(): string {
    const baseClass = 'notification-card';
    const typeClass = `notification-${this.notification.type}`;
    const visibleClass = this.isVisible ? 'visible' : '';
    return `${baseClass} ${typeClass} ${visibleClass}`.trim();
  }

  getIconClass(): string {
    return `notification-icon icon-${this.notification.type}`;
  }
}