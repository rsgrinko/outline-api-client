# Outline API Client

Simple API client for [OutlineVPN](https://getoutline.org/ru/).

## Installation

Install the latest version with

```bash
$ composer require rsgrinko/outline-api-client
```

## Requirements

PHP >= 7.4

## How to use

### Usage API Client

```php

require 'vendor/autoload.php';

use rsgrinko\Outline\Outline;

try {
    $outlineObject = new Outline('https://127.0.0.1:1234/EUwl3A2e-Af6cNeQs');
    $clientObject  = $outlineObject->getClientObject();
    // Get an array of all server keys
    $keysList = $clientObject->getKeys();

    // Create new key
    $key = $clientObject->create();

    // Rename exist key.
    $clientObject->setName($key['id'], 'My new key');

    // Set transfer data limit for key (in bytes)
    $clientObject->setLimit($key['id'], 5 * 1024 * 1024);

    // Remove key limit
    $clientObject->deleteLimit($key['id']);

    // Delete key
    $clientObject->delete($key['id']);

    // Get an array of used traffic for all keys
    $transferData = $clientObject->metricsTransfer();
} catch (Throwable $t) {
    // Handle exception...
}
```

### Usage OutlineVPN key wrapper

Interaction with an existing key

```php
<?php
require 'vendor/autoload.php';

use rsgrinko\Outline\Outline;

try {

    $outlineObject = new Outline('https://127.0.0.1:1234/EUwl3A2e-Af6cNeQs');
    $keyObject     = $outlineObject->getKeyObject();
    
    // Initializing an object and getting key data
    $key = $keyObject->load(1);
    
    // Get key id
    $key->getId();
    
    // Get key name
    $key->getName();
    
    // Get key transfer traffic
    $key->getTransfer();
    
    // Get access link 
    $key->getAccessUrl();

    // Rename exist key.
    $key->rename('New key name');

    // Set transfer data limit for key (in bytes)
    $key->limit(5 * 1024 * 1024);

    // Remove key traffic limit
    $key->deleteLimit();
    
    // Delete key
    $key->delete();
    
} catch (Throwable $e) {
    // Handle exception...
}

```

Creating a new key on the server

```php
<?php
require 'vendor/autoload.php';

use rsgrinko\Outline\Outline;

try {
    $outlineObject = new Outline('https://127.0.0.1:1234/EUwl3A2e-Af6cNeQs');
    $keyObject     = $outlineObject->getKeyObject();
    
    
    // Create new key
    $key = $keyObject->create('Key name', 5 * 1024 * 1024);

} catch (Throwable $e) {
    // Handle exception...
}
```