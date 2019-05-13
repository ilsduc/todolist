import { call, put, takeLatest } from 'redux-saga/effects';
import { todos } from './api';

// fetch todo saga
export function* fetchTodos(action) {
  try {
    const all = yield call(todos.fetchTodos, action.payload);
    yield put({type: 'FETCH_TODOS_SUCCESS', payload: all});
  } catch (e) {
    yield put({type: 'FETCH_TODOS_FAILED', payload: e.response.data.msg})
  }
}

// post todo saga
export function* postTodo(action) {
  try {
    const todo = yield call(todos.postTodo, action.payload);
    yield put({type: 'POST_TODO_SUCCESS', payload: todo});
  } catch (e) {
    yield put({type: 'POST_TODO_FAILED', payload: e.response.data.status.msg})
  }
}

// put todo saga
export function* putTodo(action) {
  try {
    const todo = yield call(todos.putTodo, action.payload);
    yield put({type: 'PUT_TODO_SUCCESS', payload: todo});
  } catch (e) {
    yield put({type: 'PUT_TODO_FAILED', payload: e.response.data.status.msg})
  }
}

// delete todo saga
export function* deleteTodo(action) {
  try {
    const todo = yield call(todos.deleteTodo, action.payload);
    yield put({type: 'DELETE_TODO_SUCCESS', payload: todo});
  } catch (e) {
    yield put({type: 'DELETE_TODO_FAILED', payload: e.response.data.status.msg})
  }
}

// main export with magix sagas listeners
function* todosSaga() {
  yield takeLatest('FETCH_TODOS', fetchTodos);
  yield takeLatest('POST_TODO', postTodo);
  yield takeLatest('DELETE_TODO', deleteTodo);
  yield takeLatest('PUT_TODO', putTodo);
}

export default  todosSaga;
