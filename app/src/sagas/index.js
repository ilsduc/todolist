import { all } from 'redux-saga/effects';
import todosSaga from '../components/Todos/saga';

export function* rootSagas() {
  yield all ([
    todosSaga(),
  ]);
}
