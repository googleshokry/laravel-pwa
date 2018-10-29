# Laravel (PWA) Progressive Web Aplication 

[![Laravel 5.x](https://img.shields.io/badge/Laravel-5.x-orange.svg)](http://laravel.com)
[![Latest Stable Version](https://poser.pugx.org/googleshokry/laravelpwa/v/stable)](https://packagist.org/packages/googleshokry/laravelpwa)
[![Latest Unstable Version](https://poser.pugx.org/googleshokry/laravelpwa/v/unstable.svg)](https://packagist.org/packages/googleshokry/laravelpwa)
[![Total Downloads](https://poser.pugx.org/googleshokry/laravelpwa/downloads.png)](https://packagist.org/packages/googleshokry/laravelpwa)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages//googleshokry/laravelpwa)

This Laravel pakage turns your project into a [progressive web app](https://developers.google.com/web/progressive-web-apps/).  Navigating to your site on an Android phone will prompt you to add the app to your home screen.

Launching the app from your home screen will display your app.  As such, it's critical that your application provides all navigation within the HTML (no reliance on the browser back or forward button).


Requirements
=====
Progressive Web Apps require HTTPS unless being served from localhost.  If you're not already using HTTPS on your site, check out [Let's Encrypt](https://letsencrypt.org/) and [ZeroSSL](https://zerossl.com/).

## Installation

Add the following to your `composer.json` file :

```json
"require": {
    "googleshokry/laravelpwa": "^1.0.0",
},
```

or execute

```bash
composer require googleshokry/laravelpwa
```

### Publish

```bash
$ php artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"
```

### Configuration

Configure your app name, description, and icons in `config/laravelpwa.php`.

```php
'manifest' => [
        'name' => env('APP_NAME', 'My PWA App'),
        'short_name' => 'PWA',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'icons' => [
            '72x72' => '/images/icons/icon-72x72.png',
            '96x96' => '/images/icons/icon-96x96.png',
            '128x128' => '/images/icons/icon-128x128.png',
            '144x144' => '/images/icons/icon-144x144.png',
            '152x152' => '/images/icons/icon-152x152.png',
            '192x192' => '/images/icons/icon-192x192.png',
            '384x384' => '/images/icons/icon-384x384.png',
            '512x512' => '/images/icons/icon-512x512.png',
        ]
    ]
```

Include within your `<head>` the blade directive `@laravelPWA` this should include the appropriate meta tags, the link to `manifest.json` and the serviceworker script.

how this example:
```html
<!-- Web Application Manifest -->
<link rel="manifest" href="/manifest.json">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="#000000">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="PWA">
<link rel="icon" sizes="512x512" href="/images/icons/icon-512x512.png">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="PWA">
<link rel="apple-touch-icon" href="/images/icons/icon-512x512.png">

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/images/icons/icon-512x512.png">

<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
            scope: '.' 
        }).then(function (registration) {
            // Registration was successful
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
</script>
```


Troubleshooting
=====
While running the Laravel test server:

1. Verify that `/manifest.json` is being served
1. Verify that `/serviceworker.js` is being served
1. Use the Application tab in the Chrome Developer Tools to verify the progressive web app is configured correctly.
1. Use the "Add to homescreen" link on the Application Tab to verify you can add the app successfully.

The Service Worker
=====
By default, the service worker implemented by this app is:
```js
var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
```
To customize service worker functionality, update the `public_path/serviceworker.js`.

The offline view
=====
By default, the offline view is implemented in `resources/views/modules/laravelpwa/offline.blade.php`

```html
@extends('layouts.app')

@section('content')

    <h1>You are currently not connected to any networks.</h1>

@endsection
```
To customize update this file.
 
## Contributing

Contributing is easy! Just fork the repo, make your changes then send a pull request on GitHub. If your PR is languishing in the queue and nothing seems to be happening, then send Silvio an [email](mailto:googleshokry@gmail.com).