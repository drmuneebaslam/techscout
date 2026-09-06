<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding: 3rem 1.5rem; max-width: 840px; min-height: calc(100vh - 180px);">
  
  <!-- Onboarding Progress Bar & Stepper -->
  <div style="text-align: center; margin-bottom: 2.5rem;">
    <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; letter-spacing: 1px; margin-bottom: 0.5rem; text-transform: uppercase;">
      BUILDER ONBOARDING &bull; CUSTOMIZE YOUR STREAM
    </span>
    <h1 style="font-size: 2.2rem; color: #f8fafc; margin: 0.25rem 0 0.5rem 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
      Welcome to TechScout, <?= htmlspecialchars($user['display_name'] ?? $user['username']) ?>! 🚀
    </h1>
    <p style="color: #94a3b8; font-size: 0.95rem; margin: 0;">Let's tailor your engineering radar, follow creators, and initialize your discovery stream.</p>
    
    <!-- Progress Indicator -->
    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem;">
      <div id="step_dot_1" class="step-indicator active">1. Topics &amp; Stacks</div>
      <div id="step_dot_2" class="step-indicator">2. Builder Role</div>
      <div id="step_dot_3" class="step-indicator">3. Follow Creators</div>
      <div id="step_dot_4" class="step-indicator">4. Finalize</div>
    </div>
  </div>

  <form id="onboardingMasterForm" action="<?= url('/onboarding/complete') ?>" method="POST">
    
    <!-- STEP 1: TOPICS & STACKS -->
    <div class="onboard-step" id="step_1">
      <div class="ts-card" style="background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; color: #f8fafc; margin-top: 0; margin-bottom: 0.5rem;">What engineering domains do you build in?</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Select topics to calibrate your personalized feed and weekly digests.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.75rem;" id="interestsGrid">
          <?php
          $interests = [
            ['ai_llm', '🤖 AI & LLMs', '#38bdf8'],
            ['cloud_k8s', '☁️ Cloud & K8s', '#818cf8'],
            ['system_arch', '🏛️ High-Scale Systems', '#c084fc'],
            ['security', '🛡️ Zero-Trust Security', '#f43f5e'],
            ['frontend', '⚡ Modern UI & Next.js', '#10b981'],
            ['prompts', '📝 Prompt Engineering', '#f59e0b'],
            ['data_eng', '📊 Data Pipelines', '#06b6d4'],
            ['devops', '🚀 CI/CD & DevOps', '#a855f7'],
            ['indie_saas', '💰 Indie Micro-SaaS', '#10b981'],
            ['mobile', '📱 Mobile Engineering', '#38bdf8'],
            ['web3', '🔗 Decentralized Systems', '#ec4899'],
            ['robotics', '🦾 Hardware & Embedded', '#f97316'],
          ];
          ?>
          <?php foreach ($interests as $item): ?>
            <label class="interest-chip" style="cursor: pointer; display: flex; align-items: center; gap: 0.6rem; padding: 0.85rem 1rem; border-radius: 10px; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); transition: all 0.2s ease;">
              <input type="checkbox" name="interests[]" value="<?= $item[0] ?>" checked style="accent-color: var(--ts-primary); width: 16px; height: 16px;">
              <span style="font-weight: 600; color: #f8fafc; font-size: 0.85rem;"><?= $item[1] ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
          <button type="button" onclick="goToStep(2)" class="ts-btn ts-btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 700;">
            Continue to Role Setup &rarr;
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 2: BUILDER ROLE -->
    <div class="onboard-step" id="step_2" style="display: none;">
      <div class="ts-card" style="background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; color: #f8fafc; margin-top: 0; margin-bottom: 0.5rem;">What describes your primary role?</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Helps authors and tool creators understand their peer audience.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
          <label style="cursor: pointer; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; justify-content: space-between;">
              <span style="font-size: 1.5rem;">💻</span>
              <input type="radio" name="builder_role" value="software_engineer" checked style="accent-color: #38bdf8;">
            </div>
            <strong style="color: #f8fafc; font-size: 0.95rem;">Software Engineer / Architect</strong>
            <p style="color: #94a3b8; font-size: 0.78rem; margin: 0;">Building production scale web apps, backend APIs, and distributed systems.</p>
          </label>

          <label style="cursor: pointer; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; justify-content: space-between;">
              <span style="font-size: 1.5rem;">✍️</span>
              <input type="radio" name="builder_role" value="technical_author" style="accent-color: #38bdf8;">
            </div>
            <strong style="color: #f8fafc; font-size: 0.95rem;">Technical Author &amp; Creator</strong>
            <p style="color: #94a3b8; font-size: 0.78rem; margin: 0;">Publishing deep-dive engineering tutorials, benchmarking tools, and prompts.</p>
          </label>

          <label style="cursor: pointer; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; justify-content: space-between;">
              <span style="font-size: 1.5rem;">🚀</span>
              <input type="radio" name="builder_role" value="ai_founder" style="accent-color: #38bdf8;">
            </div>
            <strong style="color: #f8fafc; font-size: 0.95rem;">AI Founder &amp; Indie Hacker</strong>
            <p style="color: #94a3b8; font-size: 0.78rem; margin: 0;">Showcasing autonomous tools, GPT integrations, and developer utilities.</p>
          </label>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
          <button type="button" onclick="goToStep(1)" class="ts-btn ts-btn-outline" style="padding: 0.65rem 1.25rem;">
            &larr; Back
          </button>
          <button type="button" onclick="goToStep(3)" class="ts-btn ts-btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 700;">
            Continue to Creator Follows &rarr;
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 3: FOLLOW CREATORS -->
    <div class="onboard-step" id="step_3" style="display: none;">
      <div class="ts-card" style="background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; color: #f8fafc; margin-top: 0; margin-bottom: 0.5rem;">Top TechScout Verified Creators</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Follow leading software engineers and AI researchers to see their articles first.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
          <?php
          $suggestedAuthors = [
            ['Sarah Connor', 'AI Systems Architect @ NeuralMesh', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120', 1],
            ['Alex Mercer', 'Principal Distributed Engineer', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120', 2],
            ['Elena Rostova', 'Prompt Engineering Lead', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=120', 3],
          ];
          ?>
          <?php foreach ($suggestedAuthors as $a): ?>
            <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.25rem; text-align: center;">
              <img src="<?= $a[2] ?>" alt="<?= $a[0] ?>" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; margin-bottom: 0.5rem; border: 2px solid var(--ts-primary);">
              <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;"><?= $a[0] ?></div>
              <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.75rem;"><?= $a[1] ?></div>
              <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; background: rgba(56,189,248,0.1); color: #38bdf8; padding: 0.3rem 0.75rem; border-radius: 6px; border: 1px solid rgba(56,189,248,0.3);">
                <input type="checkbox" name="follow_authors[]" value="<?= $a[3] ?>" checked style="accent-color: #38bdf8;">
                Following ✓
              </label>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
          <button type="button" onclick="goToStep(2)" class="ts-btn ts-btn-outline" style="padding: 0.65rem 1.25rem;">
            &larr; Back
          </button>
          <button type="button" onclick="goToStep(4)" class="ts-btn ts-btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 700;">
            Continue to Profile Polish &rarr;
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 4: FINALIZE -->
    <div class="onboard-step" id="step_4" style="display: none;">
      <div class="ts-card" style="background: rgba(13,21,39,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; color: #f8fafc; margin-top: 0; margin-bottom: 0.5rem;">Profile &amp; Notification Preferences</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Set your headline and fine-tune your platform dispatch frequencies.</p>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Professional Headline (e.g. Senior Backend Engineer @ Scale)</label>
          <input type="text" name="headline" placeholder="Architecting high-concurrency systems & AI workflows" value="<?= htmlspecialchars($user['headline'] ?? '') ?>" class="admin-modal-input">
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; background: rgba(15,23,42,0.8); padding: 0.85rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <input type="checkbox" name="newsletter_optin" value="1" checked style="accent-color: #10b981; width: 18px; height: 18px;">
            <div>
              <strong style="color: #f8fafc; font-size: 0.88rem;">Subscribe to TechScout Weekly Architecture Dispatch</strong>
              <div style="font-size: 0.75rem; color: #64748b;">Curated breakdown of the top 5 AI tools, architecture patterns &amp; code blueprints every Tuesday.</div>
            </div>
          </label>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
          <button type="button" onclick="goToStep(3)" class="ts-btn ts-btn-outline" style="padding: 0.65rem 1.25rem;">
            &larr; Back
          </button>
          <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.75rem 2rem; font-weight: 700; background: linear-gradient(135deg, #10b981, #38bdf8); font-size: 1rem;">
            Launch My Discovery Hub 🚀
          </button>
        </div>
      </div>
    </div>

  </form>
</div>

<style>
.step-indicator {
  font-size: 0.78rem;
  font-weight: 700;
  color: #64748b;
  padding: 0.35rem 0.85rem;
  border-radius: 20px;
  background: rgba(15,23,42,0.6);
  border: 1px solid rgba(148,163,184,0.15);
  transition: all 0.2s ease;
}
.step-indicator.active {
  color: #38bdf8;
  border-color: rgba(56,189,248,0.4);
  background: rgba(56,189,248,0.1);
}
</style>

<script>
function goToStep(stepNum) {
  document.querySelectorAll('.onboard-step').forEach(s => s.style.display = 'none');
  const target = document.getElementById('step_' + stepNum);
  if (target) target.style.display = 'block';

  document.querySelectorAll('.step-indicator').forEach((dot, idx) => {
    if (idx + 1 === stepNum) {
      dot.classList.add('active');
    } else {
      dot.classList.remove('active');
    }
  });

  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
