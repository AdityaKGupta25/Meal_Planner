document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        // Prevent default form submission to handle validation first
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Basic validation
        let isValid = true;
        
        // Check if passwords match
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            isValid = false;
        }
        
        // Check password strength (at least 8 characters)
        if (password.length < 8) {
            alert('Password must be at least 8 characters long');
            isValid = false;
        }
        
        // If all validations pass, submit the form
        if (isValid) {
            form.submit();
        }
    });
}); 