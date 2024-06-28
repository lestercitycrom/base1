// конфигурация маршрутов
const routes = [
  { path: '/', component: Home },
  { path: '/orders', component: Orders },
  { path: '/login', component: Login }
];

const router = new VueRouter({
  mode: 'history', // использование режима history
  routes // сокращённая запись для `routes: routes`
});

// Проверка авторизации
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (!token && to.path !== '/login') {
    next('/login');
  } else {
    next();
  }
});