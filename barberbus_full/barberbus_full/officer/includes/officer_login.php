<div id="loginScreen" style="display:flex;position:fixed;inset:0;background:var(--bg);align-items:center;justify-content:center;z-index:999;padding:1rem;">
  <div style="width:100%;max-width:380px;">
    <div style="text-align:center;margin-bottom:2rem;">
      <div style="display:inline-flex;align-items:center;gap:0.7rem;margin-bottom:0.5rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--gold);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1.4rem;color:#000;">B</div>
        <div style="font-family:var(--font-display);font-size:1.8rem;letter-spacing:0.06em;">BARBERBUS</div>
      </div>
      <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.18em;color:var(--gold);background:rgba(201,168,76,0.1);display:inline-block;padding:0.25rem 0.9rem;border-radius:20px;border:1px solid var(--border2);">Officer Portal</div>
    </div>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:2rem;">
      <div style="margin-bottom:1.5rem;">
        <div style="font-family:var(--font-serif);font-size:1.4rem;color:var(--white);margin-bottom:0.2rem;">Welcome back</div>
        <div style="font-size:0.8rem;color:var(--muted);">Sign in to access the officer dashboard</div>
      </div>

      <form id="loginForm" style="display:flex;flex-direction:column;gap:0.9rem;">
        <div class="fg">
          <label>Email Address</label>
          <input type="email" id="loginEmail" placeholder="officer@barberbus.com" autocomplete="email" required/>
        </div>
        <div class="fg">
          <label>Password</label>
          <input type="password" id="loginPass" placeholder="••••••••" autocomplete="current-password" required/>
        </div>

        <div id="loginError" style="display:none;background:var(--red-bg);border:1px solid rgba(224,82,82,0.3);border-radius:7px;padding:0.6rem 0.8rem;font-size:0.8rem;color:var(--red);"></div>

        <button type="submit" id="loginBtn" class="btn btn-gold btn-full" style="margin-top:0.3rem;padding:0.7rem;">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
      </form>

      <div style="margin-top:1.2rem;padding-top:1.2rem;border-top:1px solid var(--border);font-size:0.75rem;color:var(--muted);text-align:center;">
        Default: <code style="color:var(--gold)">admin@barberbus.com</code> / <code style="color:var(--gold)">password</code>
      </div>
    </div>
  </div>
</div>
