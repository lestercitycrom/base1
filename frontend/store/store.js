// инициализация Vuex
var exampleModule = {
  state: {
    exampleData: 'Example data'
  },
  mutations: {
    setExampleData(state, payload) {
      state.exampleData = payload;
    }
  },
  actions: {
    updateExampleData({ commit }, newData) {
      commit('setExampleData', newData);
    }
  },
  getters: {
    getExampleData: state => state.exampleData
  }
};

var store = new Vuex.Store({
  state: {
    // состояние вашего приложения
  },
  mutations: {
    // мутации для изменения состояния
  },
  actions: {
    // действия для выполнения асинхронных операций
  },
  modules: {
    example: exampleModule
  }
});