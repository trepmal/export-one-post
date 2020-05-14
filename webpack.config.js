module.exports = {
  entry: "./assets/sidebar-button.js",
  output: {
    path: __dirname,
    filename: "assets/sidebar-button.build.js",
  },
  module: {
    loaders: [
      {
        test: /.js$/,
        loader: "babel-loader",
        exclude: /node_modules/,
      },
    ],
  },
};
