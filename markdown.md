# Cahier des Charges - Plateforme E-Commerce ComplèteProjet Étudiant IAGE DK 2025

# Informations Générales

<table><tr><td>Élément</td><td>Détail</td></tr><tr><td>Nom du Projet</td><td>Plateforme E-Commerce Complète avec Paiement et Gestion des Commandes</td></tr><tr><td>Type de Projet</td><td>Projet Étudiant Interne</td></tr><tr><td>Équipe</td><td>Groupe de 3 étudiants</td></tr><tr><td>Année</td><td>2025</td></tr><tr><td>Institution</td><td>IAGE DK</td></tr></table>

# 1. Contexte et Objectifs

# 1.1 Contexte

Le projet consiste à développer une application web e- commerce complète destinee a la vente en ligne de produits. Cette plateforme doit couvrir I'ensemble du cycle de vente, depuis la navigation dans le catalogue jusqu'au paiement et au suivi des commandes.

# 1.2 Objectifs Principaux

Permettre aux clients de parcourir le catalogue de produits. Gerer un systeme de panier et de commandes. Proposer deux modes de paiement flexibles. Fournir une interface d'administration complete. Automatiser les notifications par email. Generer des factures PDF

# 1.3 Objectifs Techniques

- Développer une application web responsive- Intégrer un système de paiement sécurisé- Mettre en place une architecture back-office robuste- Implémenter un système de notifications automatiques

# 2. Publics Cibles et Rôles

# 2.1 Client Final

Profil : Utilisateur final consommateur

# Permissions :

Consultation du catalogue Creation et gestion de compte Gestion du panier et passage de commandes Consultation de l'historique des commandes Telechargement des factures Reception des notifications par email

# 2.2 Administrateur

Profil : Gestionnaire de la plateforme

# Permissions :

Gestion complète des produits et catégories- Gestion des commandes et des statuts- Gestion des utilisateurs clients- Suivi des paiements- Accès aux statistiques et tableaux de bord- Configuration des notifications automatiques

# 3. Fonctionnalités Détailées

# 3.1 Front-Office Client

# 3.1.1 Catalogue de Produits

# Fonctionnalités :

Affichage des produits avec :

Nom du produit Description courte Prix affiche Image principale Indicateur de disponibilité (stock)

Filtrage par catégorie

- Recherche par mots-clés- Système de pagination- Affichage responsive (desktop, tablette, mobile)

# Spécifications Techniques:

- Chargement optimisé des images- Système de cache pour améliorer les performances- Navigation intuitive et ergonomique

# 3.1.2 Fiche Produit

# Fonctionnalités:

- Détail complet du produit incluant:    
- Plusieurs images (optionnel)    
- Description longue et détaillée    
- Prix unitaire    
- Disponibilité en stock    
- Bouton "Ajouter au panier"

- Navigation entre les images (si plusieurs)- Informations techniques du produit

# 3.1.3 Gestion du Panier

# Fonctionnalités:

- Ajout de produits au panier- Suppression d'articles- Modification des quantités- Calcul automatique du total- Sauvegarde du panier en session- Affichage du récapitulatif avant commande

# Spécifications Techniques:

- Persistence du panier durant la session- Vérification automatique du stock- Calcul des taxes et frais de livraison

# 3.1.4 Passage de Commande

# Fonctionnalites :

- Fonctionnalites :- Formulaire de commande comprenant :    
- Adresse de livraison complète    
- Coordonnées du client    
- Choix du mode de paiement :    
- Paiement avant livraison (paiement en ligne simulé ou réel)    
- Paiement après livraison (paiement en espèces à la réception)

- Validation finale de la commande- Confirmation immédiate par email

# Spécifications Techniques :

- Validation des champs obligatoires- Sécurisation des données sensibles- Génération automatique de numéro de commande

# 3.1.5 Compte Client

# Fonctionnalites :

# Inscription :

- Inscription :    
- Création de compte avec validation par email    
- Formulaire d'inscription complet

# Connexion :

- Connexion :    
- Authentification sécurité    
- Gestion des sessions

# Gestion du profil :

- Gestion du profil :    
- Modification des informations personnelles    
- Gestion des adresses de livraison

# Historique des commandes :

- Historique des commandes :    
- Liste de toutes les commandes passées    
- Détails de chaque commande    
- Statut en temps réel (en attente, expédiée, livrée, annulée)

- Téléchargement des factures PDF

# 3.1.6 Notifications par Email

- Fonctionnalites automatiques :

- Confirmation de commande- Mise à jour du statut de commande (expédiée, livrée)- Confirmation de paiement- Emails de bienvenue et de rédupération de mot de passe

# 3.2 Back-Office Administrateur

# 3.2.1 Gestion des Produits

# Fonctionnalités :

# CRUD Produits:

- CRUD Produits:- Création de nouveaux produits- Modification des produits existants- Suppression de produits

# Gestion des images :

Upload multiple d'images Gestion des images principales et secondaires Optimisation automatique des images

# Gestion du stock :

