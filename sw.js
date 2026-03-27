const CACHE_NAME = 'grancatador-v8'; // Subimos la versión para limpiar la basura vieja
const urlsToCache = [
  './',
  './index.html',
  './app.js',
  './logo.png',
  './manifest.json',
  'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache)));
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => Promise.all(
      cacheNames.map(cacheName => {
        if (cacheName !== CACHE_NAME) return caches.delete(cacheName);
      })
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  
  // ESTRATEGIA SEGURA: Para HTML, JS, TXT y CSV -> SIEMPRE buscar internet primero. Si no hay señal, usar el guardado.
  if (event.request.mode === 'navigate' || url.pathname.endsWith('.js') || url.pathname.endsWith('.html') || url.pathname.endsWith('.txt') || url.pathname.endsWith('.csv')) {
    event.respondWith(fetch(event.request).catch(() => caches.match(event.request)));
    return;
  }

  // Para fotos y diseño -> Usar el guardado del teléfono para que cargue como un rayo.
  event.respondWith(caches.match(event.request).then(response => response || fetch(event.request)));
});
