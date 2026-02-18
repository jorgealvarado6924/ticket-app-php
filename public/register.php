<?php session_start(); ?>

<h2>Registro de Usuario</h2>

<form action="register_process.php" method="post">
    <input type="text" name="username" placeholder="Username" required > <br><br>
    <input type="email" name="email" placeholder="Email" required> <br><br>
    <input type="password" name="password" placeholder="Password" required> <br><br>
    <button type="submit"> Enviar </button>
</form>


<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}


?>