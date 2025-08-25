<!-- app/Views/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CHAKANOKS | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #d3ffbf71, #f1c97d73);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-card {
      background: #ffffffff;
      border-radius: 60px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
      padding: 40px 30px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      transition: transform 0.3s;
    }
    .login-card:hover {
      transform: translateY(-5px);
    }
    .login-card img {
      width: 130px;
      height: auto;
      margin-bottom: 1px;
    }
    .login-card h3 {
      margin-bottom: 10px;
      font-weight: bold;
      color: #333;
    }
    .btn-custom {
      background: linear-gradient(135deg, #667eea, #764ba2);
      border: none;
      color: #fff;
      font-weight: bold;
      transition: 0.3s;
    }
    .btn-custom:hover {
      background: linear-gradient(135deg, #764ba2, #667eea);
    }
    .alert {
      text-align: left;
    }
    .forgot-password {
      display: block;
      margin-top: 10px;
      font-size: 0.9rem;
      text-decoration: none;
      color: #667eea;
      transition: 0.3s;
    }
    .forgot-password:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <!-- LOGO -->
    <img src="<?= base_url('images/2.jpg') ?>" alt="CHAKANOKS Logo">
    <h3>CHAKANOKS LOGIN</h3>

    <!-- Flash Message -->
    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="post" action="<?= base_url('login/auth') ?>">
      <div class="mb-3 text-start">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
      </div>
      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
        <!-- Forgot Password link -->
        <a href="<?= base_url('forgot-password') ?>" class="forgot-password">Forgot Password?</a>
      </div>
      <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>
  </div>

</body>
</html>
