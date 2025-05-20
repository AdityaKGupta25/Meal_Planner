document.addEventListener('DOMContentLoaded', function() {
    // Get the file input and form elements
    const fileInput = document.getElementById('profile_image');
    const imageForm = document.getElementById('imageForm');
    
    // Auto-submit form when a file is selected
    if (fileInput && imageForm) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                // Validate file type
                const file = fileInput.files[0];
                const fileType = file.type;
                
                if (!fileType.match('image.*')) {
                    alert('Please select an image file (jpg, jpeg, png, gif)');
                    return;
                }
                
                // Validate file size (max 2MB)
                const fileSize = file.size;
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (fileSize > maxSize) {
                    alert('File is too large. Maximum size is 2MB.');
                    return;
                }
                
                // Submit the form
                imageForm.submit();
            }
        });
    }
    
    // Handle radio button changes to update visual state
    document.querySelectorAll('.option-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // When a radio changes, update the active class on the corresponding button
            const optionButtons = document.querySelectorAll('input[name="' + this.name + '"] + .option-button');
            optionButtons.forEach(function(btn) {
                btn.classList.remove('active');
            });
            
            // Add active class to the selected button
            if (this.checked) {
                this.nextElementSibling.classList.add('active');
            }
        });
    });
    
    // Handle save changes click
    const saveChangesBtn = document.querySelector('.profile-content .profile-link:nth-of-type(2)');
    if (saveChangesBtn) {
        saveChangesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            try {
                // Get all personal information values
                const sex = document.querySelector('input[name="sex"]:checked').value;
                const heightFt = document.querySelector('.profile-info .info-group:nth-of-type(2) .measurement-input:first-of-type').value;
                const heightIn = document.querySelector('.profile-info .info-group:nth-of-type(2) .measurement-input:last-of-type').value;
                const weight = document.querySelector('.profile-info .info-group:nth-of-type(3) .measurement-input').value;
                const age = document.querySelector('.profile-info .info-group:nth-of-type(4) .measurement-input').value;
                const bodyfat = document.querySelector('input[name="bodyfat"]:checked').value;
                const activityLevel = document.querySelector('.activity-dropdown').value;
                
                // Log the saved data
                console.log('Saving personal information:', {
                    sex,
                    height: { ft: heightFt, in: heightIn },
                    weight,
                    age,
                    bodyfat,
                    activityLevel
                });
                
                // Show success message
                alert('Personal information saved successfully!');
            } catch(error) {
                console.error('Error saving personal information:', error);
                alert('Please make sure all fields are filled correctly.');
            }
        });
    }
    
    // Add confirmation for logout
    const logoutBtn = document.querySelector('.btn-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to log out?')) {
                e.preventDefault();
            }
        });
    }
}); 