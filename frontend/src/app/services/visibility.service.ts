import { Injectable } from '@angular/core';
import { BehaviorSubject, fromEvent, Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class VisibilityService {
  private visibilitySubject = new BehaviorSubject<boolean>(!document.hidden);
  public visibility$ = this.visibilitySubject.asObservable();

  constructor() {
    // Écouter les changements de visibilité de la page
    fromEvent(document, 'visibilitychange').pipe(
      map(() => !document.hidden),
      startWith(!document.hidden)
    ).subscribe(isVisible => {
      this.visibilitySubject.next(isVisible);
    });

    // Écouter les événements de focus/blur de la fenêtre
    fromEvent(window, 'focus').subscribe(() => {
      this.visibilitySubject.next(true);
    });

    fromEvent(window, 'blur').subscribe(() => {
      this.visibilitySubject.next(false);
    });
  }

  get isVisible(): boolean {
    return this.visibilitySubject.value;
  }
}