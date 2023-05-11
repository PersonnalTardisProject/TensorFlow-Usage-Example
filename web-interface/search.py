#!/usr/bin/env python3

# Désactiver les messages de TensorFlow
import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
# Log level 0 = all messages are logged (default behavior)
# Log level 1 = INFO messages are not printed
# Log level 2 = INFO and WARNING messages are not printed
# Log level 3 = INFO, WARNING, and ERROR messages are not printed

# Importer les bibliothèques nécessaires
import pandas as pd
import numpy as np
import json
from tensorflow.keras.models import load_model
import sys

# Vérifier si le nombre d'arguments est correct (3)
if len(sys.argv) != 4:
    print("Usage: ./search.py <Marque> <Modele> <Canon>")
    exit(84)

# Récupérer les arguments
Marque = sys.argv[1]
Modele = sys.argv[2]
Canon = sys.argv[3]

# Ouverture du fichier JSON contenant les données de référence
json_file = open("../data/ref_example.json", "r")
json_data = json.load(json_file)

# Fonction pour vérifier si les valeurs d'entrée sont correctes
def checkInputValue(Marque, Modele, Canon, json_data):
    if Marque not in json_data['Marque']:
        print("Vérifiez les noms des marques, modèles et canons dans le fichier ref_armes.json")
        exit(84)
    if Modele not in json_data['Modele']:
        print("Vérifiez les noms des marques, modèles et canons dans le fichier ref_armes.json")
        exit(84)
    if Canon not in json_data['Canon']:
        print("Vérifiez les noms des marques, modèles et canons dans le fichier ref_armes.json")
        exit(84)

# Appeler la fonction pour vérifier les valeurs d'entrée
checkInputValue(Marque, Modele, Canon, json_data)

# Créer un DataFrame à partir des entrées utilisateur
data = pd.DataFrame(columns=['Marque', 'Modele', 'Canon'])
data.loc[len(data)] = [Marque, Modele, Canon]

# Encodage ordinal des caractéristiques pour les transformer en données numériques
def ordinalEncoded(data):
    df = pd.DataFrame(columns=['Marque', 'Modele', 'Canon'])
    for i in range(len(data)):
        # Récupération de l'ID de chaque caractéristique dans le fichier JSON
        df.loc[i] = [json_data['Marque'][data['Marque'][i]]['id'], json_data['Modele'][data['Modele'][i]]['id'], json_data['Canon'][data['Canon'][i]]['id']]
    return df

# Encodage des caractéristiques de l'ensemble des données
X_encoded = ordinalEncoded(data[['Marque', 'Modele', 'Canon']])

# Charger le modèle de l'algorithme pré-entraîné
model = load_model('../algorithm/.saved_model/model_example.h5')

# Prédiction du prix pour l'ensemble des données
y_pred = model.predict(X_encoded, verbose=0)

# Afficher le résultat de la prédiction
print(y_pred[0][0])
