/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      maxWidth: {
        'app': '500px',
      },
      colors: {
        'primary': '#60a5fa',  // blue-400
        'primary-dark': '#3b82f6',  // blue-500
        'secondary': '#9C27B0',
        'info': '#00BCD4',
        'warning': '#FFC107',
        'danger': '#FF5722',
        'gray-light': '#F9F9F9',
        'border': '#F0F0F0',
      },
    },
  },
  plugins: [],
}