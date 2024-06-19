self.addEventListener('install', event => {
  event.waitUntil(
    caches.open('media-library-v1').then(cache => {
      return cache.addAll([
        '/',
        '/index.html',
        '/auth.css',
        // Ajoutez d'autres fichiers à mettre en cache ici
      ]).catch(error => {
        console.error('Failed to cache:', error);
      });
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});
