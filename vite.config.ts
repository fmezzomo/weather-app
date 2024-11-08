import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import * as path from "path";

// https://vite.dev/config/
export default defineConfig({
  plugins: [
      laravel({
          input: 'resources/src/main.ts',
          refresh: true,
      }),
      vue({
        template: {
            transformAssetUrls: {
                base: null,
                includeAbsolute: false,
            },
        },
    })
  ],
  resolve: {
      alias: {
          '@': path.resolve(__dirname, 'resources/src'),
        },
  },
  define: {
    'process.env': {},
  },
})