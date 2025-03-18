/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',  // Blade views
    './resources/js/**/*.vue',           // Vue components
    './resources/css/**/*.css',          // CSS files
  ],
  theme: {
    extend: {
      animation: {
        customPulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      colors: {
        'custom-blue': '#365486', // Define your custom color
    },
      fontFamily: {
        roboto: ["Roboto", "sans-serif"], // ✅ Add Roboto Font
    },
    },
  },
  plugins: [],
}