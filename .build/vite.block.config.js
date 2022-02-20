import { defineConfig } from "vite";
import * as path from "path";
import react from "@vitejs/plugin-react";

const root = path.resolve(__dirname, "..");

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  publicDir: false,
  build: {
    lib: {
      entry: path.resolve(root, "block", "block.jsx"),
      name: "PodloveAggregatorBlock",
      fileName: (format) => `block.js`,
    },
    outDir: path.resolve(root, "block", "dist"),
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        entryFileNames: `block.js`,
        chunkFileNames: `chunk-[name].js`,
        assetFileNames: `block.[ext]`,
      },
      external: ["wp"],
    },
  },
});
