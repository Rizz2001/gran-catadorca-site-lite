self.addEventListener('install', (e) => {
  console.log('Service Worker instalado');
});
self.addEventListener('fetch', (e) => {
  // Permite que la app siga funcionando aunque haya cortes rápidos de red
});