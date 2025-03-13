/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './components/**/*.{vue,js}',
    './layouts/**/*.{vue,js}',
    './pages/**/*.{vue,js}',
    './plugins/**/*.{js,ts}',
    './nuxt.config.{js,ts}',
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
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/line-clamp'),
  ],
};