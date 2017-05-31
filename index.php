<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>To-do list</title>
  <script type="text/javascript" src="js/vue.js"></script>
    <script src="js/jquery-latest.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="icon" href="./todo/favicon.ico" type="image/x-icon" />
</head>
<body>
  <section class="todoapp">
    <header class="header">
      <h1>To-do</h1>
      <input class="new-todo"
        autofocus autocomplete="off"
        placeholder="What needs to be done?"
        v-model="newTodo"
        @keyup.enter="addTodo">
    </header>
    <section class="main" v-show="todos.length" v-cloak>
      <input class="toggle-all" type="checkbox" v-model="allDone">
      <ul class="todo-list">
        <li v-for="todo in filteredTodos"
          class="todo"
          :key="todo.id"
          :class="{ completed: todo.completed === '1' || todo.completed === true, editing: todo == editedTodo }">
          <div class="view">
            <input @click="updateTodo(todo)" class="toggle" type="checkbox" v-model.number="todo.completed">
            <label @dblclick="editTodo(todo)">{{ todo.title }}</label>
            <button class="destroy" @click="removeTodo(todo)"></button>
          </div>
          <input class="edit" type="text"
            v-model="todo.title"
            v-todo-focus="todo == editedTodo"
            @blur="doneEdit(todo)"
            @keyup.enter="doneEdit(todo)"
            @keyup.esc="cancelEdit(todo)">
        </li>
      </ul>
    </section>
    <footer class="footer" v-show="todos.length" v-cloak>
      <span class="todo-count">
        <strong>{{ remaining }}</strong> {{ remaining | pluralize }} left
      </span>
      <ul class="filters">
        <li><a href="#/all" :class="{ selected: visibility == 'all' }">All</a></li>
        <li><a href="#/active" :class="{ selected: visibility == 'active' }">Active</a></li>
        <li><a href="#/completed" :class="{ selected: visibility == 'completed' }">Completed</a></li>
      </ul>
      <button class="clear-completed" @click="removeCompleted" v-show="todos.length > remaining">
        Clear completed
      </button>
    </footer>
  </section>
  <script type="text/javascript" scr="js/main.js"></script>
  <script type="text/javascript">
    // Full spec-compliant TodoMVC with mysql persistence
    // and hash-based routing in ~120 effective lines of JavaScript.

    // mysql persistence
    var STORAGE_KEY = 'todos-vuejs-2.0'
    var todoStorage = {
      fetch: function () {
        var todos = <?php 
          require("includes/connect.php");
          $result = $conn->query("SELECT * FROM todo ORDER BY date ASC, time ASC");
          $arr = array();
          foreach($result as $row) {
            array_push($arr, $row);
          }
          echo json_encode($arr);
        ?>;
        return todos;
      },
      // save: function (todos) {
      //   // localStorage.setItem(STORAGE_KEY, JSON.stringify(todos))
      // },
      add: function (todo) {
        $.post('includes/add-task.php', { task: todo.title }, function( data ) {
        });
      },
      update: function (todo) {
        $.post('includes/update-task.php', { task_id: todo.id,
          title: todo.title,
          completed: todo.completed }, function() {
        });
      },
      delete: function (todo) {
        $.post('includes/delete-task.php', { task_id: todo.id }, function() {
        });
      }
    }

    // visibility filters
    var filters = {
      all: function (todos) {
        return todos
      },
      active: function (todos) {
        return todos.filter(function (todo) {
          return !(todo.completed === '1' || todo.completed === true)
        })
      },
      completed: function (todos) {
        return todos.filter(function (todo) {
          return (todo.completed === '1' || todo.completed === true)
        })
      }
    }

    // app Vue instance
    var app = new Vue({
      // app initial state
      data: {
        todos: todoStorage.fetch(),
        newTodo: '',
        editedTodo: null,
        visibility: 'all'
      },

      // watch todos change for localStorage persistence
      // watch: {
      //   todos: {
      //     handler: function (todos) {
      //       todoStorage.save(todos)
      //     },
      //     deep: true
      //   }
      // },

      // computed properties
      // http://vuejs.org/guide/computed.html
      computed: {
        filteredTodos: function () {
          return filters[this.visibility](this.todos)
        },
        remaining: function () {
          return filters.active(this.todos).length
        },
        allDone: {
          get: function () {
            return this.remaining === 0
          },
          set: function (value) {
            // this.todos.forEach(function (todo) {
            //   todo.completed = value
            // })
            window.location.href = value ? "#/all" : '#/active';
          }
        }
      },

      filters: {
        pluralize: function (n) {
          return n === 1 ? 'item' : 'items'
        }
      },

      // methods that implement data logic.
      // note there's no DOM manipulation here at all.
      methods: {
        addTodo: function () {
          var value = this.newTodo && this.newTodo.trim()
          if (!value) {
            return
          }
          this.todos.push({
            title: value,
            completed: '0'
          })
          this.newTodo = ''
          
          todoStorage.add({title: value});
          setTimeout(function() {
            this.todos = todoStorage.fetch();
          }, 1000);
        },

        updateTodo: function (todo) {
          var completed = todo.completed === '1' || todo.completed === true ? 0 : 1;
          todoStorage.update({
            id: todo.id,
            title: todo.title,
            completed: completed
          });
        },

        removeTodo: function (todo) {
          todoStorage.delete(todo);
          this.todos.splice(this.todos.indexOf(todo), 1);
        },

        editTodo: function (todo) {
          this.beforeEditCache = todo.title
          this.editedTodo = todo
        },

        doneEdit: function (todo) {
          if (!this.editedTodo) {
            return
          }
          this.editedTodo = null
          todo.title = todo.title.trim()
          if (!todo.title) {
            this.removeTodo(todo)
          }
        },

        cancelEdit: function (todo) {
          this.editedTodo = null
          todo.title = this.beforeEditCache
        },

        removeCompleted: function () {
          this.todos = filters.active(this.todos)
        }
      },

      // a custom directive to wait for the DOM to be updated
      // before focusing on the input field.
      // http://vuejs.org/guide/custom-directive.html
      directives: {
        'todo-focus': function (el, binding) {
          if (binding.value) {
            el.focus()
          }
        }
      }
    })

    // handle routing
    function onHashChange () {
      var visibility = window.location.hash.replace(/#\/?/, '')
      if (filters[visibility]) {
        app.visibility = visibility
      } else {
        window.location.hash = ''
        app.visibility = 'all'
      }
    }

    window.addEventListener('hashchange', onHashChange)
    onHashChange()

    // mount
    app.$mount('.todoapp')
  </script>
</body>
</html>
