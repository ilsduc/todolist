import React, { Component } from 'react';
import {
  Page,
  Navbar,
  NavRight,
  Link,
  BlockTitle,
  Block,
  NavTitle,
  Nav,
} from 'framework7-react';

class HowItWorks extends Component {
  render() {
    return (
      <Page >
        <Navbar>
          <NavRight>
            <Link iconOnly popupClose iconMaterial={"close"} />
          </NavRight>
          <NavTitle>How it works?</NavTitle>
        </Navbar>
        <BlockTitle colorTheme={'orange'}>A Simple todolist</BlockTitle>
        <Block strong>
          Here is a simple example of list/detail (CRUD) generic implementation.
        </Block>
        <BlockTitle colorTheme={'orange'}>Frontend</BlockTitle>
        <Block strong>
          This app uses <Link href="https://reactjs.org/" external>ReactJS</Link> mixed with <Link href="https://framework7.io/">Framework7</Link> for UI builind. It's also uses Redux and Redux-Saga in order to handle actions.
          Let's install <code> <Link color="red" href="https://www.google.fr/search?q=redux+tools&oq=redux+tools&aqs=chrome..69i57j0l5.1670j1j7&sourceid=chrome&ie=UTF-8">Redux tools</Link></code> and open up you're element inspector. Go to redux tab and see what happens.
        </Block>
        <BlockTitle colorTheme={'orange'}>Backend</BlockTitle>
        <Block strong>
          All data comes from an API. <br/>
          Go to <code> <Link color="red"  external href="http://localhost:4000/documentation">http://localhost:4000/documentation</Link></code> to see the relative auto-generated documentation. <br/>
          (Please note that this endpoint is only reacheable in development mod).
        </Block>
      </Page>
    );
  }
}

export default HowItWorks;
