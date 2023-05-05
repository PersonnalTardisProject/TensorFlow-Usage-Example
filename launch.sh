#!/bin/bash
# Ce script permet de lancer une formation d'un algorithme et de lancer une interface web.

# Afficher le message de début de la formation
echo "Start training..."

# Se déplacer dans le répertoire de l'algorithme
cd ./algorithm

# Donner les permissions d'exécution pour le fichier train.py
chmod +x train.py

# Lancer le fichier train.py
./train.py

# Afficher le message de fin de la formation
echo "Training finished."

# Revenir dans le répertoire principal
cd ..

# Afficher le message de début de l'interface web
echo "Start web interface..."

# Se déplacer dans le répertoire de l'interface web
cd ./web-interface

# Donner les permissions d'exécution pour le fichier search.py
chmod +x search.py

# Lancer un serveur PHP sur le port 8080
php -S localhost:8080