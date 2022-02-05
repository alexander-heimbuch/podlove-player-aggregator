import { defineConfig } from 'vite'
import * as path from 'path'
import react from '@vitejs/plugin-react'

const root = path.resolve(__dirname)

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    lib: {
      entry: path.resolve(__dirname, 'admin/js/settings.jsx'),
      name: 'PodloveAggregatorSettings',
      fileName: (format) => `settings.js`
    },
    outDir: path.resolve(root, 'admin', 'dist'),
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        entryFileNames: `settings.js`,
        chunkFileNames: `chunk-[name].js`,
        assetFileNames: `style.[ext]`
      },
      external: ['wp']
    }
  }
})
