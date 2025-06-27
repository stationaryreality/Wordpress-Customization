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
