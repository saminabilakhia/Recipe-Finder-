// Recipe Detail Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const recipeHeader = document.getElementById('recipeHeader');
    const recipeContent = document.getElementById('recipeContent');
    const videoGrid = document.getElementById('videoGrid');
    const backBtn = document.getElementById('backBtn');
    
    // Back button functionality
    backBtn.addEventListener('click', function() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = 'index.html';
        }
    });
    
    // Get recipe data from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const recipeData = urlParams.get('recipe');
    
    if (recipeData) {
        try {
            const recipe = JSON.parse(decodeURIComponent(recipeData));
            displayRecipeDetail(recipe);
            loadYouTubeVideos(recipe.title);
        } catch (error) {
            console.error('Error parsing recipe data:', error);
            showError();
        }
    } else {
        showError();
    }
    
    function displayRecipeDetail(recipe) {
        // Update page title
        document.title = `${recipe.title} - Recipe Finder`;
        
        // Display recipe header
        recipeHeader.innerHTML = `
            <div class="recipe-hero">
                <h1>${recipe.title}</h1>
                <p class="recipe-description">${recipe.description}</p>
                <div class="recipe-meta-large">
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>${recipe.time || '30 min'}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-signal"></i>
                        <span>${recipe.difficulty || 'Easy'}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span>4 Servings</span>
                    </div>
                </div>
                <div class="recipe-actions">
                    <button class="btn-save" onclick="toggleSave('${generateRecipeId(recipe)}')">
                        <i class="fas fa-heart"></i> Save Recipe
                    </button>
                    <button class="btn-share" onclick="shareRecipe()">
                        <i class="fas fa-share"></i> Share
                    </button>
                </div>
            </div>
        `;
        
        // Display recipe content
        recipeContent.innerHTML = `
            <div class="recipe-sections">
                <div class="ingredients-section">
                    <h2><i class="fas fa-list-ul"></i> Ingredients</h2>
                    <ul class="ingredients-list">
                        ${recipe.ingredients.map(ingredient => `
                            <li>
                                <input type="checkbox" id="ing-${Math.random()}" class="ingredient-checkbox">
                                <label for="ing-${Math.random()}">${ingredient}</label>
                            </li>
                        `).join('')}
                    </ul>
                </div>
                
                <div class="instructions-section">
                    <h2><i class="fas fa-tasks"></i> Instructions</h2>
                    <ol class="instructions-list">
                        ${recipe.steps.map((step, index) => `
                            <li>
                                <div class="step-number">${index + 1}</div>
                                <div class="step-content">${step}</div>
                            </li>
                        `).join('')}
                    </ol>
                </div>
            </div>
        `;
    }
    
    function loadYouTubeVideos(recipeTitle) {
        // Generate relevant video data based on recipe title
        const videos = generateRelevantVideos(recipeTitle);
        
        setTimeout(() => {
            videoGrid.innerHTML = videos.map(video => `
                <div class="video-card" onclick="playVideo('${video.searchQuery}')">
                    <div class="video-thumbnail">
                        <img src="${video.thumbnail}" alt="${video.title}">
                        <div class="play-button">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="video-info">
                        <h3>${video.title}</h3>
                        <p>${video.channel}</p>
                    </div>
                </div>
            `).join('');
        }, 1000);
    }
    
    function generateRelevantVideos(recipeTitle) {
        // Extract key ingredients and cooking methods from recipe title
        const title = recipeTitle.toLowerCase();
        let mainIngredient = '';
        let cookingMethod = '';
        
        // Identify main ingredients
        if (title.includes('chicken')) mainIngredient = 'chicken';
        else if (title.includes('beef')) mainIngredient = 'beef';
        else if (title.includes('pasta')) mainIngredient = 'pasta';
        else if (title.includes('fish')) mainIngredient = 'fish';
        else if (title.includes('egg')) mainIngredient = 'egg';
        else if (title.includes('cheese')) mainIngredient = 'cheese';
        else if (title.includes('tomato')) mainIngredient = 'tomato';
        else if (title.includes('mushroom')) mainIngredient = 'mushroom';
        else if (title.includes('rice')) mainIngredient = 'rice';
        else if (title.includes('potato')) mainIngredient = 'potato';
        
        // Identify cooking methods
        if (title.includes('stir fry') || title.includes('stir-fry')) cookingMethod = 'stir fry';
        else if (title.includes('curry')) cookingMethod = 'curry';
        else if (title.includes('grilled')) cookingMethod = 'grilled';
        else if (title.includes('soup')) cookingMethod = 'soup';
        else if (title.includes('salad')) cookingMethod = 'salad';
        else if (title.includes('pasta')) cookingMethod = 'pasta';
        else if (title.includes('fried')) cookingMethod = 'fried';
        else if (title.includes('baked')) cookingMethod = 'baked';
        
        // Generate relevant video searches with custom thumbnails
        const videos = [
            {
                title: `How to Make ${recipeTitle}`,
                searchQuery: encodeURIComponent(`${recipeTitle} recipe cooking tutorial`),
                thumbnail: getCustomThumbnail(mainIngredient || 'recipe', 1),
                channel: 'Chef\'s Kitchen'
            },
            {
                title: `${recipeTitle} - Professional Recipe`,
                searchQuery: encodeURIComponent(`${recipeTitle} professional chef recipe`),
                thumbnail: getCustomThumbnail(mainIngredient || 'cooking', 2),
                channel: 'Culinary Masters'
            }
        ];
        
        // Add ingredient-specific video if main ingredient identified
        if (mainIngredient) {
            videos.push({
                title: `Best ${mainIngredient.charAt(0).toUpperCase() + mainIngredient.slice(1)} Recipes`,
                searchQuery: encodeURIComponent(`${mainIngredient} recipes cooking tips`),
                thumbnail: getCustomThumbnail(mainIngredient, 3),
                channel: 'Food Network'
            });
        }
        
        // Add cooking method video if identified
        if (cookingMethod) {
            videos.push({
                title: `${cookingMethod.charAt(0).toUpperCase() + cookingMethod.slice(1)} Cooking Techniques`,
                searchQuery: encodeURIComponent(`${cookingMethod} cooking technique tutorial`),
                thumbnail: getCustomThumbnail(cookingMethod, 4),
                channel: 'Cooking School'
            });
        }
        
        return videos.slice(0, 3); // Return max 3 videos
    }
    
    function getCustomThumbnail(ingredient, variant) {
        // Food-related images from Unsplash
        const thumbnails = {
            chicken: [
                'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1606728035253-49e8a23146de?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=400&h=225&fit=crop'
            ],
            beef: [
                'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1558030006-450675393462?w=400&h=225&fit=crop'
            ],
            pasta: [
                'https://images.unsplash.com/photo-1551892374-ecf8754cf8b0?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1563379091339-03246963d51a?w=400&h=225&fit=crop'
            ],
            fish: [
                'https://images.unsplash.com/photo-1544943910-4c1dc44aab44?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1559847844-d721426d6edc?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1535140728325-781d5ecd3c9d?w=400&h=225&fit=crop'
            ],
            egg: [
                'https://images.unsplash.com/photo-1506976785307-8732e854ad03?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=400&h=225&fit=crop'
            ],
            cheese: [
                'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1452195100486-9cc805987862?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1634141510639-d691d86f47be?w=400&h=225&fit=crop'
            ],
            recipe: [
                'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=225&fit=crop'
            ],
            cooking: [
                'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=225&fit=crop',
                'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=225&fit=crop'
            ]
        };
        
        const images = thumbnails[ingredient] || thumbnails.recipe;
        return images[(variant - 1) % images.length];
    }
    
    function showError() {
        recipeHeader.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Recipe Not Found</h2>
                <p>The recipe you're looking for could not be loaded.</p>
                <button onclick="window.location.href='index.html'" class="btn-back">
                    <i class="fas fa-home"></i> Go Home
                </button>
            </div>
        `;
        recipeContent.innerHTML = '';
        videoGrid.innerHTML = '';
    }
    
    window.playVideo = function(searchQuery) {
        window.open(`https://www.youtube.com/results?search_query=${searchQuery}`, '_blank');
    };
    
    window.toggleSave = function(recipeId) {
        // Save functionality (implement based on your existing save system)
        console.log('Toggle save for:', recipeId);
    };
    
    window.shareRecipe = function() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: window.location.href
            });
        } else {
            // Fallback to copy URL
            navigator.clipboard.writeText(window.location.href);
            alert('Recipe URL copied to clipboard!');
        }
    };
    
    function generateRecipeId(recipe) {
        return recipe.title.toLowerCase().replace(/\s+/g, '-');
    }
});