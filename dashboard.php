<?php

include('./partials/header.php');

?>

<div class="dashboard">
    <span>
        <?php 
        if (isset($_SESSION['loginMessage'])) {
            echo $_SESSION['loginMessage'];
            unset($_SESSION['loginMessage']);
        }
        
        ?>

    </span>
    <h1>DASHBOARD</h1>
    <div class="btnLogOut">
        <a href="logout.php">Log out</a>
    </div>
</div>


<?php

include('./partials/footer.php');

?>