Suivi des quantites en stock Alertes de stock faible Historique des mouvements de stock

# 3.2.2 Gestion des Catégories

# Fonctionnalités :

# CRUD Catégories :

- CRUD Catégories:- Création de nouvelles catégories- Modification des catégories existantes- Suppression de catégories

# Association produits-catégories:

- Attribution de produits aux catégories- Gestion des catégories multiples par produit

# Hiérarchie des catégories :

- Système de catégories et sous-catégories

# 3.2.3 Gestion des Commandes

# Fonctionnalites :

# - Visualisation des commandes:

- Liste complète des commandes- Détails de chaque commande- Filtrage par statut, date, client

# - Modification du statut:

- En attente- Expédiee- Livrée- Annulée

# - Suivi des paiements:

- Mode de paiement choisi par le client- Statut de paiement (payé/non payé)- Mise à jour manuelle du statut de paiement

# 3.2.4 Gestion des Utilisateurs

# Fonctionnalites :

# - Liste des clients:

- Visualisation de tous les clients inscrits- Modification des informations clients- Activation/désactivation de comptes

# - Historique client:

- Historique complet des commandes par client- Statistiques par client (montant total, nombre de commandes)

# 3.2.5 Statistiques et Tableaux de Bord

# Fonctionnalites :

# - Indicateurs financiers:

- Chiffre d'affaires total- Chiffre d'affaires par periode- Évolution des ventes

# - Indicateurs de commandes:

- Nombre total de commandes- Commandes par statut

- Taux de conversion

# Indicateurs produits:

- Indicateurs produits:- Produits les plus vendus- Produits en rupture de stock- Analyse des catégories

# Suivi des paiements :

Montant des paiements en attente- Répartition des modes de paiement

# 3.2.6 Envoi Automatique d'Emails

# Fonctionnalites :

Configuration des templates d'emails- Automatisation des envois pour:- Confirmation de commandes- Changements de statut- Confirmations de paiement

Historique des emails envoyes

# 3.3 Gestion des Paiements

# 3.3.1 Modes de Paiement

# Paiement avant livraison :

Intégration d'une solution de paiement (simulation ou réelle)- Validation immédiate du paiement- Mise à jour automatique du statut

# Paiement après livraison :

Sélection du mode "Paiement à la livraison"- Règlement en espèces à la réception- Mise à jour manuelle du statut par l'administrateur

# 3.3.2 Gestion des Statuts

Suivi coherent entre statut de commande et statut de paiement- Workflow automatisée selon le mode de paiement choisi- Notifications automatiques selon les changements de statut

# 3.4 Generation de Factures PDF

# 3.4.1 Contenu des Factures

# Informations incluses:

Détails complets du client Liste des produits achetés avec : Quantites Prix unitaires Prix totaux Calcul du total TTC Date de la commande Mode de paiement choisi. Numero de facture unique

# 3.4.2 Accessibilité

Téléchargement par le client dans son espace personnel Consultation par l'administrateur dans le back- office. Generation automatique après validation de commande

# 4. Contraintes Techniques

# 4.1 Livables Obligatoires

Démonstration: Démo video ou test en direct Code source: Organisation via GitHub avec commits détaillés Documentation: Documentation technique et utilisateur

# 4.2 Contraintes de Développement

Collaboration via GitHub avec historique des commits. Traçabilité des contributions individuelles Respect des bonnes pratiques de développement

# 4.3 Contraintes Pédagogiques

Travail en équipe de 3 étudiants Interdiction stricte de plagiat ou copie. Evaluation individuelle basée sur les contributions GitHub

# 5. Spécifications Techniques Recommandées

# 5.1 Architecture Technique

5.1 Architecture Technique- Frontend: Framework moderne (React, Vue.js, ou Angular)- Backend: API REST (Node.js, Spring Boot, Laravel, etc.)- Base de données: MySQL, PostgreSQL, ou MongoDB- Authentication: JWT ou sessions sécurité

# 5.2 Intégrations Nécessaires

5.2 Intégrations Nécessaires- Service d'email: SMTP ou service tiers (SendGrid, Mailgun)- Génération PDF: Bibliothèque PDF (jsPDF, PDFKit, iText)- Paiement: Stripe, PayPal, ou simulation- Stockage d'images: Local ou cloud (AWS S3, Cloudinary)

# 5.3 Securite

5.3 Sécurité- Validation des données côté client et serveur- Protection contre les injections SQL- Chiffrement des mots de passe- Sécurisation des sessions utilisateur

# 6. Critères d'Evaluation

# 6.1 Fonctionnalites

Completude des fonctionnalites demandees- Qualité de l'expérience utilisateur- Robustesse du système de paiement

# 6.2 Technique

6.2 Technique- Qualité du code et organisation- Respect des bonnes pratiques- Performance et optimisation

# 6.3 Collaboration

- Répartition équitable du travail

- Qualité des commits GitHub- Documentation des contributions individuelles

# 6.4 Presentation

- Qualité de la démonstration- Clarté de la documentation- Professionnalisme de la présentation


 