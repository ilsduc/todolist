import React, { Component } from 'react';
import { postTodo } from './actions';
import { connect } from 'react-redux';
import {
  ListButton,
  Block,
  List,
  ListInput,
  Row,
  Col,
} from 'framework7-react';

// style
const style = {
  button: {
    marginTop: '1rem',
  }
};

class Todo extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }
  // handle user input
  handleChange(e) {
    this.setState({
      [e.target.name]: e.target.value,
    });
  }
  // post method
  post(e, todo) {
    this.props.postTodo(todo);
    this.setState({ content: '' });
  }
  //
  render() {
    const { error } = this.props;
    // retrieve data from state
    const { done } = this.state;
    // return some jsx
    return (
      <>
        <div className="block-title">Add todo</div>
        <List inset noHairlines>
          <ListInput
            outline
            label={"What do you have to do?"}
            floatingLabel
            type={"textarea"}
            resizable
            placeholder={"Remember that write is not do."}
            name={"content"}
            onChange={(e) => this.handleChange(e)}
            value={this.state.content}
          >
          </ListInput>
          {
            error &&
              <Block style={{ color: 'red' }}>
                { error }
              </Block>
          }
          <ListButton raised onClick={(e) => this.post(e, this.state)} style={style.button} title={"Add todo"} color={"orange"}/>
        </List>
      </>
    );
  }
}

const mapStateToProps = (state) => ({
  error: state.todos.error,
});

export default connect(mapStateToProps, { postTodo })(Todo);
