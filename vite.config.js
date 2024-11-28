import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                // "resources/js/bootstrap.js",
            ],
            refresh: true,
        }),
    ],
    server: {
        host: "0.0.0.0", // Allow Vite to be accessed from any IP
        port: 5173, // Ensure the port matches your Vite server configuration
        hmr: {
            host: "192.168.100.101", 
            // host: "192.168.100.194", // Adjust HMR host if necessary //
        },
        // cors: {
        //     // origin: "*",
        //     origin: ["http://192.168.33.11:8000", "http://10.10.5.202:8000"],
        //     methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        //     allowedHeaders: ["Content-Type", "Authorization"],
        // },
    },
    //
    base: "/",
});
// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: [
//                 'resources/css/app.css',
//                 'resources/js/app.js',
//             ],
//             refresh: true,
//         }),
//     ],
// });
