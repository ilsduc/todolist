import { combineReducers } from 'redux';
import todosReducer from '../components/Todos/reducer.js';

export default combineReducers({
  todos: todosReducer,
});
