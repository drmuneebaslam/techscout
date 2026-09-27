// ============================================================================
// TECHSCOUT CONTENT ENGINE — REACTIONS, COMMENTS, PROMPT INJECTOR
// ============================================================================

document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = window.TS_BASE_URL || '';

  // 1. Single Reaction Per User Toggle / Switch Handler
  const reactionBtns = document.querySelectorAll('.reaction-btn');
  reactionBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
      const contentId = btn.getAttribute('data-content-id');
      const reactionType = btn.getAttribute('data-reaction');
      if (!contentId) return;

      try {
        const res = await fetch(`${baseUrl}/api/v1/content/${contentId}/react`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ reaction_type: reactionType })
        });
        const data = await res.json();
        if (data.error) {
          if (typeof tsModal !== 'undefined') {
            tsModal.alert(data.error, 'Authentication Required');
          } else {
            showToast(data.error, 'warning');
          }
          return;
        }

        const groupBtns = document.querySelectorAll(`.reaction-btn[data-content-id="${contentId}"]`);

        if (data.action === 'removed') {
          btn.classList.remove('active');
          const countSpan = btn.querySelector('.reaction-count');
          if (countSpan) {
            let c = parseInt(countSpan.textContent) || 0;
            countSpan.textContent = Math.max(0, c - 1);
          }
          showToast('Reaction removed', 'info');
        } else if (data.action === 'switched') {
          groupBtns.forEach(b => {
            if (b.getAttribute('data-reaction') === data.previous_reaction) {
              b.classList.remove('active');
              const prevSpan = b.querySelector('.reaction-count');
              if (prevSpan) {
                let c = parseInt(prevSpan.textContent) || 0;
                prevSpan.textContent = Math.max(0, c - 1);
              }
            }
          });
          btn.classList.add('active');
          const countSpan = btn.querySelector('.reaction-count');
          if (countSpan) {
            let c = parseInt(countSpan.textContent) || 0;
            countSpan.textContent = c + 1;
          }
          showToast(`Switched reaction to ${reactionType}! ⚡`, 'success');
        } else if (data.action === 'added') {
          groupBtns.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          const countSpan = btn.querySelector('.reaction-count');
          if (countSpan) {
            let c = parseInt(countSpan.textContent) || 0;
            countSpan.textContent = c + 1;
          }
          showToast(`Reacted with ${reactionType}! 🚀`, 'success');
        }

        const totalDisplay = document.querySelector(`.total-reaction-badge[data-content-id="${contentId}"]`);
        if (totalDisplay) {
          totalDisplay.textContent = `${data.total_reactions} reactions`;
        }
      } catch (err) {
        showToast('Error processing reaction', 'error');
      }
    });
  });

  // 2. Interactive Prompt Variable Injector
  const promptTemplateEl = document.getElementById('raw-prompt-template');
  const renderedPromptEl = document.getElementById('rendered-prompt-output');
  const variableInputs = document.querySelectorAll('.prompt-var-input');

  function updateRenderedPrompt() {
    if (!promptTemplateEl || !renderedPromptEl) return;
    let template = promptTemplateEl.value;
    variableInputs.forEach(input => {
      const varName = input.getAttribute('data-var-name');
      const val = input.value.trim() || `[${varName}]`;
      const regex = new RegExp(`{{\\s*${varName}\\s*}}`, 'gi');
      template = template.replace(regex, val);
    });
    renderedPromptEl.textContent = template;
    if (typeof Prism !== 'undefined') Prism.highlightElement(renderedPromptEl);
  }

  variableInputs.forEach(input => input.addEventListener('input', updateRenderedPrompt));
  updateRenderedPrompt();

  // 3. One-Click Copy Prompt Button
  const copyPromptBtn = document.getElementById('btn-copy-prompt');
  if (copyPromptBtn && renderedPromptEl) {
    copyPromptBtn.addEventListener('click', async () => {
      const textToCopy = renderedPromptEl.textContent;
      const promptId = copyPromptBtn.getAttribute('data-prompt-id');

      try {
        await navigator.clipboard.writeText(textToCopy);
        showToast('Prompt copied to clipboard! 🚀', 'success');

        if (promptId) {
          fetch(`${baseUrl}/api/v1/prompts/${promptId}/copy`, { method: 'POST' });
        }
      } catch (err) {
        showToast('Failed to copy to clipboard', 'error');
      }
    });
  }

  // 4. Comment Posting Form
  const commentForm = document.getElementById('content-comment-form');
  if (commentForm) {
    commentForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const contentId = commentForm.getAttribute('data-content-id');
      const textarea = commentForm.querySelector('textarea[name="comment_text"]');
      const text = textarea ? textarea.value.trim() : '';

      if (text.length < 3) {
        showToast('Please enter a comment (at least 3 characters).', 'warning');
        return;
      }

      try {
        const res = await fetch(`${baseUrl}/api/v1/content/${contentId}/comments`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ comment_text: text })
        });
        const data = await res.json();
        if (data.error) {
          showToast(data.error, 'warning');
          return;
        }

        showToast('Comment posted successfully!', 'success');
        textarea.value = '';
        setTimeout(() => location.reload(), 800);
      } catch (err) {
        showToast('Error submitting comment', 'error');
      }
    });
  }

  // 5. [TS-AUT-70] Global Code Snippet Copy Enhancer
  document.querySelectorAll('pre code, .article-body-content pre').forEach(target => {
    const pre = target.tagName === 'PRE' ? target : target.closest('pre');
    if (!pre || pre.querySelector('.ts-code-copy-btn') || pre.closest('#prompt-preview-container') || pre.closest('#pane-blueprint') || pre.classList.contains('no-copy-inject') || pre.closest('.no-copy-inject') || document.getElementById('btn-copy-prompt')) return;
    pre.style.position = 'relative';
    const codeEl = pre.querySelector('code') || pre;

    const copyBtn = document.createElement('button');
    copyBtn.type = 'button';
    copyBtn.className = 'ts-code-copy-btn';
    copyBtn.innerHTML = '📋 Copy';
    copyBtn.setAttribute('title', 'Copy code snippet');
    copyBtn.style.cssText = 'position: absolute; top: 8px; right: 8px; font-size: 0.72rem; padding: 4px 10px; background: rgba(15,23,42,0.85); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); border-radius: 6px; cursor: pointer; backdrop-filter: blur(6px); transition: all 0.2s ease; z-index: 10; font-family: monospace;';

    copyBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const rawText = codeEl.innerText || codeEl.textContent || '';
      try {
        await navigator.clipboard.writeText(rawText);
        copyBtn.innerHTML = '✅ Copied!';
        copyBtn.style.color = '#34d399';
        copyBtn.style.borderColor = '#34d399';
        setTimeout(() => {
          copyBtn.innerHTML = '📋 Copy';
          copyBtn.style.color = '#94a3b8';
          copyBtn.style.borderColor = 'rgba(148,163,184,0.3)';
        }, 2000);

        const contentId = document.querySelector('[data-content-id]')?.getAttribute('data-content-id');
        if (contentId) {
          const langMatch = (codeEl.className || '').match(/language-([a-zA-Z0-9_-]+)/);
          const lang = langMatch ? langMatch[1] : 'code';
          fetch(`${baseUrl}/api/v1/telemetry/code-copy`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              content_id: contentId,
              language: lang,
              snippet_length: rawText.length,
              code_preview: rawText.substring(0, 100)
            })
          }).catch(() => {});
        }
      } catch (err) {}
    });
    pre.appendChild(copyBtn);
  });
});
