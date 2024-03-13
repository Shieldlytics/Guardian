
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>password hash examle</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/fontawesome-pro-5.15.4/css/all.css">
        <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="card mt-3" style="max-width: 18rem;">
                        <div class="card-body">
                            <div class="card-title h3">Test Login</div>
                                <div class="form-group mt-2">
                                    <label for="email" class="form-label text-black">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                                 
                                </div>
                                <div class="form-group mt-2">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="form-group mt-3">
                                    <button id="login" type="submit" class="btn btn-primary">Register</button>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <?php
                    //on login button click
                    if(isset($_POST['username']) && isset($_POST['password'])){
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        echo $username;
                        echo "<br>";
                        echo $password;
                        echo "<br>";
                        $salt = bin2hex(random_bytes(16));
                        echo $salt;
                        echo "<br>";
                        $hashedPassword = password_hash($password.$salt, PASSWORD_DEFAULT);
                        echo $hashedPassword;
                        echo "<br>";
                        $verify = password_verify($password.$salt,$hashedPassword);
                        echo $verify;
                        echo "<br>";
                        if(password_verify($password.$salt, $hashedPassword)){
                            echo "Password is correct";
                        }else{
                            echo "Password is incorrect";
                        };
                    }
                    ?>
                </div>
            </div>
        </div>

        
        
       
        <script src="node_modules/jquery/dist/jquery.js"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
        <script src="node_modules/aos/dist/aos.js"></script>
    </body>
</html>
<script>
    $(document).ready(function(){

        $('#login').click(function(){
            console.log('clicked')
            var username = $('#username').val();
            var password = $('#password').val();

            $.ajax({
                url: 'assets/PHP/functions.php',
                dataType: 'json',
                type: 'POST',
                data: {
                    method: 'registerUser',
                    username: username,
                    password: password
                },
                success: function(response){
                    console.log(response);
                }
            });
        });

    });
</script>