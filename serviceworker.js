const CACHE_NAME = 'y-nextwach-cache';
const urlsToCache = [
  '/',
  './index.php',
  './shop.php',
  './profile.php',
  './assets/css/styles.css',
  './assets/js/main.js',
  './assets/img/icons/favicon.ico',
  './assets/img/icons/logo192.png',
  './assets/img/icons/logo512.png',
  './offline.html'  // Page à afficher quand hors ligne
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        if (response) {
          return response; // Si dans le cache, on retourne la réponse cachée
        }
        return fetch(event.request)
          .then((response) => {
            // On vérifie que la réponse est valide
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }
            
            // On clone la réponse
            const responseToCache = response.clone();
            
            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache);
              });
            
            return response;
          })
          .catch(() => {
            // Si la requête échoue (hors ligne), on retourne la page offline
            return caches.match('/offline.html');
          });
      })
  );
});

self.addEventListener('activate', (event) => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});