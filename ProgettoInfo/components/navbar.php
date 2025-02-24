
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    
    <!-- Logo e brand -->
    <a class="navbar-brand" href="<?php echo $path2root?>index.php">
      <img src="<?php echo "$path2root"; ?>images/logo.png" alt="" width="30" height="24" class="d-inline-block align-text-top" style="transform: scale(2.5)" >
      Olibrary
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $path2root?>pages/about.php">About</a>
        </li>


        <!-- Controllo della sessione -->
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $path2root?>pages/account.php">Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $path2root?>pages/logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $path2root?>pages/register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $path2root?>pages/login.php">Login</a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
    
  </div>
</nav>
