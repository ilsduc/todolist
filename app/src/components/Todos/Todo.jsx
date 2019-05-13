import React, { Component } from 'react';
import { putTodo } from './actions';
import { connect } from 'react-redux';
import { boolToInt } from '../../utils';
import {
  Page,
  Navbar,
  NavRight,
  Link,
  Block,
  BlockTitle,
  List,
  ListInput,
  ListItem,
  Button,
} from 'framework7-react';

class Todo extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }
  componentDidMount() {
    this.setState(this.props.todo);
  }
  // update state with props
  componentWillReceiveProps(nextProps) {
    if (nextProps.todo.id !== this.props.todo.id) {
      this.setState(nextProps.todo);
    }
  }
  // handle user input
  handleChange(e) {
    this.setState({
      [e.target.name]: e.target.value,
    });
  }
  // handle user input
  handleCheck(e) {
    this.setState({
      [e.target.name]: e.target.checked?1:0,  // tricks to keep state values in integer (SQL fix)
      // handle callback, setState is an asynchronous function
    }, () => console.log('Huuum... Really done?'));
  }
  // put request
  put(e, todo) {
    // convert bool into boolean - true : 1, false : 0
    todo = boolToInt(todo);
    // put it
    this.props.putTodo(todo);
  }

  render() {
    const { todo, error } = this.props;
    // get data from state
    const { content, done } = this.state;
    //
    return (
      <Page>
        <Navbar title="Edit todo" backLink="Back">
          <NavRight>
            <Link onClick={(e) => this.put(e, this.state)} back>Save</Link>
          </NavRight>
        </Navbar>
        <BlockTitle>Edit a todo</BlockTitle>
        {
          // display loader while loading
          !this.state > 0 &&
            <div className="text-align-center">
              <div className="preloader color-blue"></div>
            </div>
        }
        {
          todo.id && // prevent default checking with non evaluable value
            // input form
            <List noHairlines>
              <ListInput
                outline
                label={"Edit todo"}
                floatingLabel
                type={"textarea"}
                resizable
                placeholder={"Edit todo"}
                name={"content"}
                onChange={(e) => this.handleChange(e)}
                value={content}
               >
               </ListInput>
               <ListItem
                 name={"done"}
                 onChange={(e) => this.handleCheck(e)}
                 checkbox
                 checked={Boolean(parseInt(done))}
                 title="Done"
                 >
               </ListItem>
            </List>
        }
        {
          error &&
            <Block style={{ color: 'red' }}>
              { error }
            </Block>
        }
      </Page>
    );
  }
}

// map redux state with component props by passing to connect HOC
const mapStateToProps = (state) => ({
  todo: state.todos.selectedTodo,
  error: state.todos.error,
});

export default connect(mapStateToProps, { putTodo })(Todo);
