import {
  FETCH_TODOS,
  FETCH_TODOS_SUCCESS,
  //
  POST_TODO_SUCCESS,
  POST_TODO_FAILED,
  //
  SELECT_TODO,
  //
  PUT_TODO_SUCCESS,
  PUT_TODO_FAILED,
  //
  DELETE_TODO_SUCCESS,
  DELETE_TODO_FAILED,
 } from './actions';

const initialState = {
  todos: [],
  selectedTodo: {},
  fetching: false,
  error: null,
};

export default function(state = initialState, action) {
  let newState = state;
  // blank error
  newState.error = null;
  // switch on action type
  switch (action.type) {
    // fetch
    case FETCH_TODOS:
      return {...state, fetching: true};
      break;
    case FETCH_TODOS_SUCCESS:
      return {...newState, fetching: false, todos: action.payload};
      break;

    // post
    case POST_TODO_SUCCESS:
      return {...newState, todos: [action.payload, ...state.todos]};
      break;
    case POST_TODO_FAILED:
      return {...newState, error: action.payload};
      break;

    // put
    case PUT_TODO_SUCCESS: {
      let index = newState.todos.findIndex((item) => item.id == action.payload.id);
      newState.todos[index] = action.payload;
      return {...newState, todos: newState.todos, selectedTodo: action.payload};
    }
    break;
    case PUT_TODO_FAILED:
    return {...newState, error: action.payload};
    break;

    // select
    case SELECT_TODO:
    return {...newState, selectedTodo: action.payload};
    break;

    // delete
    case DELETE_TODO_SUCCESS: {
        // get index
        let index = newState.todos.findIndex((item) => item.id == action.payload.id);
        //  retrieve
        newState.todos.splice(index, 1);
        //
        return {...newState, todos: newState.todos};
      }
      break;
    case DELETE_TODO_FAILED:
      return {...newState, error: action.payload};
      break;
    default:
      return newState;
  }
}
