#!/bin/zsh

echo "Start training..."

cd ./algorithm

chmod +x train.py
./train.py

echo "Training finished."

cd ..

echo "Start web interface..."

cd ./web-interface

chmod +x search.py
php -S localhost:8080