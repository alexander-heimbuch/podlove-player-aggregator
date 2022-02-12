import { defineConfig } from 'vite'
import * as path from 'path'
import react from '@vitejs/plugin-react'

const root = path.resolve(__dirname, '..')

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  publicDir: false,
  build: {
    lib: {
      entry: path.resolve(root, 'admin', 'settings', 'main.jsx'),
      name: 'PodloveAggregatorSettings',
      fileName: (format) => `settings.js`
    },
    outDir: path.resolve(root, 'admin', 'dist'),
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        entryFileNames: `settings.js`,
        chunkFileNames: `chunk-[name].js`,
        assetFileNames: `settings.[ext]`
      },
      external: ['wp']
    }
  }
})
