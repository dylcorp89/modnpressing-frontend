/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    theme: {
        extend: {
            colors:{
               
                transparent: 'transparent',
              current: 'currentColor',
              'white': '#ffffff',
              'purple': '#242424',
              'midnight': '#242424',
              'metal': '#565584',
              'tahiti': '#3ab7bf',
              'silver': '#ecebff',
              'bubble-gum': '#ff77e9',
              'bermuda': '#78dcca',
            },
        },
    },
  },
  plugins: [],
}

