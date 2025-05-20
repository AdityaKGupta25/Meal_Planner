document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    // Here you would normally authenticate the user
    console.log('Login form submitted');
    // Redirect to dashboard after successful login
    // window.location.href = 'dashboard.html';
});