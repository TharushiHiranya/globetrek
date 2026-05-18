// Run this script only after the HTML finishes loading
document.addEventListener('DOMContentLoaded', function() {
    
    // Grab the main container that holds the scrolling cards
    const scrollContainer = document.getElementById('featured-carousel');
    
    // Grab the left and right arrow buttons
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    
    // Grab the outer wrapper that detects mouse hovers
    const container = document.querySelector('.carousel-container');
    
    // Stop the script if any carousel element is missing from the page
    if (!scrollContainer || !prevBtn || !nextBtn) return;
    
    // Store the timer ID for the auto-scroll feature
    let autoScrollInterval;
    
    // Calculate how far to scroll each time
    function getScrollStep() {
        // Find the first visible card to measure its width
        const card = document.querySelector('.featured-card');
        if (card) {
            // Add 28 pixels to account for the gap between cards
            return card.offsetWidth + 28;
        }
        // Fall back to a fixed width if no card is found
        return 300;
    }

    // Handle the scrolling logic in both directions
    function scrollCarousel(direction) {
        const step = getScrollStep();
        
        if (direction === 'next') {
            // Check if we reached the right edge of the container
            if (scrollContainer.scrollLeft + scrollContainer.clientWidth >= scrollContainer.scrollWidth - 10) {
                // Jump smoothly back to the beginning
                scrollContainer.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                // Move one step to the right
                scrollContainer.scrollBy({ left: step, behavior: 'smooth' });
            }
        } else {
            // Check if we are already at the far left edge
            if (scrollContainer.scrollLeft <= 0) {
                // Jump smoothly to the end
                scrollContainer.scrollTo({ left: scrollContainer.scrollWidth, behavior: 'smooth' });
            } else {
                // Move one step to the left
                scrollContainer.scrollBy({ left: -step, behavior: 'smooth' });
            }
        }
    }

    // Scroll when the user clicks the arrow buttons
    prevBtn.addEventListener('click', () => scrollCarousel('prev'));
    nextBtn.addEventListener('click', () => scrollCarousel('next'));

    // Move the carousel right every 3 seconds
    function startAutoScroll() {
        autoScrollInterval = setInterval(() => {
            scrollCarousel('next');
        }, 3000);
    }

    // Pause the auto-scroll timer
    function stopAutoScroll() {
        clearInterval(autoScrollInterval);
    }

    // Start moving the carousel immediately
    startAutoScroll();
    
    // Stop moving when the user hovers over the cards
    container.addEventListener('mouseenter', stopAutoScroll);
    
    // Resume moving when the mouse leaves the area
    container.addEventListener('mouseleave', startAutoScroll);
});
