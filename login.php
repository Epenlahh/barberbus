<?php
$pageTitle = "BarberBus – Login";
$currentPage = "login";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<body class="auth-page">

  <div class="auth-container">
    <div class="auth-left">
      <div class="auth-brand">
        <a href="index.php" class="logo-text">BARBER<span class="accent">BUS</span></a>
      </div>
      <div class="auth-tagline">
        <h2>Your Style,<br/><em>Our Craft.</em></h2>
        <p>Sign in to manage your bookings and preferences.</p>
      </div>
      <div class="auth-deco">✂</div>
    </div>

    <div class="auth-right">
      <div class="auth-box">
        <div class="auth-toggle">
          <button class="toggle-btn active" id="loginTab">Login</button>
          <button class="toggle-btn" id="registerTab">Register</button>
        </div>

        <!-- LOGIN FORM -->
        <form class="auth-form" id="loginForm">
          <h3>Welcome Back</h3>
          <p class="form-sub">Enter your credentials to continue</p>
          <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrap">
              <i class="fas fa-envelope"></i>
              <input type="email" placeholder="your@email.com" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
              <i class="fas fa-lock"></i>
              <input type="password" id="loginPass" placeholder="••••••••" required/>
              <i class="fas fa-eye toggle-pass" onclick="togglePass('loginPass', this)"></i>
            </div>
          </div>
          <div class="form-options">
            <label class="checkbox-label">
              <input type="checkbox"/> Remember me
            </label>
            <a href="#" class="forgot-link">Forgot Password?</a>
          </div>
          <button type="submit" class="btn-primary full" onclick="handleLogin(event)">Login</button>
          <div class="auth-divider"><span>or continue with</span></div>
          <div class="social-btns">
            <button class="social-btn"><i class="fab fa-google"></i> Google</button>
            <button class="social-btn"><i class="fab fa-facebook-f"></i> Facebook</button>
          </div>
        </form>

        <!-- REGISTER FORM -->
        <form class="auth-form hidden" id="registerForm">
          <h3>Create Account</h3>
          <p class="form-sub">Join BarberBus today</p>
          <div class="form-group">
            <label>Full Name</label>
            <div class="input-wrap">
              <i class="fas fa-user"></i>
              <input type="text" placeholder="Ahmad bin Ali" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <div class="input-wrap">
              <i class="fas fa-phone"></i>
              <input type="tel" placeholder="+60 12-345 6789" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrap">
              <i class="fas fa-envelope"></i>
              <input type="email" placeholder="your@email.com" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
              <i class="fas fa-lock"></i>
              <input type="password" id="regPass" placeholder="••••••••" required/>
              <i class="fas fa-eye toggle-pass" onclick="togglePass('regPass', this)"></i>
            </div>
          </div>
          <button type="submit" class="btn-primary full" onclick="handleRegister(event)">Create Account</button>
        </form>

      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div class="toast" id="toast"></div>

  <script src="js/main.js"></script>
  <script src="js/auth.js"></script>
</body>
</html>
