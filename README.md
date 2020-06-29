# Travis CI

[![Build Status](https://travis-ci.org/Easy-Lab/ITCommunity-Front.svg?branch=master)](https://travis-ci.org/Easy-Lab/ITCommunity-Front)

# Installation

### 1/ Cloning the project :

SSH: `git clone git@github.com:Easy-Lab/ITCommunity-Front.git`

HTTPS: `git clone https://github.com/Easy-Lab/ITCommunity-Front.git` 

### 2/ Moving around the project:

`cd ITCommunity-Front`

### 3/ Create the .env file:

`cp .env.dist .env`

### 4/ Complete him:

```
LOCALE= #Language of your translation file
ASSETS_URL= #URL of your assets (css,js,...)
GOOGLE_MAPS_FRONTEND_API_KEY= #Google Maps API Public Key
API_URL= #Url of your API
FRONT_URL= #Url of your Front
ANALYTICS_KEY= #Tracking ID of your Google Analytics
```

### 5/ Install all dependencies:

`composer install` (if errors, add the option `--ignore-platform-reqs`)

`npm install`

### 6/ Compile SCSS files:

`sh bin/watch`

### 7/ Run the project:

`sh bin/run`
