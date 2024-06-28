Vue.component('example-component', {
  data: function () {
    return {
      message: 'Hello from Example Component!'
    }
  },
  template: '<div><h1>{{ message }}</h1></div>'
});