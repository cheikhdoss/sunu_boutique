#!/bin/bash

# 🚀 Script de démarrage pour développement avec PayDunya + Ngrok
# Ce script configure automatiquement l'environnement de développement

echo "🌟 Démarrage de l'environnement de développement SunuBoutique"
echo "=================================================="

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Vérifier si ngrok est installé
if ! command -v ngrok &> /dev/null; then
    echo -e "${RED}❌ Ngrok n'est pas installé. Veuillez l'installer d'abord.${NC}"
    echo "Installation: https://ngrok.com/download"
    exit 1
fi

# Vérifier si le fichier .env existe
if [ ! -f "backend/.env" ]; then
    echo -e "${RED}❌ Fichier backend/.env non trouvé !${NC}"
    echo "Veuillez créer le fichier .env dans le dossier backend/"
    exit 1
fi

# Démarrer les services en arrière-plan
echo -e "${BLUE}🔧 Démarrage des services...${NC}"

# Démarrer Laravel
echo "📱 Démarrage du backend Laravel..."
cd backend
php artisan serve --port=8000 &
LARAVEL_PID=$!
cd ..

# Attendre que Laravel démarre
sleep 3

# Démarrer Angular
echo "🎨 Démarrage du frontend Angular..."
cd frontend
ng serve --port=4200 &
ANGULAR_PID=$!
cd ..

# Attendre qu'Angular démarre
sleep 5

# Démarrer ngrok
echo "🌍 Démarrage de ngrok..."
ngrok http 8000 &
NGROK_PID=$!

# Attendre que ngrok démarre
sleep 5

# Obtenir l'URL ngrok
NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o 'https://[^"]*\.ngrok\.io')

if [ -z "$NGROK_URL" ]; then
    echo -e "${RED}❌ Impossible d'obtenir l'URL ngrok${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Ngrok URL: $NGROK_URL${NC}"

# Mettre à jour le fichier .env avec l'URL ngrok
echo "🔧 Mise à jour de la configuration..."
sed -i "s|NGROK_URL=.*|NGROK_URL=$NGROK_URL|g" backend/.env

# Redémarrer Laravel pour prendre en compte la nouvelle configuration
echo "🔄 Redémarrage de Laravel..."
kill $LARAVEL_PID
cd backend
php artisan config:clear
php artisan serve --port=8000 &
LARAVEL_PID=$!
cd ..

echo ""
echo -e "${GREEN}🎉 Environnement de développement prêt !${NC}"
echo "=================================================="
echo -e "${BLUE}🌐 Backend (Laravel):${NC} http://localhost:8000"
echo -e "${BLUE}🎨 Frontend (Angular):${NC} http://localhost:4200"
echo -e "${BLUE}🌍 URL publique (Ngrok):${NC} $NGROK_URL"
echo -e "${BLUE}📊 Ngrok Dashboard:${NC} http://localhost:4040"
echo ""
echo -e "${YELLOW}📋 URLs importantes pour PayDunya:${NC}"
echo -e "   IPN URL: $NGROK_URL/api/payments/paydunya/ipn"
echo -e "   Success URL: $NGROK_URL/payment/success"
echo -e "   Error URL: $NGROK_URL/payment/error"
echo ""
echo -e "${YELLOW}🔧 Configuration PayDunya:${NC}"
echo "   1. Connectez-vous à votre compte PayDunya"
echo "   2. Configurez les URLs de callback avec l'URL ngrok ci-dessus"
echo "   3. Copiez vos clés API dans backend/.env"
echo ""
echo -e "${RED}⚠️  Important:${NC} L'URL ngrok change à chaque redémarrage."
echo "   Pensez à mettre à jour vos webhooks PayDunya si nécessaire."
echo ""
echo "Appuyez sur Ctrl+C pour arrêter tous les services."

# Fonction de nettoyage
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Arrêt des services...${NC}"
    kill $LARAVEL_PID 2>/dev/null
    kill $ANGULAR_PID 2>/dev/null
    kill $NGROK_PID 2>/dev/null
    echo -e "${GREEN}✅ Tous les services ont été arrêtés.${NC}"
    exit 0
}

# Capturer Ctrl+C
trap cleanup SIGINT

# Attendre indéfiniment
while true; do
    sleep 1
done