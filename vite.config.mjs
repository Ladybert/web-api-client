import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          // Memisahkan vendor ke dalam chunk terpisah
          if (id.includes('node_modules')) {
            return 'vendor';
          }
        },
      },
    },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: [
        'app/Filament/**',
        'app/Forms/Components/**',
        'app/Livewire/**',
        'app/Infolists/Components/**',
        'app/Providers/Filament/**',
        'app/Tables/Columns/**',
      ],
    }),
  ],
});
