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
});
