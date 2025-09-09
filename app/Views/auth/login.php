<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chakanoks Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .login-container {
      height: 100vh;
    }
    .login-left {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 60px;
      background-color: #ffffff;
    }
    .login-right {
      background: orange;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-right img {
      max-width: 60%;
      height: auto;
    }
	  .login-right img {
    max-width: 300px;
    height: auto;
    border-radius: 60px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<div class="container-fluid login-container">
  <div class="row h-100">
    <!-- left -->
    <div class="col-md-6 login-left">
      <h2 class="mb-4">Welcome Back</h2>
      <!-- It will go to loginAttempt -->
      <form action= "<?= base_url('login') ?>" method="POST"> 
        <div class="mb-3">
          <label for="emp_ID" class="form-label">Employee ID</label>
          <input type="number" class="form-control" id="emp_ID" name="id" placeholder="Enter your ID" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="mt-3">Cant Login? <a href="#">Contact Us</a></p>
      </form>
    </div>
    
    <!-- right -->
    <div class="col-md-6 login-right d-flex flex-column align-items-center justify-content-center">
      <img src="../public/images/2.jpg" alt="Logo">
      <h1 class="mt-3  text-white">Chakanoks</h1>
      <h5 class="mt-1  text-white">Masarap Kahit Walang Laman</h5>
    </div>

  </div>
</div>

</body>
</html>
