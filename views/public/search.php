<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 800px; margin: 0 auto 3rem auto; text-align: center;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Platform Search</h1>
    
    <form action="<?= url('/search') ?>" method="GET" style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
      <input type="text" name="q" value="<?= htmlspecialchars($query ?? '') ?>" placeholder="Search articles, AI tools, prompt blueprints, GPTs..." style="flex: 1; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: var(--ts-radius-md); padding: 0.85rem 1.25rem; color: var(--ts-text-primary); font-size: 1rem; outline: none;" required>
      <button type="submit" class="ts-btn ts-btn-primary">
        <i data-lucide="search"></i> Search
      </button>
    </form>

    <?php if ($query): ?>
      <p style="color: var(--ts-text-tertiary); font-size: 0.95rem;">
        Showing search results for "<strong style="color: var(--ts-text-primary);"><?= htmlspecialchars($query) ?></strong>"
      </p>
    <?php endif; ?>
  </div>

  <?php if (!empty($results)): ?>
    <div class="ts-grid-3">
      <?php foreach ($results as $item): ?>
        <?php
          $itemPrefix = 'article';
          if ($item['content_type'] === 'ai_tool') $itemPrefix = 'tools';
          elseif ($item['content_type'] === 'prompt') $itemPrefix = 'prompts';
          elseif ($item['content_type'] === 'gpt_listing') $itemPrefix = 'gpts';
          elseif ($item['content_type'] === 'campaign') $itemPrefix = 'campaigns';
        ?>
        <article class="ts-card">
          <span class="ts-badge ts-badge-cyan" style="margin-bottom: 0.75rem;">
            <?= strtoupper(htmlspecialchars(str_replace('_', ' ', $item['content_type']))) ?>
          </span>
          <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/' . $itemPrefix . '/' . $item['slug']) ?>">
              <?= htmlspecialchars($item['title']) ?>
            </a>
          </h3>
          <p style="font-size: 0.875rem; color: var(--ts-text-secondary); line-height: 1.5;">
            <?= htmlspecialchars($item['summary']) ?>
          </p>
        </article>
      <?php endforeach; ?>
    </div>
  <?php elseif ($query): ?>
    <div class="ts-card" style="text-align: center; padding: 3rem; max-width: 600px; margin: 0 auto;">
      <i data-lucide="search-x" style="width: 48px; height: 48px; color: var(--ts-text-tertiary); margin-bottom: 1rem;"></i>
      <h3>No results found for "<?= htmlspecialchars($query) ?>"</h3>
      <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Try searching for keywords like "RAG", "MySQL", "PHP", "Kubernetes", or "Prompts".</p>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
