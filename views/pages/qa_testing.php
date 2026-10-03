<?php
$title = 'QA Testing Dashboard | TechScout';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
  <div class="ts-card" style="padding: 2rem;">
    <h1 style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="flask-conical" style="width: 28px; height: 28px; color: #10b981;"></i>
      QA Testing Dashboard
    </h1>
    <p style="color: var(--ts-text-secondary); margin-bottom: 2rem;">Comprehensive quality assurance testing for each and every feature.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
      
      <!-- Login Testing -->
      <div class="ts-card" style="background: var(--ts-bg-surface);">
        <h3 style="margin-bottom: 1rem; color: #38bdf8; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.5rem;">Authentication: Login</h3>
        <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
          <li><a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Login: Standard View</a></li>
          <li><a href="<?= url('/auth/login?test=invalid_email') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Login: Invalid Email Case</a></li>
          <li><a href="<?= url('/auth/login?test=wrong_password') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Login: Wrong Password Case</a></li>
          <li><a href="<?= url('/auth/login?test=empty_fields') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Login: Empty Fields Case</a></li>
          <li><a href="<?= url('/auth/login?test=sql_injection') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Login: SQL Injection Attempt</a></li>
        </ul>
      </div>

      <!-- Registration Testing -->
      <div class="ts-card" style="background: var(--ts-bg-surface);">
        <h3 style="margin-bottom: 1rem; color: #f472b6; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.5rem;">Authentication: Sign Up</h3>
        <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
          <li><a href="<?= url('/auth/register') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Sign Up: Standard View</a></li>
          <li><a href="<?= url('/auth/register?test=existing_email') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Sign Up: Existing Email</a></li>
          <li><a href="<?= url('/auth/register?test=weak_password') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Sign Up: Weak Password</a></li>
          <li><a href="<?= url('/auth/register?test=mismatch_password') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Sign Up: Password Mismatch</a></li>
          <li><a href="<?= url('/auth/register?test=invalid_username') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Sign Up: Invalid Username</a></li>
        </ul>
      </div>

      <!-- Role / Authorization Testing -->
      <div class="ts-card" style="background: var(--ts-bg-surface);">
        <h3 style="margin-bottom: 1rem; color: #fbbf24; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.5rem;">Authorization & Roles</h3>
        <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
          <li><a href="<?= url('/?qa_auth=member') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Simulate: Standard Member</a></li>
          <li><a href="<?= url('/?qa_auth=author') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Simulate: Verified Author</a></li>
          <li><a href="<?= url('/?qa_auth=admin') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Simulate: Administrator</a></li>
          <li><a href="<?= url('/?qa_auth=impersonate') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Test: Admin Impersonation Mode</a></li>
        </ul>
      </div>

      <!-- Maintenance Mode Testing -->
      <div class="ts-card" style="background: var(--ts-bg-surface);">
        <h3 style="margin-bottom: 1rem; color: #ef4444; border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 0.5rem;">System States</h3>
        <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
          <li><a href="<?= url('/?preview_503=1') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;" target="_blank">View: 503 Maintenance Screen</a></li>
          <li><a href="<?= url('/?simulate_guest=1') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Simulate: Unauthenticated Guest</a></li>
          <li><a href="<?= url('/?test_popup=1') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Trigger: System Popup Modal</a></li>
          <li><a href="<?= url('/?reset_popup=1') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: flex-start;">Reset: Popup Display Frequency</a></li>
        </ul>
      </div>

    </div>
  </div>
</div>

<script>
  // Add test parameters handlers
  document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('test')) {
        showToast('Running test scenario: ' + urlParams.get('test'), 'info');
    }
  });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
