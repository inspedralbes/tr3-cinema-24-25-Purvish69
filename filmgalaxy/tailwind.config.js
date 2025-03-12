/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      "./index.html",
      "./src/**/*.{vue,js,ts,jsx,tsx}",
    ],
    theme: {
      extend: {
        colors: {
          primary: '#22223B',
          secondary: '#4A4E69',
          accent: '#9A8C98',
          neutral: '#C9ADA7',
          light: '#F2E9E4',
          dark: '#1C1C1C',
          gold: '#C8A96E',
          cream: '#EAE0D5',
        },
      },
    },
    plugins: [],
  }