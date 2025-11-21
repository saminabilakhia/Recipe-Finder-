# Recipe Finder Web Application

A simple recipe finder application built with HTML, CSS, JavaScript, PHP, and Java.

## Project Structure
```
recipe-finder/
├── index.html          # Main frontend page
├── css/
│   └── style.css       # Styling
├── js/
│   └── app.js          # Frontend JavaScript
├── api/
│   ├── search.php      # Recipe search API
│   └── recipe-of-day.php # Recipe of the Day API
├── java/
│   └── RecipeOfDay.java # Java component for daily recipes
└── README.md
```

## Setup Instructions

### 1. Web Server Setup
- Ensure XAMPP is running (Apache and optionally MySQL)
- Place files in `c:\xampp\htdocs\recipe-finder\`

### 2. Java Setup
- Install Java JDK if not already installed
- Compile the Java program:
  ```bash
  cd java
  javac RecipeOfDay.java
  ```

### 3. Access the Application
- Open browser and go to: `http://localhost/recipe-finder/`

## Features

### Frontend (HTML/CSS/JS)
- Clean, responsive design with gradient background
- Search bar for ingredient-based recipe search
- Recipe cards with hover effects
- Recipe of the Day section

### Backend (PHP)
- `search.php`: Handles recipe search by ingredient
- `recipe-of-day.php`: Integrates with Java component
- Returns JSON responses for frontend consumption

### Java Component
- `RecipeOfDay.java`: Generates random featured recipes
- Outputs JSON format for PHP integration
- Provides daily recipe recommendations

## Usage

1. **Search Recipes**: Enter an ingredient (e.g., "chicken", "tomato") and click "Find Recipes"
2. **View Results**: Recipes are displayed as cards with ingredients and steps
3. **Recipe of the Day**: Automatically loads when page opens

## Sample Data
The application includes mock recipes for:
- Chicken Stir Fry
- Chicken Curry  
- Beef Tacos
- Tomato Pasta

## API Endpoints

### GET /api/search.php?ingredient={ingredient}
Returns recipes matching the ingredient.

### GET /api/recipe-of-day.php
Returns the daily featured recipe from Java component.

## Customization

### Adding More Recipes
Edit the `$mockRecipes` array in `api/search.php`

### Adding More Daily Recipes
Edit the `FEATURED_RECIPES` array in `java/RecipeOfDay.java`

### Database Integration
Replace mock data in `search.php` with MySQL queries for production use.