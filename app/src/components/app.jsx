import React from 'react';
import {
  App,
  Statusbar,
  View,
  Page,
  Panel,
  Block,
  BlockTitle,
  List,
  Link,
  ListButton,
  ListItem,
  Popup
} from 'framework7-react';
import { Provider } from 'react-redux';
import cordovaApp from '../js/cordova-app';
import routes from '../js/routes';
import HowItWorks from './HowItWorks/HowItWorks';

class Main extends React.Component {
  constructor() {
    super();

    this.state = {
      // Framework7 Parameters
      f7params: {
        id: 'todolist.test', // App bundle ID
        name: 'Todolist', // App name
        theme: 'auto', // Automatic theme detection
        // App routes
        routes: routes,
        // Input settings
        input: {
          scrollIntoViewOnFocus: this.$device.cordova && !this.$device.electron,
          scrollIntoViewCentered: this.$device.cordova && !this.$device.electron,
        },
        // Cordova Statusbar settings
        statusbar: {
          overlay: this.$device.cordova && this.$device.ios || 'auto',
          iosOverlaysWebView: true,
          androidOverlaysWebView: false,
        },
      },
      themeDark: window.localStorage.getItem('themeDark')==='true'||false,
    }
  }

  toggleTheme() {
    this.setState({
        themeDark: !this.state.themeDark
      }, () => {
        // handle callback
        window.localStorage.setItem('themeDark', this.state.themeDark);
        document.getElementById('meta-theme-color').setAttribute('content', this.state.themeDark?'#000':'#F7F7F7');
      });
  }

  render() {
    const { f7params, themeDark, effect } = this.state;
    //
    document.getElementById('meta-theme-color').setAttribute('content', this.state.themeDark?'#000':'#F7F7F7');
    //
    return (
      // <Provider store={store}>
        <App colorTheme="orange" themeDark={Boolean(themeDark)} params={ f7params } >
          {/* Status bar overlay for fullscreen mode*/}
          <Statusbar></Statusbar>
          <Panel left reveal>
            <View>
              <Page>
                <BlockTitle>Informations</BlockTitle>
                <List>
                  <ListButton popupOpen=".how-it-works-popup">How it works?</ListButton>
                </List>
                <BlockTitle>Theming</BlockTitle>
                <List>
                  <ListItem checkbox title="Enable dark theme" checked={Boolean(themeDark)} onClick={() => this.toggleTheme()} />
                </List>
              </Page>
            </View>
          </Panel>

          <View id="view-home" main url="/" />

          <Popup className={'how-it-works-popup'}>
            <HowItWorks />
          </Popup>
        </App>
    )
  }

  componentDidMount() {
    this.$f7ready((f7) => {
      // Init cordova APIs (see cordova-app.js)
      if (f7.device.cordova) {
        cordovaApp.init(f7);
      }
    });
  }
}

export default Main;
