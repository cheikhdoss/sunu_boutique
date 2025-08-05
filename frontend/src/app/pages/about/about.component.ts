import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MaterialModule } from '../../material.module';

@Component({
  selector: 'app-about',
  standalone: true,
  imports: [CommonModule, RouterModule, MaterialModule],
  templateUrl: './about.component.html',
  styleUrls: ['./about.component.css']
})
export class AboutComponent {
  
  teamMembers = [
    {
      name: 'Cheikh Sidy Makhtar Mbodji',
      role: 'Co-fondateur & Développeur Full-Stack',
      image: '/assets/images/team/cheikh.jpg',
      description: 'Expert en développement web et architecte de la plateforme SunuBoutique'
    },
    {
      name: 'Saura Mansour',
      role: 'Co-fondateur & Développeur Full-Stack',
      image: '/assets/images/team/saura.jpg',
      description: 'Spécialiste en développement frontend et expérience utilisateur'
    }
  ];

  values = [
    {
      icon: 'verified',
      title: 'Qualité',
      description: 'Nous sélectionnons rigoureusement chaque produit pour garantir la meilleure qualité à nos clients.'
    },
    {
      icon: 'speed',
      title: 'Rapidité',
      description: 'Livraison rapide et efficace partout au Sénégal grâce à notre réseau logistique optimisé.'
    },
    {
      icon: 'favorite',
      title: 'Satisfaction',
      description: 'Votre satisfaction est notre priorité. Nous nous engageons à dépasser vos attentes.'
    },
    {
      icon: 'eco',
      title: 'Durabilité',
      description: 'Nous privilégions les produits locaux et respectueux de l\'environnement.'
    }
  ];

  milestones = [
    {
      year: '2020',
      title: 'Création de SunuBoutique',
      description: 'Lancement de la plateforme avec 50 produits locaux'
    },
    {
      year: '2021',
      title: 'Expansion régionale',
      description: 'Extension des livraisons dans toutes les régions du Sénégal'
    },
    {
      year: '2022',
      title: '1000+ produits',
      description: 'Catalogue élargi avec plus de 1000 références'
    },
    {
      year: '2023',
      title: '10 000+ clients',
      description: 'Communauté de plus de 10 000 clients satisfaits'
    },
    {
      year: '2024',
      title: 'Innovation continue',
      description: 'Nouvelles fonctionnalités et partenariats stratégiques'
    }
  ];

  stats = [
    { value: '10,000+', label: 'Clients satisfaits' },
    { value: '1,500+', label: 'Produits disponibles' },
    { value: '50+', label: 'Partenaires locaux' },
    { value: '99%', label: 'Taux de satisfaction' }
  ];
}