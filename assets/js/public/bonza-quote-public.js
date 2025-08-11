/**
 * Configuration object for BonzaQuoteFlash messages and texts.
 * Customize these strings for different languages or messages.
 */
window.BonzaQuoteConfig = window.BonzaQuoteConfig || {
  messages: {
    submitted: 'Thank you! Your quote has been submitted.',
    submitting: 'Submitting...',
    submit: 'Submit Quote',
    error: 'An error occurred. Please try again.'
  }
};

/**
 * Displays a flash message on the screen with accessible ARIA attributes.
 * The message fades in, stays visible for a short time, then fades out and removes itself.
 * 
 * @param {string} message - The message text to display.
 * @param {string} [type='success'] - Type of message ('success', 'error', etc.) affecting styles.
 */
window.BonzaQuoteFlash = function (message, type = 'success') {
  const msg = document.createElement('div');
  msg.className = `bonza-quote-flash ${type}`;
  msg.textContent = message;

  // Accessibility attributes
  msg.setAttribute('role', 'alert');         // Inform screen readers this is an important message
  msg.setAttribute('aria-live', 'assertive'); // Ensure immediate announcement
  msg.setAttribute('aria-atomic', 'true');    // Announce entire text, not just changes

  // Initial styles for animation
  msg.style.opacity = '0';
  msg.style.transform = 'translateY(-20px)';
  msg.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

  document.body.appendChild(msg);

  // Trigger fade-in animation on next frame
  requestAnimationFrame(() => {
    msg.style.opacity = '1';
    msg.style.transform = 'translateY(0)';
  });

  // Fade out and remove after 4 seconds
  setTimeout(() => {
    msg.style.opacity = '0';
    msg.style.transform = 'translateY(-20px)';
    setTimeout(() => msg.remove(), 500);
  }, 4000);
};

// Wait for DOM to load before attaching event listeners
document.addEventListener('DOMContentLoaded', () => {
  // Check URL params to auto-show submission success flash message
  const url = new URL(window.location.href);
  if (url.searchParams.get('bq_submitted') === '1') {
    BonzaQuoteFlash(window.BonzaQuoteConfig.messages.submitted);
    url.searchParams.delete('bq_submitted');
    window.history.replaceState({}, '', url);
  }

  const form = document.getElementById('bonza-quote-form');
  if (!form) return; // Bail if form is not present

  const submitBtn = form.querySelector('.bq-submit-btn');
  const btnText = submitBtn.querySelector('.bq-btn-text');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.classList.add('loading');
    btnText.textContent = window.BonzaQuoteConfig.messages.submitting;

    const formData = new FormData(form);

    try {
      const response = await fetch(bq_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'bq_submit_quote',
          ...Object.fromEntries(formData)
        })
      });

      const data = await response.json();

      submitBtn.classList.remove('loading');
      submitBtn.disabled = false;
      btnText.textContent = window.BonzaQuoteConfig.messages.submit;

      if (data.success) {
        BonzaQuoteFlash(data.data.message, 'success');
        form.reset();
      } else {
        BonzaQuoteFlash(data.data.message, 'error');
      }
    } catch (error) {
      submitBtn.classList.remove('loading');
      submitBtn.disabled = false;
      btnText.textContent = window.BonzaQuoteConfig.messages.submit;
      BonzaQuoteFlash(window.BonzaQuoteConfig.messages.error, 'error');
    }
  });
});
