/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/Views/**/*.php',
    './app/Controllers/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: 'hsl(158 30% 38%)',
          foreground: '#ffffff',
        },
        accent: {
          DEFAULT: 'hsl(35 60% 55%)',
          foreground: '#ffffff',
        },
        info: {
          DEFAULT: 'hsl(200 60% 50%)',
          foreground: '#ffffff',
        },
        success: {
          DEFAULT: 'hsl(158 40% 42%)',
          foreground: '#ffffff',
        },
        warning: {
          DEFAULT: 'hsl(35 85% 50%)',
          foreground: '#ffffff',
        },
        destructive: {
          DEFAULT: 'hsl(0 65% 55%)',
          foreground: '#ffffff',
        },
        muted: {
          DEFAULT: 'hsl(40 15% 94%)',
          foreground: 'hsl(220 10% 46%)',
        },
        border: 'hsl(40 15% 89%)',
        background: 'hsl(40 20% 97%)',
        foreground: 'hsl(220 20% 15%)',
      },
      boxShadow: {
        panel: '0 8px 24px rgba(15, 23, 42, 0.05)',
      },
    },
  },
  plugins: [],
};
