// Run this script only after the HTML finishes loading
document.addEventListener('DOMContentLoaded', function() {
    
    // Find every featured checkbox and loop through them
    document.querySelectorAll('.feature-toggle').forEach(checkbox => {
        
        // Listen for when the admin ticks or unticks the box
        checkbox.addEventListener('change', function() {
            
            // Read the package ID from the data attribute
            const packageId = this.getAttribute('data-id');
            
            // Convert the checkbox state into a 1 or 0 for the database
            const isFeatured = this.checked ? 1 : 0;
            
            // Prepare the data to send it to the server
            const formData = new FormData();
            
            formData.append('package_id', packageId);
            formData.append('is_featured', isFeatured);
            
            // Send a background request to the toggle endpoint
            fetch('toggle_feature.php', {
                method: 'POST',
                body: formData
            })
            // Parse the server response as JSON
            .then(response => response.json())

            .then(data => {
                // Check if the server rejected the change
                if (!data.success) {
                    alert('Failed to update featured status: ' + (data.message || 'Unknown error'));
                    // Flip the checkbox back to its previous state
                    this.checked = !this.checked;
                }
            })
            
            .catch(err => {
                // Show an error if the network request fails completely
                console.error(err);
                alert('Error updating package.');
                // Flip the checkbox back to its previous state
                this.checked = !this.checked;
            });
        });
    });
});
