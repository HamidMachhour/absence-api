#!/bin/bash
php artisan db:seed --class=PlanSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=SubscriptionSeeder

php artisan db:seed --class=MatiereSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=JourSeeder

php artisan db:seed --class=AddressSeeder
php artisan db:seed --class=ClasseSeeder
php artisan db:seed --class=SeanceSeeder

php artisan db:seed --class=StudentCandidateSeeder
php artisan db:seed --class=StudentSeeder
php artisan db:seed --class=ClasseImageSeeder
php artisan db:seed --class=UserImageSeeder

php artisan db:seed --class=AppelSeeder
php artisan db:seed --class=AppelStudentSeeder
php artisan db:seed --class=AbsenceSeeder
php artisan db:seed --class=AbsenceStudentSeeder







