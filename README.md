# MealPlanner

A comprehensive meal planning web application that helps users create, save, and manage personalized meal plans based on their dietary preferences.

![MealPlanner Screenshot](img/diet%20plan.jpg)

## Features

- ✅ Create custom meal plans based on dietary preferences and restrictions
- ✅ Save and organize multiple meal plans 
- ✅ View detailed nutrition information for meals
- ✅ Edit and customize existing meal plans
- ✅ Mark favorite plans for quick access
- ✅ Generate grocery lists based on your meal plans
- ✅ Download meal plans as PDF documents
- ✅ Automatic random meal image generation
- ✅ Mobile-friendly responsive design

## Demo

[Live Demo](https://yourdomain.com/mealplanner) - Replace with your actual deployment URL when available

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP (for local development)

## Installation

### Prerequisites

- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP, WAMP, MAMP, or similar local development environment

### Setup Instructions

1. Clone this repository to your web server's directory:
   ```
   git clone https://github.com/yourusername/mealplanner.git
   ```

2. Navigate to your project directory:
   ```
   cd mealplanner
   ```

3. Create a MySQL database named 'mealplanner'

4. Update the database connection details in `php/db_connect.php` if needed:
   ```php
   $host = 'localhost';
   $user = 'root'; // change to your database username
   $password = ''; // change to your database password
   $database = 'mealplanner';
   ```

5. Set up the database by visiting:
   ```
   http://localhost/mealplanner/php/update_db_schema.php
   ```

6. Access the application at:
   ```
   http://localhost/mealplanner
   ```

## Project Structure

```
mealplanner/
├── css/                  # CSS stylesheets
├── img/                  # Image assets
│   └── meals/            # Meal images
├── js/                   # JavaScript files
├── meal_plan/            # Core meal planning functionality
│   ├── index.php         # Generate new meal plans
│   ├── saved_plans.php   # View all saved meal plans
│   ├── view_plan.php     # View individual meal plans
│   └── ...
├── php/                  # Backend PHP scripts
│   ├── db_connect.php    # Database connection
│   └── ...
├── index.php             # Homepage
├── login.php             # User login
├── profile.php           # User profile management
├── signup.php            # New user registration
└── README.md             # Project documentation
```

## Features In Detail

### Meal Plan Creation
Users can create custom meal plans based on their dietary preferences, calorie goals, and meal frequency.

### Favorite Plans
Mark any meal plan as a favorite by clicking the star icon. Favorite plans are highlighted for easy access.

### PDF Download
Click the "Download" button on any saved meal plan to get a PDF version for offline use.

### Random Meal Images
The system automatically generates appropriate food images for each meal, enhancing the visual appeal.

## Troubleshooting

- **Images not displaying?** Make sure the `/img/meals` directory exists and is writable
- **Database errors?** Verify your connection settings in `php/db_connect.php`
- **PDF generation issues?** Ensure you have the necessary PHP libraries installed

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add some amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Font Awesome](https://fontawesome.com) for icons
- [Unsplash](https://unsplash.com) for food images 