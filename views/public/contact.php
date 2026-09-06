<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem; max-width: 720px;">
  <div class="ts-card ts-contact-card" style="padding: 2.5rem;">
    <h1 style="font-size: 2.25rem; margin-bottom: 0.5rem;">Contact TechScout</h1>
    <p style="color: var(--ts-text-secondary); margin-bottom: 2rem;">Inquiries regarding editorial submissions, advertising, or technical partnerships.</p>

    <form action="<?= url('/contact') ?>" method="POST">
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">Your Name <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 70 chars)</span></label>
        <input type="text" name="name" maxlength="70" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary);" required>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">Work Email</label>
        <input type="email" name="email" maxlength="100" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary);" required>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">Subject <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 150 chars)</span></label>
        <input type="text" name="subject" maxlength="150" placeholder="Partnership, Ad Placement, or Editorial Inquiry" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary);" required>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.4rem;">Message / Inquiry <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 2,000 chars)</span></label>
        <textarea name="message" maxlength="2000" rows="5" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary); resize: vertical;" required></textarea>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; font-weight: 700; padding: 0.85rem;">
        <i data-lucide="send"></i> Dispatch Message to Editorial Team 🚀
      </button>
    </form>
  </div>
</div>

<style>
@media (max-width: 768px) {
  .ts-contact-card {
    padding: 1.15rem !important;
  }
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
