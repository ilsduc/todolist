import axios from 'axios';
import qs from 'qs';

import { URL_API } from '../../confs.js'

const todos = {
  fetchTodos: () =>
    axios.get(URL_API+'/todos', {}).then(res => res.data.data),
  postTodo: (todo) =>
    axios.post(URL_API+'/todos', qs.stringify(todo)).then(res => res.data.data),
  deleteTodo: (id) =>
    axios.delete(URL_API+'/todos/'+id).then( res => res.data.data),
  putTodo: (todo) =>
    axios.put(URL_API+'/todos/'+todo.id, qs.stringify(todo)).then(res => res.data.data),
}

export { todos };
