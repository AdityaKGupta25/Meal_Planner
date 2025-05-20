// script.js
// Mobile Menu Toggle
const menuToggle = document.querySelector('.mobile-menu-toggle');
const navLinks = document.querySelector('.nav-links');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

// Sticky Header
window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    header.classList.toggle('sticky', window.scrollY > 0);
});

// Simple Testimonial Slider (can be expanded with multiple testimonials)
let currentTestimonial = 0;
const testimonials = [
    {
        text: "MealPlanner has completely transformed how I plan meals for my family. I save so much time and money each week, and we're eating healthier than ever!",
        author: "Ramesh",
        role: "Family of 4"
    },
    {
        text: "As a fitness coach, I recommend MealPlanner to all my clients. The nutrition tracking and meal planning features are perfect for anyone with specific dietary goals.",
        author: "Dr. Richa",
        role: "Personal Trainer"
    },
    {
        text: "I used to waste so much food before using MealPlanner. Now I buy exactly what I need and my grocery bills have decreased by 30%!",
        author: "Naina",
        role: "College Student"
    }
];

function showTestimonial(index) {
    const testimonialContainer = document.querySelector('.testimonial');
    const testimonial = testimonials[index];

    testimonialContainer.innerHTML = `
        <p class="testimonial-text">"${testimonial.text}"</p>
        <h4 class="testimonial-author">${testimonial.author}</h4>
        <p class="testimonial-role">${testimonial.role}</p>
    `;
}

// Change testimonial every 5 seconds
setInterval(() => {
    currentTestimonial = (currentTestimonial + 1) % testimonials.length;
    showTestimonial(currentTestimonial);
}, 5000);

// Connect get started, Sign Up for free, and signup buttons
document.querySelectorAll('.btn-primary, .cta-btn, .login-btn').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        // For now, all buttons lead to the same login/signup page
        // This could be enhanced with different behaviors
        alert('Redirecting to login/signup page...');
        // window.location.href = '/signup.html';
    });
});