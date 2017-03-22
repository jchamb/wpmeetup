(function($){

    var Todos = {
        collection: [],
        itemTemplate: null,

        init: function() {

            this.itemTemplate = _.template($('#item-template').html());
            this.fetchTodos();
            // this.bind();
        },

        bind: function() {
            $('#new-todo').on('keyup', $.proxy(this.onKeyUp, this))
            $('.js-toggle').on('click', $.proxy(this.toggleCompleted, this));
        },

        render: function() {
            var that = this,
                $list = $('#todolist');

            $.each(this.collection, function(k, item) {
                $list.prepend( that.itemTemplate(item) );
            });
            this.bind();
        },

        renderOne: function(todo) {
            var $list = $('#todolist');
            $list.append( this.itemTemplate(todo) );
        },

        onKeyUp: function(event) {

            var keycode = (event.keyCode ? event.keyCode : event.which);

            if (keycode == '13') {
                this.newTodo({
                    title: $(event.currentTarget).val(),
                    completed: 0,
                    status: 'publish'
                })
            }
        },

        fetchTodos: function() {
            var that = this;

            $.get('http://vagrant.webpagefxdev.com/wp-json/wp/v2/todos')
             .done(function(response) {
                that.collection = response;
                that.render();
             });
        },

        newTodo: function(todo) {
            var that = this;

            $.ajax( {
                url: 'http://vagrant.webpagefxdev.com/wp-json/wp/v2/todos',
                method: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                },
                data: todo
            })
            .done(function(response){
                that.renderOne(response);
                that.collection.push(response);
            })
            .always(function(response){
                console.log(response)
            });
        },

        saveTodo: function(todo) {

            //console.log(todo);
            //
            $.ajax( {
                url: todo._links.self[0].href,
                method: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                },
                data:{
                    title: todo.title.rendered,
                    completed: todo.completed
                }
            })
            .done(function(response){
                console.log(response)
            })
            .always(function(response){
                console.log(response)
            });
        },

        toggleCompleted: function(event) {
            var id = $(event.currentTarget).data('id'),
                todo = _.findWhere(this.collection, {id: id});

            console.log(id, todo)

            todo.completed = ! todo.completed === true ? 1 : 0;

            this.saveTodo(todo);
        }

    };

    $(function(){
        Todos.init();
    });
})(jQuery);
