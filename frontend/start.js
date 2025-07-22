#!/usr/bin/env node

const { spawn } = require('child_process');

console.log('🚀 Démarrage de Sunu Boutique Frontend...\n');

const ngServe = spawn('ng', ['serve', '--host', '0.0.0.0', '--port', '4200', '--configuration', 'development'], {
  stdio: 'inherit',
  shell: true
});

ngServe.on('error', (error) => {
  console.error('❌ Erreur lors du démarrage:', error);
});

ngServe.on('close', (code) => {
  console.log(`\n🛑 Serveur arrêté avec le code ${code}`);
});

process.on('SIGINT', () => {
  console.log('\n🛑 Arrêt du serveur...');
  ngServe.kill('SIGINT');
});