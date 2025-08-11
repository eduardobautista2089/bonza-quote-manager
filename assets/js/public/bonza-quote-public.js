window.BonzaQuoteFlash = function (message, type = 'success') {
    const msg = document.createElement('div');
    msg.className = 'bonza-quote-flash ' + type;
    msg.textContent = message;

    document.body.appendChild(msg);

    // Fade in
    requestAnimationFrame(() => {
        msg.style.opacity = '1';
        msg.style.transform = 'translateY(0)';
    });

    // Remove after delay
    setTimeout(() => {
        msg.style.opacity = '0';
        msg.style.transform = 'translateY(-20px)';
        setTimeout(() => msg.remove(), 500);
    }, 4000);
};

// Auto-trigger and clean URL
document.addEventListener('DOMContentLoaded', function () {
    const url = new URL(window.location.href);
    if (url.searchParams.get('bq_submitted') === '1') {
        BonzaQuoteFlash('Thank you! Your quote has been submitted.');
        // Remove param without reloading
        url.searchParams.delete('bq_submitted');
        window.history.replaceState({}, '', url);
    }
});
