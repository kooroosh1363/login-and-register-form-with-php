<?php
include("./partials/header.php")

?>

<?php 

if (isset($_SESSION['createdAccount'])) {
    echo $_SESSION['createdAccount'];
    unset($_SESSION['createdAccount']);
}

?>

<div class="container_form">
    <div class="overlay">
        <!-- nothing content -->
    </div>
    <div class="divTitle">
        <h1 class="title">LOGIN</h1>
        <span class="subTitle">WELCOME BACK</span>
    </div>
    <?php

    if (isset($_SESSION['notAdmin'])) {
        echo $_SESSION['notAdmin'];
        unset($_SESSION['notAdmin']);
    }

    ?>

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
if (isset($_POST['submit'])) {
    // echo "everything is ok";
    // create var for store
    $username = $_POST['username'];
    $password = $_POST['password'];


    // write sql codes for details in db
    $sql = "SELECT * from admin WHERE username='$username' AND password='$password'";
    // query code for execute
    $result = mysqli_query($conn, $sql);

    // same count number of account in db
    $count = mysqli_num_rows($result);
    // everything is results in array
    $row = mysqli_fetch_assoc($result);

    // if for checking last account in db 
    if ($count == 1) {
        // sent a message for new account
        $_SESSION['loginMessage'] = '<span class="succ">welcome' . $username . '</span>';
        header('location:' . SITEURL . 'dashboard.php');
        exit();
    } else {
        // sent a message if is not account in db
        $_SESSION['notAdmin'] = '<span class="fail">welcome' . $username . 'is not registered yet!</span>';
        header('location:' . SITEURL . 'index.php');
        exit();
    }
}

?>