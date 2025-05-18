<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
    
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $path2root ?>index.php">
      <img src="<?php echo $path2root; ?>images/logo.png" alt="Logo" width="32" height="32" style="transform: scale(1.8);" class="d-inline-block">
      <span class="fw-bold fs-4 text-dark">Olibrary</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-2">
        <li class="nav-item">
          <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/about.php">About</a>
        </li>

        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/account.php">Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/uploaditem.php">Add item +</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/logout.php">Logout</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/cart.php">Carrello</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-dark" href="<?php echo $path2root ?>pages/login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
    
  </div>
</nav>

<style>
  .navbar-nav .nav-link {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  .navbar-nav .nav-link:hover {
    background-color: #FFB22C;
    color: #fff !important;
  }

  .navbar-brand img {
    transition: transform 0.3s ease;
  }

  .navbar-brand:hover img {
    transform: scale(2);
  }

  .navbar {
    font-family: 'Segoe UI', sans-serif;
  }
</style>
