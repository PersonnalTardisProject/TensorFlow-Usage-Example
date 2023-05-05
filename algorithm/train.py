#!/usr/bin/env python3

import pandas as pd
import numpy as np
import json
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense
from tensorflow.keras.models import load_model

json_file = open("../data/ref_example.json", "r")
json_data = json.load(json_file)

csv_data = pd.read_csv('../data/example.csv')

X = csv_data[['Marque', 'Modele', 'Canon']]
y = csv_data['Prix']

def ordinalEncoded(data):
    df = pd.DataFrame(columns=['Marque', 'Modele', 'Canon'])
    for i in range(len(data)):
        df.loc[i] = [json_data['Marque'][data['Marque'][i]]['id'], json_data['Modele'][data['Modele'][i]]['id'], json_data['Canon'][data['Canon'][i]]['id']]
    return df

X_encoded = ordinalEncoded(X)

X_train, X_test, y_train, y_test = train_test_split(X_encoded, y, test_size=0.2, random_state=42)

model = Sequential()
model.add(Dense(units=64, activation='relu', input_dim=3))
model.add(Dense(units=32, activation='relu'))
model.add(Dense(units=16, activation='relu'))
model.add(Dense(units=1, activation='linear'))

model.compile(optimizer='Adam', loss='mse', metrics=['acc'])

model.fit(X_train, y_train, epochs=1000, batch_size=32, verbose=0)

mse = model.evaluate(X_test, y_test, verbose=0)

X_pred = ordinalEncoded(csv_data[['Marque', 'Modele', 'Canon']])
y_pred = model.predict(X_pred)
csv_data['Prix Moyen'] = y_pred

csv_data.to_csv('../data/example_predictions.csv', index=False)

# Sauvegarder le modèle Keras
model.save('./.saved_model/model_example.h5')