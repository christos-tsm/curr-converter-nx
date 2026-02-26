import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: resolve(__dirname, '../assets/build'),
    emptyOutDir: true,
    cssCodeSplit: false,
    rollupOptions: {
      input: resolve(__dirname, 'src/main.tsx'),
      output: {
        entryFileNames: 'currency-converter.js',
        chunkFileNames: '[name].js',
        assetFileNames: () => 'currency-converter.css',
      },
    },
  },
  server: {
    port: 3100,
    open: '/index.html',
  },
});
