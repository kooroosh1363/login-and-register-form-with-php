<?php
include("./partials/header.php")

?>



<div class="container_form">
    <div class="overlay">
        <!-- nothing content -->
    </div>
    <div class="divTitle">
        <h1 class="title">LOGIN</h1>
        <span class="subTitle">WELCOME BACK</span>
    </div>

    <!-- section form -->
    <form action="" method="POST">
        <div class="row grid">
            <!-- username -->
            <div class="row">
                <label for="username">User Name</label>
                <input type="text" id="username" name="username" placeholder="Enter User Name" required>
            </div>
            <!-- password -->
            <div class="row">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
            </div>
            <!-- submit -->
            <div class="row">
                <input type="submit" id="sub_btn" name="submit" value="LOGIN">

                <span class="linkRegister">Don't Have An Account?<a href="./register.php">Register</a></span>
            </div>
        </div>
    </form>
</div>


<?php
include("./partials/footer.php")

?>



<!-- login to database -->
<?php 
if(isset($_POST['submit'])){

}

?>