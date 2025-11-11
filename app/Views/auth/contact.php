<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Chakanoks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body { padding: 2rem; max-width: 800px; margin: 0 auto; }
    .form-container { 
      margin-top: 2rem; 
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .btn-primary { 
      background-color: #ff8c00; 
      border: none; 
      padding: 10px 25px;
      font-weight: 500;
    }
    .btn-primary:hover { 
      background-color: #e67e00; 
    }
    .form-label {
      font-weight: 500;
    }
    .alert {
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>
  <div class="text-center mb-5">
    <h2 class="mb-3">Contact Us</h2>
    <p class="text-muted">Have questions or feedback? We'd love to hear from you. Fill out the form below and we'll get back to you as soon as possible.</p>
  </div>
  
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  
  <?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <h5>Please fix the following errors:</h5>
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= $error ?></li>
        <?php endforeach; ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="form-container">
    <form action="<?= site_url('contact/send') ?>" method="POST">
      <?= csrf_field() ?>
      
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
      </div>
      
      <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" required>
      </div>
      
      <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
      </div>
      
      <div class="d-grid gap-2 d-md-flex justify-content-between mt-4">
        <a href="<?= site_url('login') ?>" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Back to Login
        </a>
        <button type="submit" class="btn btn-primary px-4">
          <i class="bi bi-send"></i> Send Message
        </button>
      </div>
    </form>
  </div>
</body>
</html>
