import "regenerator-runtime/runtime";
import { createStore, applyMiddleware, compose } from 'redux'
import createSagaMiddleware from 'redux-saga'
import rootReducer from './reducers';
import { rootSagas } from './sagas';
import { loadState, saveState } from './local-storage';

/* active the first option for peristant data in local storage */
// const initialState = loadState();
const initialState = {};

const sagaMiddleware = createSagaMiddleware();

export const initializeSagas = () => {
  sagaMiddleware.run(rootSagas);
};

export const initializeStore = () => {
  // creeate the store
  const store = createStore(
    rootReducer,
    initialState,
    getMiddleWare()
  );

  store.subscribe(() => {
    saveState(store.getState());
  });

  return store;
};

const getMiddleWare = () => {
  if ((!process.env.NODE_ENV || process.env.NODE_ENV === 'development') /*&& !isMobile()*/) {
    return compose(
        applyMiddleware(sagaMiddleware),
        window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()  // Activate the extension
      );
  }else {
      return applyMiddleware(sagaMiddleware);
  }
}
