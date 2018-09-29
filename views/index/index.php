<?php include 'views/header.php'; ?>
<nav class="orange">
    <div class="nav-wrapper container">
        <a href="#" class="brand-logo left">To Do List</a>
        <ul class="right hide-on-med-and-down">
            <li><a href="#" >Contact</a></li>
            <li><a href="#" >About</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <div>
        <?php if(isset($data['msg'])): ?>
        <h3><?php echo $data['msg'] ?></h3>
        <?php endif; ?>
    </div>
    
    <div class="row">
        <div class="col s12 l5">
            <h3><i class="material-icons medium">create</i> Register</h3>
            <form method="post" action="/todolist/index/register">
                <div class="input-field">
                    <input type="text" name="name"/>
                    <label for="name">Name: </label>
                </div>
                <div class="input-field">
                    <input type="text" name="login"/>
                    <label for="login">Login: </label>
                </div>
                <div class="input-field">
                    <input type="password" name="password"/>
                    <label for="password">Password: </label>
                </div>    
                <div class="input-field">
                    <input type="password" name="secpassword" />
                    <label for="secpassword">Confirm password: </label>
                </div>
                <div class="input-field">
                    <input type="email" name="email" />
                    <label for="email">E-mail:</label>
                </div>        
                <input class="btn green waves-effect waves-light white-text right" type="submit" value="Register" />
            </form>
        </div>
        
        <div class="col s12 l6 offset-l1">
            <h3><i class="material-icons medium">account_circle</i> Sign in</h3>
            <form method="post" action="/todolist/index/signin">
                <div class="input-field">
                    <input type="text" name="login"/>
                    <label>Login: </label>
                </div>
                <div class="input-field"> 
                    <input type="password" name="password" />
                    <label>Password: </label>
                </div>
                <input class="btn green waves-effect waves-light right" type="submit" value="SignIn" />
            </form>
        </div>
        
    </div>
    
    <div class="row">
        <div class="col s12">
            <br/><br/><br/><br/><br/><br/>
        </div>
    </div>
    
</div>
<?php include 'views/footer.php'; ?>