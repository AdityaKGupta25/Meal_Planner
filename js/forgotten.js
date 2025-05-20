document.getElementById('forgot-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Here you would normally send a reset password request to your server
    console.log('Reset password request submitted');
    
    // Show success message
    document.getElementById('success-message').style.display = 'block';
    
    // Clear the form
    this.reset();
});