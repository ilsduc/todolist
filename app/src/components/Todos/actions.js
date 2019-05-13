// fetch todo
export const FETCH_TODOS = 'FETCH_TODOS';
export const fetchTodos = () => ({
  type: FETCH_TODOS
});

export const FETCH_TODOS_SUCCESS = 'FETCH_TODOS_SUCCESS';

// post todo
export const POST_TODO = 'POST_TODO';
export const postTodo = (todo) => ({
  type: POST_TODO, payload: todo
})
export const POST_TODO_SUCCESS = 'POST_TODO_SUCCESS';
export const POST_TODO_FAILED = 'POST_TODO_FAILED';

// delete todo
export const DELETE_TODO = 'DELETE_TODO';
export const deleteTodo = (id) => ({
  type: DELETE_TODO, payload: id
})
export const DELETE_TODO_SUCCESS = 'DELETE_TODO_SUCCESS';
export const DELETE_TODO_FAILED = 'DELETE_TODO_FAILED';

// put todo
export const PUT_TODO = 'PUT_TODO';
export const putTodo = (todo) => ({
  type: PUT_TODO, payload: todo
})
export const PUT_TODO_SUCCESS = 'PUT_TODO_SUCCESS';
export const PUT_TODO_FAILED = 'PUT_TODO_FAILED';

// select todo
export const SELECT_TODO = 'SELECT_TODO';
export const selectTodo = (todo) => ({
  type: SELECT_TODO, payload: todo
});
