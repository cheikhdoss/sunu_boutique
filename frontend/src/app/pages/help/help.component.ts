import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';

@Component({
  selector: 'app-help',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule],
  templateUrl: './help.component.html',
  styleUrls: ['./help.component.css']
})
export class HelpComponent {
  
  faqItems = [
    {
      question: 'Comment passer une commande ?',
      answer: 'Pour passer une commande, ajoutez vos articles au panier, puis cliquez sur "Passer commande". Remplissez vos informations de livraison et choisissez votre mode de paiement.',
      expanded: false
    },
    {
      question: 'Quels sont les modes de paiement acceptés ?',
      answer: 'Nous acceptons PayDunya (Orange Money, Free Money, Wave) et le paiement à la livraison pour certaines zones.',
      expanded: false
    },
    {
      question: 'Combien de temps prend la livraison ?',
      answer: 'La livraison prend généralement 2-5 jours ouvrables selon votre localisation. Nous vous contacterons pour confirmer le créneau de livraison.',
      expanded: false
    },
    {
      question: 'Puis-je modifier ou annuler ma commande ?',
      answer: 'Vous pouvez modifier ou annuler votre commande dans les 2 heures suivant la confirmation, depuis votre espace "Mes commandes".',
      expanded: false
    },
    {
      question: 'Comment suivre ma commande ?',
      answer: 'Connectez-vous à votre compte et rendez-vous dans "Mes commandes" pour suivre l\'état de vos commandes en temps réel.',
      expanded: false
    },
    {
      question: 'Que faire si un article est défectueux ?',
      answer: 'Contactez-nous dans les 7 jours suivant la réception. Nous organiserons un échange ou un remboursement selon le cas.',
      expanded: false
    }
  ];

  helpCategories = [
    {
      title: 'Commandes',
      icon: 'shopping_bag',
      description: 'Tout sur vos commandes',
      items: ['Passer une commande', 'Modifier une commande', 'Suivre une commande', 'Annuler une commande']
    },
    {
      title: 'Paiement',
      icon: 'payment',
      description: 'Modes de paiement et facturation',
      items: ['PayDunya', 'Paiement à la livraison', 'Factures', 'Remboursements']
    },
    {
      title: 'Livraison',
      icon: 'local_shipping',
      description: 'Informations sur la livraison',
      items: ['Zones de livraison', 'Délais', 'Frais de port', 'Suivi colis']
    },
    {
      title: 'Compte',
      icon: 'account_circle',
      description: 'Gestion de votre compte',
      items: ['Créer un compte', 'Mot de passe oublié', 'Modifier profil', 'Adresses']
    }
  ];

  toggleFaq(index: number) {
    this.faqItems[index].expanded = !this.faqItems[index].expanded;
  }

  scrollToSection(sectionId: string) {
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  }
}