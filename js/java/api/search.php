<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get the ingredient(s) from query parameter
$ingredientInput = isset($_GET['ingredient']) ? trim($_GET['ingredient']) : '';

if (empty($ingredientInput)) {
    echo json_encode(['success' => false, 'message' => 'No ingredient provided']);
    exit;
}

// Parse multiple ingredients
$ingredients = array_map('trim', explode(',', $ingredientInput));
$ingredients = array_filter($ingredients); // Remove empty values

// Curated authentic recipes with proper ingredient combinations
$recipeTemplates = [
    'chicken' => [
        [
            'title' => 'Classic Chicken Stir Fry',
            'description' => 'Tender chicken with fresh vegetables in a savory Asian sauce',
            'time' => '20 min',
            'difficulty' => 'Easy',
            'ingredients' => ['2 chicken breasts, sliced thin', '1 red bell pepper, sliced', '1 yellow bell pepper, sliced', '1 medium onion, sliced', '2 cloves garlic, minced', '1 tbsp fresh ginger, grated', '3 tbsp soy sauce', '1 tbsp oyster sauce', '2 tbsp vegetable oil', '1 tsp cornstarch', '2 green onions, chopped'],
            'steps' => ['Heat 1 tbsp oil in a wok over high heat', 'Add chicken and stir-fry until golden, about 5 minutes', 'Remove chicken and set aside', 'Add remaining oil, then vegetables', 'Stir-fry vegetables for 3-4 minutes until crisp-tender', 'Return chicken to wok with sauces', 'Stir everything together for 1 minute', 'Garnish with green onions and serve over steamed rice']
        ],
        [
            'title' => 'Butter Chicken',
            'description' => 'Creamy Indian curry with tender chicken in rich tomato sauce',
            'time' => '40 min',
            'difficulty' => 'Medium',
            'ingredients' => ['1.5 lbs chicken thighs, cut into chunks', '1 large onion, diced', '4 cloves garlic, minced', '1 inch ginger, grated', '1 can crushed tomatoes', '1/2 cup heavy cream', '2 tbsp butter', '2 tsp garam masala', '1 tsp cumin', '1 tsp paprika', '1/2 tsp turmeric', 'Salt to taste', 'Fresh cilantro'],
            'steps' => ['Season chicken with salt and half the spices', 'Heat butter in a large pan and brown chicken', 'Remove chicken and sauté onion until golden', 'Add garlic, ginger, and remaining spices', 'Add tomatoes and simmer 10 minutes', 'Return chicken to pan and cook 15 minutes', 'Stir in cream and simmer 5 more minutes', 'Garnish with cilantro and serve with basmati rice']
        ],
        [
            'title' => 'Lemon Herb Roasted Chicken',
            'description' => 'Perfectly roasted chicken with Mediterranean herbs and lemon',
            'time' => '60 min',
            'difficulty' => 'Easy',
            'ingredients' => ['1 whole chicken (3-4 lbs)', '2 lemons, sliced', '4 tbsp olive oil', '2 tsp dried rosemary', '2 tsp dried thyme', '1 tsp oregano', '4 cloves garlic, minced', '1 tsp salt', '1/2 tsp black pepper', 'Fresh parsley for garnish'],
            'steps' => ['Preheat oven to 425°F', 'Pat chicken dry and place in roasting pan', 'Mix olive oil, herbs, garlic, salt, and pepper', 'Rub herb mixture all over chicken', 'Stuff cavity with lemon slices', 'Roast for 50-60 minutes until internal temp reaches 165°F', 'Let rest 10 minutes before carving', 'Garnish with fresh parsley']
        ]
    ],
    'beef' => [
        [
            'title' => 'Classic Beef Tacos',
            'description' => 'Seasoned ground beef in warm tortillas with fresh Mexican toppings',
            'time' => '20 min',
            'difficulty' => 'Easy',
            'ingredients' => ['1 lb ground beef (80/20)', '8 corn or flour tortillas', '1 medium onion, diced', '2 cloves garlic, minced', '2 tsp chili powder', '1 tsp cumin', '1 tsp paprika', '1/2 tsp oregano', '1 cup shredded Mexican cheese', '2 tomatoes, diced', '1 cup lettuce, shredded', 'Sour cream', 'Lime wedges'],
            'steps' => ['Brown ground beef in a large skillet over medium-high heat', 'Add onion and garlic, cook until softened', 'Add spices and cook for 1 minute until fragrant', 'Season with salt and pepper', 'Warm tortillas in a dry pan or microwave', 'Fill tortillas with beef mixture', 'Top with cheese, lettuce, tomatoes, and sour cream', 'Serve with lime wedges']
        ],
        [
            'title' => 'Classic Beef Stew',
            'description' => 'Hearty stew with tender beef and root vegetables in rich gravy',
            'time' => '2 hours',
            'difficulty' => 'Medium',
            'ingredients' => ['2 lbs beef chuck roast, cut into 2-inch cubes', '3 large carrots, cut into chunks', '4 medium potatoes, quartered', '1 large onion, diced', '3 celery stalks, chopped', '4 cloves garlic, minced', '4 cups beef broth', '2 tbsp tomato paste', '3 tbsp flour', '3 tbsp vegetable oil', '2 bay leaves', '1 tsp thyme', 'Salt and pepper'],
            'steps' => ['Pat beef dry and season with salt and pepper', 'Heat oil in Dutch oven and brown beef in batches', 'Remove beef and sauté onion, celery, and garlic', 'Sprinkle flour over vegetables and cook 1 minute', 'Add tomato paste and cook 1 minute more', 'Gradually add broth, scraping up browned bits', 'Return beef to pot with herbs and bay leaves', 'Bring to boil, then simmer covered for 1.5 hours', 'Add carrots and potatoes, cook 30 minutes more until tender']
        ]
    ],
    'pasta' => [
        [
            'title' => 'Authentic Spaghetti Carbonara',
            'description' => 'Traditional Roman pasta with eggs, pecorino cheese, and guanciale',
            'time' => '20 min',
            'difficulty' => 'Medium',
            'ingredients' => ['400g spaghetti', '150g guanciale or pancetta, diced', '4 large egg yolks', '1 whole egg', '100g Pecorino Romano cheese, grated', '1 tsp freshly cracked black pepper', 'Salt for pasta water'],
            'steps' => ['Bring large pot of salted water to boil for pasta', 'Cook guanciale in a large pan until crispy, about 8 minutes', 'Whisk eggs, egg yolks, cheese, and pepper in a bowl', 'Cook spaghetti until al dente, reserve 1 cup pasta water', 'Add hot pasta to pan with guanciale', 'Remove from heat and quickly toss with egg mixture', 'Add pasta water gradually until creamy', 'Serve immediately with extra cheese and pepper']
        ],
        [
            'title' => 'Classic Spaghetti Bolognese',
            'description' => 'Rich meat sauce from Bologna with tomatoes and herbs',
            'time' => '2 hours',
            'difficulty' => 'Medium',
            'ingredients' => ['400g spaghetti', '300g ground beef', '200g ground pork', '1 large onion, finely diced', '2 carrots, finely diced', '2 celery stalks, finely diced', '4 cloves garlic, minced', '800g canned San Marzano tomatoes', '1/2 cup red wine', '1/2 cup whole milk', '3 tbsp olive oil', '2 bay leaves', 'Fresh basil', 'Parmigiano-Reggiano'],
            'steps' => ['Heat olive oil in heavy-bottomed pot', 'Sauté onion, carrots, and celery until soft, about 10 minutes', 'Add garlic and cook 1 minute', 'Add ground meats and brown, breaking up with spoon', 'Add wine and cook until evaporated', 'Add tomatoes, crushing by hand, and bay leaves', 'Simmer on low heat for 1.5-2 hours, stirring occasionally', 'Stir in milk in last 30 minutes', 'Cook pasta and toss with sauce', 'Serve with fresh basil and grated Parmigiano-Reggiano']
        ],
        [
            'title' => 'Aglio e Olio',
            'description' => 'Simple Italian pasta with garlic, olive oil, and red pepper flakes',
            'time' => '15 min',
            'difficulty' => 'Easy',
            'ingredients' => ['400g spaghetti', '6 cloves garlic, thinly sliced', '1/2 cup extra virgin olive oil', '1/2 tsp red pepper flakes', '1/4 cup fresh parsley, chopped', 'Parmigiano-Reggiano cheese', 'Salt and black pepper'],
            'steps' => ['Cook spaghetti in salted water until al dente', 'Heat olive oil in large pan over medium heat', 'Add garlic and red pepper flakes, cook until fragrant', 'Add drained pasta with 1/2 cup pasta water', 'Toss vigorously to create creamy emulsion', 'Add parsley and toss again', 'Serve with grated cheese and black pepper']
        ]
    ],
    'beef' => [
        ['title' => 'Beef Tacos', 'type' => 'mexican', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Beef Stew', 'type' => 'stew', 'time' => '60 min', 'difficulty' => 'Medium'],
        ['title' => 'Beef Stir Fry', 'type' => 'stir-fry', 'time' => '18 min', 'difficulty' => 'Easy'],
        ['title' => 'Beef Burgers', 'type' => 'american', 'time' => '20 min', 'difficulty' => 'Easy'],
        ['title' => 'Beef Stroganoff', 'type' => 'european', 'time' => '35 min', 'difficulty' => 'Medium'],
        ['title' => 'Grilled Beef Steaks', 'type' => 'grilled', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Beef and Broccoli', 'type' => 'asian', 'time' => '22 min', 'difficulty' => 'Easy']
    ],
    'pasta' => [
        ['title' => 'Pasta Carbonara', 'type' => 'italian', 'time' => '20 min', 'difficulty' => 'Medium'],
        ['title' => 'Pasta Bolognese', 'type' => 'italian', 'time' => '40 min', 'difficulty' => 'Medium'],
        ['title' => 'Pasta Primavera', 'type' => 'vegetarian', 'time' => '25 min', 'difficulty' => 'Easy'],
        ['title' => 'Pasta Alfredo', 'type' => 'italian', 'time' => '18 min', 'difficulty' => 'Easy'],
        ['title' => 'Pasta Arrabbiata', 'type' => 'italian', 'time' => '22 min', 'difficulty' => 'Easy'],
        ['title' => 'Pasta Pesto', 'type' => 'italian', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Mac and Cheese', 'type' => 'american', 'time' => '25 min', 'difficulty' => 'Easy']
    ],
    'tomato' => [
        [
            'title' => 'Caprese Salad',
            'description' => 'Fresh mozzarella, ripe tomatoes, and basil with balsamic glaze',
            'time' => '10 min',
            'difficulty' => 'Easy',
            'ingredients' => ['4 large ripe tomatoes, sliced', '8 oz fresh mozzarella, sliced', '1/4 cup fresh basil leaves', '3 tbsp extra virgin olive oil', '2 tbsp balsamic vinegar', '1 tsp honey', 'Sea salt and black pepper'],
            'steps' => ['Arrange tomato and mozzarella slices alternately on platter', 'Tuck basil leaves between slices', 'Whisk olive oil, balsamic vinegar, and honey', 'Drizzle dressing over salad', 'Season with salt and pepper', 'Let sit 10 minutes before serving']
        ],
        [
            'title' => 'Classic Tomato Soup',
            'description' => 'Creamy tomato soup made with fresh tomatoes and herbs',
            'time' => '45 min',
            'difficulty' => 'Easy',
            'ingredients' => ['3 lbs ripe tomatoes, quartered', '1 large onion, chopped', '4 cloves garlic, minced', '2 cups vegetable broth', '1/2 cup heavy cream', '2 tbsp butter', '1 tbsp sugar', '1 tsp dried basil', '1/2 tsp oregano', 'Salt and pepper', 'Fresh basil for garnish'],
            'steps' => ['Roast tomatoes at 400°F for 25 minutes', 'Sauté onion in butter until soft', 'Add garlic and herbs, cook 1 minute', 'Add roasted tomatoes and broth', 'Simmer 15 minutes, then blend until smooth', 'Strain if desired for smoother texture', 'Stir in cream and sugar', 'Season with salt and pepper', 'Garnish with fresh basil']
        ],
        [
            'title' => 'Bruschetta',
            'description' => 'Toasted bread topped with fresh tomatoes, garlic, and basil',
            'time' => '15 min',
            'difficulty' => 'Easy',
            'ingredients' => ['1 French baguette, sliced', '4 ripe tomatoes, diced', '3 cloves garlic, minced', '1/4 cup fresh basil, chopped', '3 tbsp extra virgin olive oil', '1 tbsp balsamic vinegar', '1/2 tsp salt', '1/4 tsp black pepper', '2 cloves garlic for rubbing'],
            'steps' => ['Toast bread slices until golden', 'Rub warm toast with garlic cloves', 'Mix diced tomatoes, minced garlic, basil, olive oil, and vinegar', 'Season tomato mixture with salt and pepper', 'Let mixture sit 10 minutes to develop flavors', 'Top each toast with tomato mixture', 'Serve immediately']
        ]
    ],
    'cheese' => [
        [
            'title' => 'Perfect Grilled Cheese',
            'description' => 'Golden, crispy sandwich with perfectly melted cheese',
            'time' => '10 min',
            'difficulty' => 'Easy',
            'ingredients' => ['8 slices sourdough bread', '8 slices sharp cheddar cheese', '4 tbsp butter, softened', '2 tbsp mayonnaise'],
            'steps' => ['Spread butter on outside of bread slices', 'Spread thin layer of mayo on inside', 'Layer cheese between bread slices', 'Heat pan over medium-low heat', 'Cook sandwich 3-4 minutes until golden', 'Flip carefully and cook until second side is golden', 'Let cool 1 minute before cutting diagonally']
        ],
        [
            'title' => 'Three-Cheese Quesadilla',
            'description' => 'Crispy tortilla with blend of melted cheeses and peppers',
            'time' => '12 min',
            'difficulty' => 'Easy',
            'ingredients' => ['4 large flour tortillas', '1 cup Monterey Jack cheese, shredded', '1/2 cup cheddar cheese, shredded', '1/2 cup mozzarella cheese, shredded', '1 bell pepper, diced small', '1/4 cup red onion, diced', '2 tbsp vegetable oil', 'Sour cream, salsa, and guacamole for serving'],
            'steps' => ['Mix all three cheeses in a bowl', 'Sauté bell pepper and onion until soft', 'Spread cheese mixture on half of each tortilla', 'Add sautéed vegetables over cheese', 'Fold tortillas in half', 'Heat oil in large skillet over medium heat', 'Cook quesadillas 2-3 minutes per side until golden', 'Cut into wedges and serve with condiments']
        ],
        [
            'title' => 'Classic Mac and Cheese',
            'description' => 'Creamy baked macaroni and cheese with crispy breadcrumb topping',
            'time' => '45 min',
            'difficulty' => 'Medium',
            'ingredients' => ['1 lb elbow macaroni', '4 tbsp butter', '4 tbsp flour', '3 cups whole milk', '2 cups sharp cheddar, shredded', '1 cup Gruyere cheese, shredded', '1/2 cup Parmesan, grated', '1 tsp mustard powder', '1/2 tsp paprika', '1 cup panko breadcrumbs', 'Salt and pepper'],
            'steps' => ['Cook macaroni until just shy of al dente', 'Make roux with butter and flour in large pot', 'Gradually whisk in milk until smooth', 'Add mustard powder and seasonings', 'Remove from heat and stir in cheeses until melted', 'Combine with cooked pasta', 'Transfer to buttered baking dish', 'Top with breadcrumbs mixed with melted butter', 'Bake at 375°F for 25-30 minutes until bubbly and golden']
        ]
    ],
    'egg' => [
        [
            'title' => 'Perfect Scrambled Eggs',
            'description' => 'Creamy, fluffy scrambled eggs cooked low and slow',
            'time' => '8 min',
            'difficulty' => 'Easy',
            'ingredients' => ['8 large eggs', '1/4 cup whole milk', '3 tbsp butter', '2 tbsp fresh chives, chopped', 'Salt and white pepper'],
            'steps' => ['Crack eggs into bowl and whisk with milk', 'Season with salt and pepper', 'Heat butter in non-stick pan over low heat', 'Add eggs and stir constantly with rubber spatula', 'Cook slowly, stirring frequently, about 6-8 minutes', 'Remove from heat while still slightly wet', 'Garnish with fresh chives']
        ],
        [
            'title' => 'Classic French Omelette',
            'description' => 'Silky smooth French-style omelette with herbs',
            'time' => '5 min',
            'difficulty' => 'Medium',
            'ingredients' => ['3 large eggs', '2 tbsp butter', '1 tbsp fresh herbs (chives, parsley, tarragon)', '2 tbsp heavy cream', 'Salt and white pepper'],
            'steps' => ['Beat eggs with cream until well combined', 'Season with salt and pepper', 'Heat butter in 8-inch non-stick pan over medium heat', 'Add eggs and stir vigorously with fork', 'When eggs start to set, stop stirring and let cook 30 seconds', 'Add herbs to one side', 'Fold omelette in half and slide onto plate', 'Serve immediately']
        ]
    ],
    'rice' => [
        ['title' => 'Fried Rice', 'type' => 'asian', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Rice Pilaf', 'type' => 'middle-eastern', 'time' => '25 min', 'difficulty' => 'Easy'],
        ['title' => 'Rice Pudding', 'type' => 'dessert', 'time' => '40 min', 'difficulty' => 'Easy'],
        ['title' => 'Spanish Paella', 'type' => 'spanish', 'time' => '45 min', 'difficulty' => 'Hard'],
        ['title' => 'Rice Bowl', 'type' => 'healthy', 'time' => '20 min', 'difficulty' => 'Easy'],
        ['title' => 'Coconut Rice', 'type' => 'tropical', 'time' => '22 min', 'difficulty' => 'Easy']
    ],
    'fish' => [
        ['title' => 'Grilled Fish', 'type' => 'grilled', 'time' => '20 min', 'difficulty' => 'Easy'],
        ['title' => 'Fish Curry', 'type' => 'curry', 'time' => '30 min', 'difficulty' => 'Medium'],
        ['title' => 'Fish Tacos', 'type' => 'mexican', 'time' => '18 min', 'difficulty' => 'Easy'],
        ['title' => 'Baked Fish', 'type' => 'healthy', 'time' => '25 min', 'difficulty' => 'Easy'],
        ['title' => 'Fish and Chips', 'type' => 'british', 'time' => '35 min', 'difficulty' => 'Medium'],
        ['title' => 'Fish Soup', 'type' => 'soup', 'time' => '40 min', 'difficulty' => 'Medium']
    ],
    'potato' => [
        ['title' => 'Mashed Potatoes', 'type' => 'side', 'time' => '20 min', 'difficulty' => 'Easy'],
        ['title' => 'Roasted Potatoes', 'type' => 'roasted', 'time' => '35 min', 'difficulty' => 'Easy'],
        ['title' => 'Potato Curry', 'type' => 'indian', 'time' => '30 min', 'difficulty' => 'Easy'],
        ['title' => 'French Fries', 'type' => 'fried', 'time' => '25 min', 'difficulty' => 'Easy'],
        ['title' => 'Potato Salad', 'type' => 'salad', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Baked Potatoes', 'type' => 'baked', 'time' => '45 min', 'difficulty' => 'Easy']
    ],
    'egg' => [
        ['title' => 'Scrambled Eggs', 'type' => 'breakfast', 'time' => '5 min', 'difficulty' => 'Easy'],
        ['title' => 'Egg Fried Rice', 'type' => 'asian', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Egg Curry', 'type' => 'indian', 'time' => '20 min', 'difficulty' => 'Easy'],
        ['title' => 'Deviled Eggs', 'type' => 'appetizer', 'time' => '12 min', 'difficulty' => 'Easy'],
        ['title' => 'Egg Salad', 'type' => 'salad', 'time' => '10 min', 'difficulty' => 'Easy'],
        ['title' => 'Omelette', 'type' => 'breakfast', 'time' => '8 min', 'difficulty' => 'Easy']
    ],
    'mushroom' => [
        ['title' => 'Mushroom Stir Fry', 'type' => 'stir-fry', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Mushroom Soup', 'type' => 'soup', 'time' => '25 min', 'difficulty' => 'Easy'],
        ['title' => 'Stuffed Mushrooms', 'type' => 'appetizer', 'time' => '30 min', 'difficulty' => 'Medium'],
        ['title' => 'Mushroom Risotto', 'type' => 'italian', 'time' => '35 min', 'difficulty' => 'Medium'],
        ['title' => 'Mushroom Curry', 'type' => 'indian', 'time' => '22 min', 'difficulty' => 'Easy'],
        ['title' => 'Grilled Mushrooms', 'type' => 'grilled', 'time' => '12 min', 'difficulty' => 'Easy']
    ],
    'cheese' => [
        ['title' => 'Cheese Omelette', 'type' => 'breakfast', 'time' => '8 min', 'difficulty' => 'Easy'],
        ['title' => 'Cheese Quesadilla', 'type' => 'mexican', 'time' => '10 min', 'difficulty' => 'Easy'],
        ['title' => 'Cheese Fondue', 'type' => 'appetizer', 'time' => '15 min', 'difficulty' => 'Easy'],
        ['title' => 'Grilled Cheese Sandwich', 'type' => 'american', 'time' => '6 min', 'difficulty' => 'Easy'],
        ['title' => 'Cheese Soufflé', 'type' => 'french', 'time' => '45 min', 'difficulty' => 'Hard'],
        ['title' => 'Cheese Pizza', 'type' => 'italian', 'time' => '25 min', 'difficulty' => 'Medium']
    ]
];

// Common ingredients for recipe generation
$commonIngredients = [
    'vegetables' => ['onion', 'garlic', 'bell pepper', 'carrot', 'celery'],
    'spices' => ['salt', 'black pepper', 'olive oil', 'butter'],
    'herbs' => ['parsley', 'basil', 'thyme', 'oregano']
];

// Generate recipes based on ingredient(s)
function generateRecipes($ingredients, $templates, $commonIngredients) {
    $recipes = [];
    
    if (count($ingredients) == 1) {
        // Single ingredient - use existing logic
        $ingredient = strtolower($ingredients[0]);
        if (isset($templates[$ingredient])) {
            foreach ($templates[$ingredient] as $template) {
                $recipes[] = createRecipe($ingredient, $template, $commonIngredients);
            }
        } else {
            $recipes[] = createGenericRecipe($ingredient, $commonIngredients);
        }
    } else {
        // Multiple ingredients - generate combination recipes
        $recipes = generateCombinationRecipes($ingredients, $commonIngredients);
        
        // Also add individual recipes for each ingredient
        foreach ($ingredients as $ingredient) {
            $ingredient = strtolower(trim($ingredient));
            if (isset($templates[$ingredient])) {
                // Add 1-2 recipes for each ingredient
                $selectedTemplates = array_slice($templates[$ingredient], 0, 2);
                foreach ($selectedTemplates as $template) {
                    $recipes[] = createRecipe($ingredient, $template, $commonIngredients);
                }
            }
        }
    }
    
    return $recipes;
}

// Generate combination recipes using multiple ingredients
function generateCombinationRecipes($ingredients, $commonIngredients) {
    $recipes = [];
    $ingredientList = implode(', ', $ingredients);
    
    // Create different combination recipes
    $combinationTemplates = [
        [
            'title' => ucwords($ingredientList) . ' Stir Fry',
            'type' => 'stir-fry',
            'time' => '25 min',
            'difficulty' => 'Easy'
        ],
        [
            'title' => ucwords($ingredientList) . ' Soup',
            'type' => 'soup', 
            'time' => '35 min',
            'difficulty' => 'Easy'
        ],
        [
            'title' => ucwords($ingredientList) . ' Curry',
            'type' => 'curry',
            'time' => '30 min', 
            'difficulty' => 'Medium'
        ],
        [
            'title' => ucwords($ingredientList) . ' Pasta',
            'type' => 'italian',
            'time' => '22 min',
            'difficulty' => 'Easy'
        ],
        [
            'title' => ucwords($ingredientList) . ' Salad',
            'type' => 'salad',
            'time' => '15 min',
            'difficulty' => 'Easy'
        ]
    ];
    
    foreach ($combinationTemplates as $template) {
        $recipes[] = createCombinationRecipe($ingredients, $template, $commonIngredients);
    }
    
    return $recipes;
}

// Create combination recipe
function createCombinationRecipe($ingredients, $template, $commonIngredients) {
    $recipe = [
        'title' => $template['title'],
        'description' => generateCombinationDescription($ingredients, $template['type']),
        'time' => $template['time'],
        'difficulty' => $template['difficulty'],
        'ingredients' => generateCombinationIngredients($ingredients, $template['type'], $commonIngredients),
        'steps' => generateCombinationSteps($ingredients, $template['type'])
    ];
    
    return $recipe;
}

// Generate description for combination recipes
function generateCombinationDescription($ingredients, $type) {
    $ingredientList = implode(', ', $ingredients);
    
    $descriptions = [
        'stir-fry' => 'Delicious stir fry combining ' . $ingredientList . ' with fresh vegetables',
        'soup' => 'Hearty and nutritious soup featuring ' . $ingredientList,
        'curry' => 'Flavorful curry with ' . $ingredientList . ' in aromatic spices',
        'italian' => 'Italian-style dish with ' . $ingredientList . ' and herbs',
        'salad' => 'Fresh and healthy salad with ' . $ingredientList
    ];
    
    return $descriptions[$type] ?? 'Delicious recipe with ' . $ingredientList;
}

// Generate ingredients for combination recipes
function generateCombinationIngredients($ingredients, $type, $commonIngredients) {
    $baseIngredients = [];
    foreach ($ingredients as $ingredient) {
        $baseIngredients[] = '1 cup ' . trim($ingredient);
    }
    
    switch ($type) {
        case 'stir-fry':
            return array_merge($baseIngredients, [
                '2 tbsp vegetable oil',
                '2 cloves garlic, minced',
                '1 tbsp soy sauce',
                '1 tsp sesame oil',
                'Salt and pepper to taste'
            ]);
        case 'soup':
            return array_merge($baseIngredients, [
                '4 cups vegetable broth',
                '1 onion, diced',
                '2 cloves garlic, minced',
                '1 bay leaf',
                'Salt and pepper to taste'
            ]);
        case 'curry':
            return array_merge($baseIngredients, [
                '1 can coconut milk',
                '2 tbsp curry powder',
                '1 onion, diced',
                '3 cloves garlic, minced',
                '1 inch ginger, grated'
            ]);
        case 'italian':
            return array_merge($baseIngredients, [
                '400g pasta',
                '3 cloves garlic, minced',
                '1/4 cup olive oil',
                '1/2 cup parmesan cheese',
                'Fresh basil leaves'
            ]);
        case 'salad':
            return array_merge($baseIngredients, [
                '4 cups mixed greens',
                '1/4 cup olive oil',
                '2 tbsp balsamic vinegar',
                '1/4 cup nuts or seeds',
                'Salt and pepper'
            ]);
        default:
            return array_merge($baseIngredients, [
                '2 tbsp olive oil',
                '1 onion, diced',
                '2 cloves garlic, minced',
                'Salt and pepper to taste'
            ]);
    }
}

// Generate steps for combination recipes
function generateCombinationSteps($ingredients, $type) {
    $ingredientList = implode(', ', $ingredients);
    
    switch ($type) {
        case 'stir-fry':
            return [
                'Heat oil in a large wok over high heat',
                'Add garlic and stir-fry for 30 seconds',
                'Add ' . $ingredientList . ' and stir-fry for 5-7 minutes',
                'Add soy sauce and sesame oil',
                'Toss everything together until well coated',
                'Serve immediately over rice'
            ];
        case 'soup':
            return [
                'Heat oil in a large pot over medium heat',
                'Sauté onion and garlic until fragrant',
                'Add ' . $ingredientList . ' and cook for 5 minutes',
                'Pour in broth and add bay leaf',
                'Bring to boil, then simmer for 20 minutes',
                'Season with salt and pepper before serving'
            ];
        case 'curry':
            return [
                'Heat oil in a large pan over medium heat',
                'Sauté onion, garlic, and ginger until soft',
                'Add curry powder and cook for 1 minute',
                'Add ' . $ingredientList . ' and cook for 5 minutes',
                'Pour in coconut milk and simmer for 15 minutes',
                'Serve with rice or naan bread'
            ];
        case 'italian':
            return [
                'Cook pasta according to package directions',
                'Heat olive oil in a large pan',
                'Add garlic and cook until fragrant',
                'Add ' . $ingredientList . ' and cook until tender',
                'Toss with cooked pasta and parmesan',
                'Garnish with fresh basil and serve'
            ];
        case 'salad':
            return [
                'Prepare and wash all vegetables',
                'Arrange ' . $ingredientList . ' on mixed greens',
                'Whisk together olive oil and balsamic vinegar',
                'Add nuts or seeds for extra crunch',
                'Drizzle with dressing just before serving',
                'Toss gently and enjoy fresh'
            ];
        default:
            return [
                'Prepare all ingredients',
                'Heat oil in a large pan',
                'Add onion and garlic, cook until soft',
                'Add ' . $ingredientList . ' and cook thoroughly',
                'Season with salt and pepper',
                'Serve hot and enjoy'
            ];
    }
}

// Create a recipe based on template
function createRecipe($mainIngredient, $template, $commonIngredients) {
    // Use pre-defined realistic recipes if available
    if (isset($template['ingredients']) && isset($template['steps'])) {
        return [
            'title' => $template['title'],
            'description' => $template['description'],
            'time' => $template['time'],
            'difficulty' => $template['difficulty'],
            'ingredients' => $template['ingredients'],
            'steps' => $template['steps']
        ];
    }
    
    // Fallback to generated recipe for backward compatibility
    $recipe = [
        'title' => $template['title'],
        'description' => generateDescription($mainIngredient, $template['type']),
        'time' => $template['time'],
        'difficulty' => $template['difficulty'],
        'ingredients' => generateIngredients($mainIngredient, $template['type'], $commonIngredients),
        'steps' => generateSteps($mainIngredient, $template['type'])
    ];
    
    return $recipe;
}

// Create generic recipe for unknown ingredients
function createGenericRecipe($ingredient, $commonIngredients) {
    return [
        'title' => ucfirst($ingredient) . ' Delight',
        'description' => 'A delicious recipe featuring ' . $ingredient,
        'time' => '25 min',
        'difficulty' => 'Easy',
        'ingredients' => array_merge(
            ['2 cups ' . $ingredient],
            array_slice($commonIngredients['vegetables'], 0, 2),
            array_slice($commonIngredients['spices'], 0, 3)
        ),
        'steps' => [
            'Prepare and clean the ' . $ingredient,
            'Heat oil in a large pan',
            'Add onion and garlic, cook until fragrant',
            'Add ' . $ingredient . ' and cook for 10-15 minutes',
            'Season with salt and pepper',
            'Serve hot and enjoy!'
        ]
    ];
}

// Generate description based on type
function generateDescription($ingredient, $type) {
    $descriptions = [
        'stir-fry' => 'Quick and healthy ' . $ingredient . ' stir fry with fresh vegetables',
        'curry' => 'Aromatic and flavorful ' . $ingredient . ' curry with rich spices',
        'grilled' => 'Perfectly grilled ' . $ingredient . ' with herbs and seasonings',
        'soup' => 'Comforting and warming ' . $ingredient . ' soup',
        'mexican' => 'Spicy and delicious ' . $ingredient . ' with Mexican flavors',
        'italian' => 'Classic Italian-style ' . $ingredient . ' dish',
        'asian' => 'Authentic Asian ' . $ingredient . ' with traditional flavors',
        'salad' => 'Fresh and crispy ' . $ingredient . ' salad'
    ];
    
    return $descriptions[$type] ?? 'Delicious ' . $ingredient . ' recipe';
}

// Generate ingredients list
function generateIngredients($mainIngredient, $type, $commonIngredients) {
    $baseIngredients = ['2 cups ' . $mainIngredient];
    
    switch ($type) {
        case 'stir-fry':
            return array_merge($baseIngredients, [
                '1 bell pepper, sliced',
                '1 onion, sliced',
                '2 cloves garlic, minced',
                '2 tbsp soy sauce',
                '1 tbsp vegetable oil',
                'Salt and pepper to taste'
            ]);
        case 'curry':
            return array_merge($baseIngredients, [
                '1 can coconut milk',
                '2 tbsp curry powder',
                '1 onion, diced',
                '3 cloves garlic, minced',
                '1 inch ginger, grated',
                'Salt to taste'
            ]);
        case 'italian':
            return array_merge($baseIngredients, [
                '3 cloves garlic, minced',
                '1/4 cup olive oil',
                '1 can diced tomatoes',
                '1/2 cup parmesan cheese',
                'Fresh basil leaves',
                'Salt and pepper'
            ]);
        case 'mexican':
            return array_merge($baseIngredients, [
                '1 onion, diced',
                '2 cloves garlic, minced',
                '1 tsp cumin',
                '1 tsp chili powder',
                '1/2 cup cheese, shredded',
                'Lime wedges'
            ]);
        case 'asian':
            return array_merge($baseIngredients, [
                '2 tbsp soy sauce',
                '1 tbsp sesame oil',
                '2 cloves garlic, minced',
                '1 inch ginger, grated',
                '1 green onion, sliced',
                '1 tsp cornstarch'
            ]);
        case 'salad':
            return array_merge($baseIngredients, [
                '4 cups mixed greens',
                '1/4 cup olive oil',
                '2 tbsp vinegar',
                '1/4 cup nuts or seeds',
                'Salt and pepper',
                'Optional: cheese'
            ]);
        case 'grilled':
            return array_merge($baseIngredients, [
                '2 tbsp olive oil',
                '2 cloves garlic, minced',
                '1 tsp herbs (thyme/rosemary)',
                '1 lemon, juiced',
                'Salt and pepper',
                'Optional: vegetables'
            ]);
        case 'soup':
            return array_merge($baseIngredients, [
                '4 cups vegetable broth',
                '1 onion, diced',
                '2 carrots, chopped',
                '2 celery stalks, chopped',
                '2 cloves garlic, minced',
                'Fresh herbs'
            ]);
        default:
            return array_merge($baseIngredients, [
                '1 onion, diced',
                '2 cloves garlic, minced',
                '2 tbsp olive oil',
                'Salt and pepper to taste'
            ]);
    }
}

// Generate cooking steps
function generateSteps($mainIngredient, $type) {
    switch ($type) {
        case 'stir-fry':
            return [
                'Heat oil in a large wok or pan over high heat',
                'Add garlic and onion, stir-fry for 1 minute',
                'Add ' . $mainIngredient . ' and cook for 5-7 minutes',
                'Add bell pepper and stir-fry for 3 minutes',
                'Add soy sauce and seasonings',
                'Serve immediately over rice'
            ];
        case 'curry':
            return [
                'Heat oil in a large pot over medium heat',
                'Sauté onion, garlic, and ginger until fragrant',
                'Add curry powder and cook for 1 minute',
                'Add ' . $mainIngredient . ' and brown for 5 minutes',
                'Pour in coconut milk and simmer for 20 minutes',
                'Season with salt and serve with rice'
            ];
        case 'soup':
            return [
                'Heat oil in a large pot over medium heat',
                'Sauté onion, carrots, and celery until soft',
                'Add garlic and cook for 1 minute',
                'Add ' . $mainIngredient . ' and broth',
                'Bring to boil, then simmer for 25 minutes',
                'Season and garnish with fresh herbs'
            ];
        case 'italian':
            return [
                'Heat olive oil in a large pan',
                'Add garlic and cook until fragrant',
                'Add ' . $mainIngredient . ' and cook until tender',
                'Add diced tomatoes and simmer',
                'Season with salt, pepper, and basil',
                'Serve with parmesan cheese'
            ];
        case 'mexican':
            return [
                'Heat oil in a large skillet',
                'Add onion and garlic, cook until soft',
                'Add ' . $mainIngredient . ' and spices',
                'Cook until heated through',
                'Serve with cheese and lime',
                'Garnish with fresh cilantro'
            ];
        case 'asian':
            return [
                'Heat oil in a wok over high heat',
                'Add garlic and ginger, stir-fry briefly',
                'Add ' . $mainIngredient . ' and cook quickly',
                'Add soy sauce and sesame oil',
                'Toss until well coated',
                'Garnish with green onions'
            ];
        case 'salad':
            return [
                'Prepare and wash all vegetables',
                'Arrange ' . $mainIngredient . ' on greens',
                'Whisk together oil and vinegar',
                'Add nuts or seeds for crunch',
                'Drizzle with dressing',
                'Serve immediately'
            ];
        case 'grilled':
            return [
                'Preheat grill to medium-high heat',
                'Marinate ' . $mainIngredient . ' with oil and herbs',
                'Season with salt and pepper',
                'Grill until cooked through',
                'Squeeze lemon juice over top',
                'Let rest before serving'
            ];
        default:
            return [
                'Prepare all ingredients',
                'Heat oil in a pan over medium heat',
                'Add onion and garlic, cook until fragrant',
                'Add ' . $mainIngredient . ' and cook thoroughly',
                'Season with salt and pepper',
                'Serve hot and enjoy!'
            ];
    }
}

// Generate recipes
$recipes = generateRecipes($ingredients, $recipeTemplates, $commonIngredients);

// Return results
echo json_encode([
    'success' => true,
    'recipes' => $recipes,
    'count' => count($recipes),
    'ingredients' => $ingredients,
    'isMultipleIngredients' => count($ingredients) > 1
]);
?>