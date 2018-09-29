<?php include 'views/header.php'; ?>
<nav class="orange">
    <div class="nav-wrapper container">
        <a href="/" class="brand-logo left">To Do List</a>
        <a href="#" data-target="slide-out" class="sidenav-trigger right"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
            <li><a href="#" >Contact</a></li>
            <li><a href="#" >About</a></li>
            <li><a href="/todolist/index/signout">Sign out</a></li>
        </ul>
    </div>
    <ul id="slide-out" class="sidenav sidenav-fixed hide-on-large-only">
        <li><a href="#" >Contact</a></li>
        <li><a href="#" >About</a></li>
        <li><a href="/todolist/index/signout">Sign out</a></li>
    </ul>
</nav>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h3>Your notes is here</h3>
            <table id="t1">
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col s12 l2 right-align">
            <i class="material-icons large amber-text text-darken-2">edit</i>
        </div>
        <div class="col s12 l9">
            <div class="input-field">
                <input name="title" type="text"/>
                <label for="title">Title</label>
            </div>
            <div class="input-field">
                <textarea col="7" name="note" class="materialize-textarea"></textarea>
                <label for="note">Text:</label>
            </div>
            <div class="attachment">
                <div class="drop-area">
                    <i class="material-icons medium grey-text">add</i>
                </div>
            </div>
            <button style="display: none" id="update" class="btn orange right">
                <i class="material-icons">update</i>
            </button>
            <button id="add" class="btn green right">
                <i class="material-icons">add</i>
            </button>
            <br /><br />
        </div>
        <div class="col s12" id="notebook">
            <ul class="collapsible popout">
            </ul>
        </div>
    </div>
</div>           

<script type="text/javascript" >
    var notes = [];
    var note_id = null;
    var files = new FormData();
    
    $(".drop-area").on('dragenter', function(){
        console.log("a");
        $('.drop-area').addClass("active");
    })
    
    $('.drop-area').on(
        'dragover',
        function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.drop-area').addClass("active");
        }
    )
    
    $('.drop-area').on(
        'dragleave',
        function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.drop-area').removeClass("active");
        }
    )


    $('.drop-area').on(
        'dragenter',
        function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.drop-area').removeClass("active");
        }
    )
     
    $('.drop-area').on(
    'drop',
    function(e){
        if(e.originalEvent.dataTransfer){
            if(e.originalEvent.dataTransfer.files.length) {
                e.preventDefault();
                e.stopPropagation();
                let length = e.originalEvent.dataTransfer.files.length;
                for(var i=0; i<length; i++){
                    let image = URL.createObjectURL(e.originalEvent.dataTransfer.files[i]);
                    $(".attachment").prepend("<img src='" + image + "' />");
                    files.append('files[]', e.originalEvent.dataTransfer.files[i]);
                } 
                console.log(files.getAll('files[]'));
            }   
        }
    }
    );
    
    function expandAllNotes(){
        $('#notebook li').addClass('active');
        $('#notebook .collapsible').collapsible({'accordion': false});
    }
    
    function showAllNotes(){
        $.get('/todolist/index/getjson', function(data, status){
            $("#notebook > ul").empty();
            notes = $.parseJSON(data);
            $.each(notes, function(name, value){
                var output = '';
                output += "<li id=\"#note" + value.id +"\">";
                output += '<div class="collapsible-header grey-text"><i class="material-icons black-text">account_circle</i>'+ value.title + '</div>';
                output += '<div class="collapsible-body">';
                output += '<div class="row">';
                output += '<div class="col s12 l2">';
                output += '<div><span class="grey-text"><i class="inline-icon material-icons">access_time</i> ' + value.hours + ' </span></div></div>';
                output += '<div class="col s12 l8">' + value.note +'</div>';
                output += '<div class="col s12 l2 right-align">';
                output += '<div><span><a class="update" href="' + value.id + '"><i class="material-icons black-text small">edit</i></a><a class="delete" href="' + value.id + '"><i class="material-icons black-text small">delete</i></a></span></div>';
                output += '</div>';
                output +='</div>';
                output += '</div>';
                output += "</li>";
                $("#notebook > ul").append(output);
            })
            expandAllNotes();
        });
    }
    
    function createNote(){
        $("button#add").click(function(e){
            var textarea = $('textarea').val();
            var title = $('input[name="title"]').val();
            var done = $('input:checked').val();
            $.post("/todolist/index/create",
            {
                title: title,
                note: textarea,
                done: done
            }, 
            function(){
                clear();
                showAllNotes();
            });
        });
    }
    
    function clear(){
            $("input[name=title]").val('');
            $("textarea").val('');
            $("textarea").removeAttr("style");
            M.updateTextFields();
            M.textareaAutoResize($('textarea'));
    }
    
    function updateNote(){
        $(document).on('click', '.update', function(e){
            e.preventDefault();
            let id = this.getAttribute("href");
            let result = notes.find(function(e){
                return e.id == id;
            });
            note_id = result.id;
            clear();
            $("input[name=title]").val(result.title);
            $("textarea").val(result.note.replace(/<br[^>][/]?>/g, ''));
            $("button#add").hide();
            $("button#update").show();
            M.updateTextFields();
            M.textareaAutoResize($('textarea'));
        });
        
        $("button#update").click(function(){          
            var title = $('input[name="title"]').val();
            var textarea = $('textarea').val();
            $.post("/todolist/index/update/"+note_id, {
                title: title,
                note: textarea
            }, 
            function(){
                clear();
                $("button#add").show();
                $("button#update").hide();
                showAllNotes();
            });
        });
    }
    
    function deleteNote(){
        $(document).on('click', '.delete', function(e){
            e.preventDefault();
            var el = this;
            $.get("/todolist/index/delete/" + this.getAttribute("href"), function(){
                $(el).closest('li').slideUp("slow", function(){
                    $(el).remove();
                });
            });
            return false;
        });
    }
    
    $(document).ready(function(){
        $('#slide-out').sidenav();
        showAllNotes();
        expandAllNotes();
        createNote();
        updateNote();
        deleteNote(); 
    });
    
</script>
<?php include 'views/footer.php'; ?>