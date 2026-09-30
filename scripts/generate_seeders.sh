#!/bin/bash

# Define the path to your models directory
MODELS_PATH="app/Models"

# Loop through all the PHP files in the models directory
for FILE in $(ls $MODELS_PATH/*.php); do

    # Extract the model class name from the file name
    CLASS_NAME=$(basename $FILE .php)
    SEEDER_NAME=$CLASS_NAME"Seeder"
 # Check if the seeder file already exists
    if [ ! -f "database/seeders/$SEEDER_NAME.php" ]; then
        # Generate a seeder class for the model
        php artisan make:seeder $SEEDER_NAME
    fi

done
