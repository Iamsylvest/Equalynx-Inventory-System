/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class', // enable dark mode using the class method
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
        'custom-white': '#E5E7EB', 
        'custom-table': '#1F1F1F', 
        'custom-hover': '#2D3748',
        'custom-main': '#121212', 
    },
      fontFamily: {
        roboto: ["Roboto", "sans-serif"], // ✅ Add Roboto Font
    },
    },
  },
  plugins: [],
}