<?php

include('./partials/header.php');

?>

<div class="container_form">
    <div class="overlay">
        <!-- nothing content -->
    </div>
    <div class="divTitle">
        <h1 class="title">REGISTER</h1>
    </div>

    <!-- section form -->
    <form action="" method="">
        <div class="row grid">

            <!-- username -->
            <div class="row">
                <label for="username">User Name</label>
                <input type="text" id="username" name="username" placeholder="Enter User Name" required>
            </div>
            <!-- EMAIL -->
            <div class="row">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <!-- PHONE -->
            <div class="row">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter Your Phone Number" required>
            </div>

            <!-- password -->
            <div class="row">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
            </div>

            <!-- submit -->
            <div class="row">
                <input type="submit" id="sub_btn" name="submit" value="LOGIN">

                <span class="linkRegister">Have An Account Already?<a href="./index.php">LOGIN</a></span>
            </div>
        </div>
    </form>
</div>

<?php

include('./partials/footer.php');

?>