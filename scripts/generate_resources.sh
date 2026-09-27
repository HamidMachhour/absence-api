#!/bin/bash

# Define the path to your models directory
MODELS_PATH="app/Models"

# Loop through all the PHP files in the models directory
for FILE in $(ls $MODELS_PATH/*.php); do

    # Extract the model class name from the file name
    CLASS_NAME=$(basename $FILE .php)

    RESOURCE_NAME=$CLASS_NAME"Resource"

      # Check if the resource file already exists
    if [ ! -f "app/Http/Resources/V1/$RESOURCE_NAME.php" ]; then
        # Generate a resource class for the model
        php artisan make:resource V1/$RESOURCE_NAME
    fi

done
