import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Optimized CSS inputs
                'public/css/design-system.css',
                'public/css/components-optimized.css',
                'public/css/login.css',
                
                // JavaScript inputs
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/dashboard.js',
                'public/js/navigation.js'
            ],
            refresh: true,
        }),
    ],
    
    build: {
        // Enable CSS minification
        cssMinify: true,
        
        // Enable JavaScript minification
        minify: 'esbuild',
        
        // Optimize bundle splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    // Core navigation functionality
                    navigation: ['public/js/navigation.js'],
                    
                    // Admin functionality
                    admin: ['resources/js/admin.js', 'resources/js/dashboard.js'],
                    
                    // Core styles
                    'design-system': ['public/css/design-system.css'],
                    'components': ['public/css/components-optimized.css']
                },
                
                // Optimize file naming
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const extType = assetInfo.name.split('.').pop();
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        return 'images/[name]-[hash].[ext]';
                    }
                    if (/css/i.test(extType)) {
                        return 'css/[name]-[hash].[ext]';
                    }
                    return 'assets/[name]-[hash].[ext]';
                }
            }
        },
        
        // Asset optimization
        assetsInlineLimit: 4096,
        sourcemap: false,
        target: 'es2015',
        
        // Performance optimization
        chunkSizeWarningLimit: 500
    },
    
    // CSS optimization
    css: {
        devSourcemap: true,
        preprocessorOptions: {
            css: {
                charset: false
            }
        }
    },
    
    // Development server
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: false
        }
    },
    
    // Dependency optimization
    optimizeDeps: {
        include: [],
        exclude: ['public/js/navigation.js']
    },
    
    // Path aliases
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
            '@public': resolve(__dirname, 'public'),
            '@css': resolve(__dirname, 'public/css'),
            '@js': resolve(__dirname, 'public/js')
        }
    }
});
