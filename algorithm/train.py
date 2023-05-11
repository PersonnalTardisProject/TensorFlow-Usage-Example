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
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense
from tensorflow.keras.models import load_model

# Ouverture du fichier JSON contenant les données de référence
json_file = open("../data/ref_example.json", "r")
json_data = json.load(json_file)

# Lecture des données CSV
csv_data = pd.read_csv('../data/example.csv')

# Extraction des caractéristiques et des prix
X = csv_data[['Marque', 'Modele', 'Canon']]
y = csv_data['Prix']

# Encodage ordinal des caractéristiques pour les transformer en données numériques
def ordinalEncoded(data):
    df = pd.DataFrame(columns=['Marque', 'Modele', 'Canon'])
    for i in range(len(data)):
        # Récupération de l'ID de chaque caractéristique dans le fichier JSON
        df.loc[i] = [json_data['Marque'][data['Marque'][i]]['id'], json_data['Modele'][data['Modele'][i]]['id'], json_data['Canon'][data['Canon'][i]]['id']]
    return df

# Encodage des caractéristiques de l'ensemble des données
X_encoded = ordinalEncoded(X)

# Séparation des données en ensembles d'entraînement et de test
X_train, X_test, y_train, y_test = train_test_split(X_encoded, y, test_size=0.2, random_state=42)

# Création du modèle de réseau de neurones
model = Sequential()
model.add(Dense(units=64, activation='relu', input_dim=3))
model.add(Dense(units=32, activation='relu'))
model.add(Dense(units=16, activation='relu'))
model.add(Dense(units=1, activation='linear'))

# Compilation du modèle avec l'optimiseur Adam, l'erreur quadratique moyenne comme fonction de coût et l'exactitude comme métrique
model.compile(optimizer='Adam', loss='mse', metrics=['acc'])

# Entraînement du modèle sur les données d'entraînement
model.fit(X_train, y_train, epochs=1000, batch_size=32, verbose=0)

# Évaluation du modèle sur les données de test
mse = model.evaluate(X_test, y_test, verbose=0)

# Prédiction du prix pour l'ensemble des données
X_pred = ordinalEncoded(csv_data[['Marque', 'Modele', 'Canon']])
y_pred = model.predict(X_pred)

# Ajout d'une colonne "Prix Moyen" avec les prédictions dans les données CSV
csv_data['Prix Moyen'] = y_pred

# Sauvegarde des données avec les prédictions dans un nouveau fichier CSV
csv_data.to_csv('../data/example_predictions.csv', index=False)

# Sauvegarde du modèle entraîné
model.save('./.saved_model/model_example.h5')
