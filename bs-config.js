module.exports = {
  proxy: "localhost:8000",
  files: [
    "public/**/*.php",
    "includes/**/*.php",
    "templates/**/*.php",
    "assets/css/**/*.css",
    "assets/js/**/*.js",
  ],
  notify: false,
  open: false,
};
