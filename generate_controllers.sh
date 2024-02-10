#!/bin/bash

# Define the path to your models directory
MODELS_PATH="app/Models"

# Loop through all the PHP files in the models directory
for FILE in $(ls $MODELS_PATH/*.php); do

    # Extract the model class name from the file name
    CLASS_NAME=$(basename $FILE .php)

    CONTROLLER_NAME=$CLASS_NAME"Controller"

      # Check if the controller file already exists
    if [ ! -f "app/Http/Controllers/Api/V1/$CONTROLLER_NAME.php" ]; then
        # Generate a resource controller for the model
        php artisan make:controller Api/V1/$CONTROLLER_NAME --resource --model=$CLASS_NAME
    fi

    if [ ! -f "app/Http/Controllers/Web/$CONTROLLER_NAME.php" ]; then
        # Generate a resource controller for the model
        php artisan make:controller Web/$CONTROLLER_NAME --model=$CLASS_NAME
    fi
done
