/* The localStorage.js file make the data persistent within the local storage */
export const loadState = () => {
  try {
    const serializedState = localStorage.getItem('todolist.kgn8xvg8s261be59d');
    if (serializedState === null) {
      return undefined;
    }
    return JSON.parse(serializedState);
  } catch (err) {
    return undefined;
  }
};

export const saveState = (state) => {
  try {
    const serializedState = JSON.stringify(state);
    localStorage.setItem('todoslist.kgn8xvg8s261be59d', serializedState);
  } catch {

  }
}
