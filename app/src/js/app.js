// Import React and ReactDOM
import React from 'react';
import ReactDOM from 'react-dom';

// Import Framework7
import Framework7 from 'framework7/framework7.esm.bundle.js';

// Import Framework7-React Plugin
import Framework7React from 'framework7-react';

// Import Framework7 Styles
import 'framework7/css/framework7.bundle.css';

// Import Icons and App Custom Styles
import '../css/icons.css';
import '../css/app.css';

// Import App Component
import App from '../components/app.jsx';

// Import store and sagas initialization functions
import { initializeStore, initializeSagas } from '../store.js';

// Import provider
import { Provider } from 'react-redux';

// initialize store and sagas
const store = initializeStore();
initializeSagas();

// Init F7 Vue Plugin
Framework7.use(Framework7React);

const reduxApp = () => (
  <Provider store={store}>
    <App />
  </Provider>
);

// Mount React App
ReactDOM.render(
  React.createElement(reduxApp),
  document.getElementById('app'),
);
