<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get user's search history from request
$searchHistory = isset($_GET['history']) ? json_decode($_GET['history'], true) : [];

// Daily recipe pool based on date
$dailyRecipes = [
    [
        'title' => 'Mediterranean Quinoa Bowl',
        'description' => 'Healthy quinoa bowl with fresh vegetables, feta cheese, and olive oil dressing',
        'time' => '25 min',
        'difficulty' => 'Easy',
        'tags' => ['healthy', 'vegetarian', 'quinoa', 'mediterranean']
    ],
    [
        'title' => 'Thai Green Curry',
        'description' => 'Authentic Thai curry with coconut milk, fresh herbs, and aromatic spices',
        'time' => '35 min',
        'difficulty' => 'Medium',
        'tags' => ['thai', 'curry', 'coconut', 'spicy']
    ],
    [
        'title' => 'Italian Risotto',
        'description' => 'Creamy arborio rice with mushrooms, parmesan, and white wine',
        'time' => '40 min',
        'difficulty' => 'Medium',
        'tags' => ['italian', 'rice', 'mushroom', 'cheese']
    ],
    [
        'title' => 'Mexican Fish Tacos',
        'description' => 'Fresh fish tacos with lime, cilantro, and spicy mayo',
        'time' => '20 min',
        'difficulty' => 'Easy',
        'tags' => ['mexican', 'fish', 'tacos', 'lime']
    ],
    [
        'title' => 'Japanese Ramen',
        'description' => 'Rich tonkotsu ramen with soft-boiled egg and fresh vegetables',
        'time' => '45 min',
        'difficulty' => 'Hard',
        'tags' => ['japanese', 'ramen', 'egg', 'noodles']
    ],
    [
        'title' => 'French Coq au Vin',
        'description' => 'Classic French chicken braised in wine with herbs and vegetables',
        'time' => '60 min',
        'difficulty' => 'Hard',
        'tags' => ['french', 'chicken', 'wine', 'herbs']
    ],
    [
        'title' => 'Indian Butter Chicken',
        'description' => 'Creamy tomato-based chicken curry with aromatic Indian spices',
        'time' => '35 min',
        'difficulty' => 'Medium',
        'tags' => ['indian', 'chicken', 'curry', 'tomato']
    ],
    [
        'title' => 'Greek Moussaka',
        'description' => 'Layered eggplant casserole with meat sauce and bechamel',
        'time' => '90 min',
        'difficulty' => 'Hard',
        'tags' => ['greek', 'eggplant', 'beef', 'cheese']
    ],
    [
        'title' => 'Korean Bibimbap',
        'description' => 'Mixed rice bowl with vegetables, meat, and spicy gochujang sauce',
        'time' => '30 min',
        'difficulty' => 'Medium',
        'tags' => ['korean', 'rice', 'vegetables', 'spicy']
    ],
    [
        'title' => 'Spanish Paella',
        'description' => 'Traditional Spanish rice dish with seafood, chicken, and saffron',
        'time' => '50 min',
        'difficulty' => 'Hard',
        'tags' => ['spanish', 'rice', 'seafood', 'chicken']
    ]
];

// Get today's date as seed for consistent daily recipe
$today = date('Y-m-d');
$dayOfYear = date('z'); // Day of year (0-365)

// Select base recipe for today
$baseRecipeIndex = $dayOfYear % count($dailyRecipes);
$todaysRecipe = $dailyRecipes[$baseRecipeIndex];

// Personalize based on search history
if (!empty($searchHistory)) {
    $personalizedRecipe = findPersonalizedRecipe($dailyRecipes, $searchHistory, $baseRecipeIndex);
    if ($personalizedRecipe) {
        $todaysRecipe = $personalizedRecipe;
    }
}

// Return the recipe
echo json_encode([
    'success' => true,
    'recipe' => $todaysRecipe,
    'date' => $today,
    'personalized' => !empty($searchHistory)
]);

// Function to find personalized recipe based on search history
function findPersonalizedRecipe($recipes, $searchHistory, $excludeIndex) {
    $scores = [];
    
    foreach ($recipes as $index => $recipe) {
        if ($index === $excludeIndex) continue; // Don't use the same base recipe
        
        $score = 0;
        
        // Score based on matching tags with search history
        foreach ($searchHistory as $searchTerm) {
            $searchTerm = strtolower(trim($searchTerm));
            
            // Check if search term matches recipe tags
            foreach ($recipe['tags'] as $tag) {
                if (strpos($tag, $searchTerm) !== false || strpos($searchTerm, $tag) !== false) {
                    $score += 10;
                }
            }
            
            // Check if search term matches title or description
            if (stripos($recipe['title'], $searchTerm) !== false) {
                $score += 15;
            }
            if (stripos($recipe['description'], $searchTerm) !== false) {
                $score += 5;
            }
        }
        
        if ($score > 0) {
            $scores[$index] = $score;
        }
    }
    
    // Return highest scoring recipe, or null if no matches
    if (!empty($scores)) {
        arsort($scores);
        $bestIndex = array_key_first($scores);
        return $recipes[$bestIndex];
    }
    
    return null;
}
?>