/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
      './resources/**/*.ts',  // Thêm hỗ trợ cho TypeScript
    ],
    theme: {
      extend: {
        // Bạn có thể thêm các tùy chỉnh cho theme tại đây nếu cần
        colors: {
          'custom-color': '#ffcc00', // Ví dụ về màu tùy chỉnh
        },
      },
    },
    plugins: [],
  }
  