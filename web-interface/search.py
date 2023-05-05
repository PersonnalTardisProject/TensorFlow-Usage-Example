#!/usr/bin/env python3

import pandas as pd
import numpy as np
import json
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense
from tensorflow.keras.models import load_model
from sklearn.preprocessing import LabelEncoder, OneHotEncoder
import sys

if len(sys.argv) != 4:
    print("Usage: ./search.py <Marque> <Modele> <Canon>")
    exit(84)

Marque = sys.argv[1]
Modele = sys.argv[2]
Canon = sys.argv[3]

json_file = open("../data/ref_example.json", "r")
json_data = json.load(json_file)

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

checkInputValue(Marque, Modele, Canon, json_data)

data = pd.DataFrame(columns=['Marque', 'Modele', 'Canon'])
data.loc[len(data)] = [Marque, Modele, Canon]

def ordinalEncoded(data):
    df = pd.DataFrame(columns=['Marque', 'Modele', 'Canon'])
    for i in range(len(data)):
        df.loc[i] = [json_data['Marque'][data['Marque'][i]]['id'], json_data['Modele'][data['Modele'][i]]['id'], json_data['Canon'][data['Canon'][i]]['id']]
    return df

X_encoded = ordinalEncoded(data[['Marque', 'Modele', 'Canon']])

model = load_model('../algorithm/.saved_model/model_example.h5')

y_pred = model.predict(X_encoded, verbose=0)

print(y_pred[0][0])
