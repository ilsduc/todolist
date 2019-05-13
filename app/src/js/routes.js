import Todos from '../components/Todos/Todos.jsx';
import Todo from '../components/Todos/Todo.jsx';
import NotFoundPage from '../pages/404.jsx';

var routes = [
  {
    path: '/',
    component: Todos,
  },
  {
    path: '/todos/:id',
    component: Todo
  },
  {
    path: '(.*)',
    component: NotFoundPage,
  },
];

export default routes;
