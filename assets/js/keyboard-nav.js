/**
 * Keyboard Navigation for Image and Concept CPTs
 * Allows left/right arrow key navigation
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Listen for arrow key presses
    document.addEventListener('keydown', function(e) {
        
        // Only activate if user is not typing in an input/textarea
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        
        // Left arrow - go to previous
        if (e.key === 'ArrowLeft') {
            const prevLink = document.querySelector('.cpt-keyboard-nav-prev');
            if (prevLink) {
                window.location.href = prevLink.href;
            }
        }
        
        // Right arrow - go to next
        if (e.key === 'ArrowRight') {
            const nextLink = document.querySelector('.cpt-keyboard-nav-next');
            if (nextLink) {
                window.location.href = nextLink.href;
            }
        }
    });
    
});