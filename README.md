# App info : 
  - SYMFONY 4.4.22 | PHP 3.7
# App setup
  - composer install
  - php bin/console assets:install public
# DB setup 
  - create DB 
  - php bin/console d:d:c
  - php bin/console d:s:u --force
# Front setup 
  - npm install (install packages --1st time only)
  - npm run build (run webpack to generate the assets)
  - You can also make webpack listen for changes and compile only what’s needed as you work on your files:
    npm run watch

# TABLE OF CONTENTS:
  ## Front
    - to access front in browser
      - domain.com\
    - setup all front slug  
      - src\Controller\FrontController.php
    - for twig pages 
      - templates\front\components  
    - for front css & js (any changes require nmp run build | watch)
      - .\assets 
  ## Backoffice
    - to access backoffice in browser
      - domain.com\admin
    - change content order of sidebar 
      - config\services.yaml
    - custom functions
      - src\Controller\AdminController.php
    - for backoffice css & js
      - .\public\admin

---
# BACK-OFFICE (extra infos)
- in case the editors in backoffice are not working run:
  - php bin/console ckeditor:install
  - php bin/console assets:install public

- new Entity
  - php bin/console make:entity
  - php bin/console make:migration
  - php bin/console doctrine:migrations:migrate
  
- new Admin
  - php bin/console make:sonata:admin
  
- clear cache & update the database structeur
  - php bin/console cache:clear
  - php bin/console doctrine:schema:update --force
