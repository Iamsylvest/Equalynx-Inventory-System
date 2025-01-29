/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',  // Blade views
    './resources/js/**/*.vue',           // Vue components
    './resources/css/**/*.css',          // CSS files
  ],
  theme: {
    extend: {
      colors: {
        'custom-blue': '#365486', // Define your custom color
    },
    },
  },
  plugins: [],
}