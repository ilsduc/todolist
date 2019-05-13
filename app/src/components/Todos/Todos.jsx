import React from 'react';
import {
  Page,
  Navbar,
  NavLeft,
  NavTitle,
  Link,
  Row,
  Col,
  Block,
  BlockTitle,
  Popover,
  List,
  ListButton,
  Icon,
} from 'framework7-react';
import { fetchTodos, selectTodo, deleteTodo } from './actions';
import { connect } from 'react-redux';
import { timestampToDay, timestampToMonth } from '../../utils';
import AddTodo from './AddTodo.jsx';

// style
const style = {
  todoBox: {
    display: 'flex',
  },
  content: {
    flex: '1',
    display: 'flex',
    alignItems: 'center',
  },
  actions: {
    marginLeft: '1rem',
  },
  flexbox: {
    display: 'flex',
  },
  flex: {
    flex: '1',
  },
  done: {
    color: 'green',
  },
};

class Todos extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }
  // fetch todos on Mounting,
  // doing stuff in f7ready method is recommended (read the doc)
  componentDidMount() {
    this.$f7ready(($f7) => (
      this.props.fetchTodos()
    ));
  }

  render() {
    const { todos, fetching } = this.props.todos;
    return (
      <Page name="home">
        {/* Top Navbar */}
        <Navbar sliding={false}>
          <NavLeft>
            <Link iconIos="f7:menu" iconAurora="f7:menu" iconMd="material:menu" panelOpen="left" />
          </NavLeft>
          <NavTitle sliding>Todos</NavTitle>
        </Navbar>

        <Row noGap>
          <Col width={"100"} tabletWidth={"50"}>
            <AddTodo />
          </Col>
        </Row>

        {/* Page content */}
        <BlockTitle>
          {
            (todos && todos.length > 0) &&
              todos.length + ' todo(s). And you\'re already late...'
          }
        </BlockTitle>

        <Block>
          {
            ((!todos || (todos && !todos.length > 0)) && fetching) &&
              <div className="text-align-center">
                <div className="preloader color-blue"></div>
              </div>
          }
          {
            ((todos && todos.length) && !fetching) &&
              <Row>
                <Col width={'100'} tabletWidth={"50"}>
                  <div className="timeline">
                    {
                      todos.map((todo) => (
                        <div key={todo.id} className="timeline-item">
                          <div className="timeline-item-date">{timestampToDay(todo.date_inserted)} <small>{timestampToMonth(todo.date_inserted)}</small></div>
                          <div className="timeline-item-divider"></div>
                          <div className="timeline-item-content">
                            <div className="timeline-item-inner">
                              <div style={style.todoBox}>
                                <div style={style.content}>
                                  <div className="timeline-item-title">{todo.content}</div>
                                </div>
                                <div style={style.actions}>
                                  <Link iconOnly popoverOpen={".popover-menu"+todo.id} iconMaterial={'more_vert'} iconSize={'20px'}/>
                                  <Popover backdrop={false} className={"list-menu-popover popover-menu"+todo.id}>
                                    <List >
                                      <ListButton popoverClose onClick={() => this.props.selectTodo(todo)} href={'/todos/'+todo.id} title="Edit" />
                                      <ListButton popoverClose onClick={() => this.props.deleteTodo(todo.id)} title="Delete" />
                                    </List>
                                  </Popover>
                                  <div>
                                    { todo.done > 0 && <Icon style={style.done} f7="check"></Icon>}
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      ))
                    }
                  </div>
                </Col>
              </Row>
          }
        </Block>

      </Page>
    );
  }
}

const mapStateToProps = (state) => ({
  todos: state.todos,
});

export default connect(mapStateToProps, { fetchTodos, selectTodo, deleteTodo })(Todos);
