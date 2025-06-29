(function () {
  // Add class for Wayback detection
  if (document.getElementById('wm-ipp')) {
    document.body.classList.add('web-archive-mode');
  }

  // Patch layout functions if they exist
  if (typeof adjustSidebarHeight === 'function') {
    const originalAdjustSidebarHeight = adjustSidebarHeight;
    adjustSidebarHeight = function (btn) {
      if (document.getElementById('wm-ipp')) return;
      return originalAdjustSidebarHeight(btn);
    };
  }
})();

  // Wayback Snapshot warning
if (document.getElementById('wm-ipp')) {
  const msg = document.createElement('div');
  msg.innerHTML = '📌 This is an archived version — click the ❌ at the top to fix layout.';
  msg.style.cssText = `
    background: #fff3cd;
    color: #856404;
    padding: 1rem;
    text-align: center;
    font-size: 0.9rem;
    font-family: sans-serif;
    border-bottom: 1px solid #ffeeba;
  `;
  document.body.insertBefore(msg, document.body.firstChild);
}
