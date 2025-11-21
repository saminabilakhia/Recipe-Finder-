// Recipe Finder JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const results = document.getElementById('results');
    const resultsHeader = document.getElementById('resultsHeader');
    const resultsCount = document.getElementById('resultsCount');
    const dailyRecipe = document.getElementById('dailyRecipe');
    const tagBtns = document.querySelectorAll('.tag-btn');
    const savedSection = document.getElementById('saved');
    const savedResults = document.getElementById('savedResults');
    const savedCount = document.getElementById('savedCount');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const shareModal = document.getElementById('shareModal');
    const shareRecipePreview = document.getElementById('shareRecipePreview');
    const closeModal = document.querySelector('.close');
    
    let currentRecipeToShare = null;

    // Initialize saved recipes
    updateSavedCount();
    
    // Share modal event listeners
    closeModal.addEventListener('click', closeShareModal);
    window.addEventListener('click', function(e) {
        if (e.target === shareModal) {
            closeShareModal();
        }
    });
    
    // Share button event listeners
    document.getElementById('copyLinkBtn').addEventListener('click', copyRecipeLink);
    document.getElementById('shareWhatsAppBtn').addEventListener('click', shareToWhatsApp);
    document.getElementById('shareInstagramBtn').addEventListener('click', shareToInstagram);
    document.getElementById('shareTelegramBtn').addEventListener('click', shareToTelegram);
    document.getElementById('shareLinkedInBtn').addEventListener('click', shareToLinkedIn);
    document.getElementById('sharePinterestBtn').addEventListener('click', shareToPinterest);
    document.getElementById('shareEmailBtn').addEventListener('click', shareToEmail);
    document.getElementById('shareSMSBtn').addEventListener('click', shareToSMS);
    document.getElementById('shareFacebookBtn').addEventListener('click', shareToFacebook);
    document.getElementById('shareTwitterBtn').addEventListener('click', shareToTwitter);

    // Load Recipe of the Day on page load
    loadRecipeOfDay();

    // Search button click event
    searchBtn.addEventListener('click', searchRecipes);

    // Enter key press in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchRecipes();
        }
    });

    // Popular search tag clicks
    tagBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const ingredient = this.getAttribute('data-ingredient');
            searchInput.value = ingredient;
            searchRecipes();
        });
    });

    // Clear all saved recipes
    clearAllBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all saved recipes?')) {
            localStorage.removeItem('savedRecipes');
            updateSavedCount();
            if (savedSection.style.display !== 'none') {
                displaySavedRecipes();
            }
            showNotification('All saved recipes cleared!', 'success');
        }
    });

    // Smooth scrolling and section management for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            
            // Hide all main sections except hero
            document.querySelector('.recipe-of-day-section').style.display = 'none';
            document.querySelector('.search-section').style.display = 'none';
            document.querySelector('.results-section').style.display = 'none';
            savedSection.style.display = 'none';
            
            // Show target section
            if (targetId === 'saved') {
                savedSection.style.display = 'block';
                displaySavedRecipes();
                // Scroll to saved section
                savedSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else if (targetId === 'search') {
                document.querySelector('.search-section').style.display = 'block';
                document.querySelector('.results-section').style.display = 'block';
                document.querySelector('.search-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else if (targetId === 'daily') {
                document.querySelector('.recipe-of-day-section').style.display = 'block';
                document.querySelector('.recipe-of-day-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else if (targetId === 'home') {
                document.querySelector('.recipe-of-day-section').style.display = 'block';
                document.querySelector('.search-section').style.display = 'block';
                document.querySelector('.results-section').style.display = 'block';
                document.querySelector('.hero').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Function to load Recipe of the Day with personalization
    async function loadRecipeOfDay() {
        try {
            // Get search history for personalization
            const searchHistory = getSearchHistory();
            const historyParam = searchHistory.length > 0 ? `?history=${encodeURIComponent(JSON.stringify(searchHistory))}` : '';
            
            const response = await fetch(`api/recipe-of-day.php${historyParam}`);
            const data = await response.json();
            
            if (data.success) {
                const personalizedBadge = data.personalized ? 
                    '<div class="personalized-badge"><i class="fas fa-user"></i> Personalized for you</div>' : 
                    '<div class="recipe-badge"><i class="fas fa-star"></i> Featured Today</div>';
                
                dailyRecipe.innerHTML = `
                    <div class="daily-recipe-content">
                        <div class="recipe-icon">🍽️</div>
                        <h3>${data.recipe.title}</h3>
                        <p class="description">${data.recipe.description}</p>
                        <div class="recipe-meta-daily">
                            <span class="time"><i class="fas fa-clock"></i> ${data.recipe.time || '30 min'}</span>
                            <span class="difficulty"><i class="fas fa-signal"></i> ${data.recipe.difficulty || 'Easy'}</span>
                        </div>
                        ${personalizedBadge}
                    </div>
                `;
            } else {
                dailyRecipe.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-circle"></i> Unable to load recipe of the day</div>';
            }
        } catch (error) {
            console.error('Error loading recipe of the day:', error);
            dailyRecipe.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-circle"></i> Unable to load recipe of the day</div>';
        }
    }
    
    // Search history management
    function getSearchHistory() {
        return JSON.parse(localStorage.getItem('searchHistory') || '[]');
    }
    
    function addToSearchHistory(searchTerm) {
        let history = getSearchHistory();
        
        // Add new search term
        if (searchTerm && !history.includes(searchTerm)) {
            history.unshift(searchTerm); // Add to beginning
            
            // Keep only last 10 searches
            if (history.length > 10) {
                history = history.slice(0, 10);
            }
            
            localStorage.setItem('searchHistory', JSON.stringify(history));
        }
    }

    // Function to search recipes
    async function searchRecipes() {
        const ingredient = searchInput.value.trim();
        
        if (!ingredient) {
            showNotification('Please enter an ingredient', 'warning');
            return;
        }

        // Show loading
        results.innerHTML = `
            <div class="loading-container">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Searching for ${ingredient} recipes...</p>
            </div>
        `;
        resultsHeader.style.display = 'block';
        resultsCount.textContent = 'Searching...';

        // Scroll to results
        results.scrollIntoView({ behavior: 'smooth' });

        try {
            // Add to search history
            addToSearchHistory(ingredient);
            
            const response = await fetch(`api/search.php?ingredient=${encodeURIComponent(ingredient)}`);
            const data = await response.json();

            if (data.success && data.recipes.length > 0) {
                displayRecipes(data.recipes, ingredient);
            } else {
                results.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>No recipes found</h3>
                        <p>Try searching for a different ingredient like chicken, pasta, or tomato.</p>
                    </div>
                `;
                resultsCount.textContent = 'No recipes found';
            }
        } catch (error) {
            console.error('Error searching recipes:', error);
            results.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Oops! Something went wrong</h3>
                    <p>Please try again in a moment.</p>
                </div>
            `;
            resultsCount.textContent = 'Error occurred';
        }
    }

    // Function to display recipes as cards
    function displayRecipes(recipes, searchTerm) {
        resultsCount.textContent = `Found ${recipes.length} recipe${recipes.length !== 1 ? 's' : ''} for "${searchTerm}"`;
        results.innerHTML = '';
        
        recipes.forEach((recipe, index) => {
            const recipeCard = document.createElement('div');
            recipeCard.className = 'recipe-card';
            recipeCard.style.animationDelay = `${index * 0.1}s`;
            
            recipeCard.innerHTML = `
                <div class="recipe-header">
                    <h3>${recipe.title}</h3>
                    <div class="recipe-meta">
                        <span class="difficulty"><i class="fas fa-signal"></i> ${recipe.difficulty || 'Easy'}</span>
                        <span class="time"><i class="fas fa-clock"></i> ${recipe.time || '30 min'}</span>
                    </div>
                </div>
                <p class="description">${recipe.description}</p>
                
                <div class="ingredients">
                    <h4><i class="fas fa-list-ul"></i> Ingredients</h4>
                    <ul>
                        ${recipe.ingredients.map(ingredient => `<li><i class="fas fa-check"></i> ${ingredient}</li>`).join('')}
                    </ul>
                </div>
                
                <div class="steps">
                    <h4><i class="fas fa-tasks"></i> Instructions</h4>
                    <ol>
                        ${recipe.steps.map(step => `<li>${step}</li>`).join('')}
                    </ol>
                </div>
                
                <div class="recipe-footer">
                    <button class="save-btn"><i class="fas fa-heart"></i> Save Recipe</button>
                    <button class="view-btn" data-recipe-index="${index}">
                        <i class="fas fa-eye"></i> View Recipe
                    </button>
                    <button class="share-btn"><i class="fas fa-share"></i> Share</button>
                </div>
            `;
            
            results.appendChild(recipeCard);
        });

        // Store recipes globally for view function
        window.currentRecipes = recipes;
        
        // Add event listeners to save, view, and share buttons
        document.querySelectorAll('.save-btn').forEach((btn, index) => {
            const recipe = recipes[index];
            const recipeId = generateRecipeId(recipe);
            
            // Check if recipe is already saved
            if (isRecipeSaved(recipeId)) {
                btn.classList.add('saved');
                btn.innerHTML = '<i class="fas fa-heart"></i> Saved!';
            }
            
            btn.addEventListener('click', function() {
                toggleSaveRecipe(recipe, this);
            });
        });

        document.querySelectorAll('.view-btn').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                const recipe = recipes[index];
                const recipeData = encodeURIComponent(JSON.stringify(recipe));
                window.open(`recipe-detail.html?recipe=${recipeData}`, '_blank');
            });
        });

        document.querySelectorAll('.share-btn').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                openShareModal(recipes[index]);
            });
        });
    }

    // Saved recipes functions
    function generateRecipeId(recipe) {
        return recipe.title.toLowerCase().replace(/\s+/g, '-');
    }

    function getSavedRecipes() {
        return JSON.parse(localStorage.getItem('savedRecipes') || '[]');
    }

    function saveRecipe(recipe) {
        const savedRecipes = getSavedRecipes();
        const recipeId = generateRecipeId(recipe);
        
        if (!savedRecipes.find(r => generateRecipeId(r) === recipeId)) {
            savedRecipes.push(recipe);
            localStorage.setItem('savedRecipes', JSON.stringify(savedRecipes));
            updateSavedCount();
            return true;
        }
        return false;
    }

    function removeSavedRecipe(recipeId) {
        const savedRecipes = getSavedRecipes();
        const filteredRecipes = savedRecipes.filter(r => generateRecipeId(r) !== recipeId);
        localStorage.setItem('savedRecipes', JSON.stringify(filteredRecipes));
        updateSavedCount();
    }

    function isRecipeSaved(recipeId) {
        const savedRecipes = getSavedRecipes();
        return savedRecipes.some(r => generateRecipeId(r) === recipeId);
    }

    function toggleSaveRecipe(recipe, button) {
        const recipeId = generateRecipeId(recipe);
        
        if (isRecipeSaved(recipeId)) {
            removeSavedRecipe(recipeId);
            button.classList.remove('saved');
            button.innerHTML = '<i class="fas fa-heart"></i> Save Recipe';
            showNotification('Recipe removed from saved!', 'info');
        } else {
            if (saveRecipe(recipe)) {
                button.classList.add('saved');
                button.innerHTML = '<i class="fas fa-heart"></i> Saved!';
                showNotification('Recipe saved successfully!', 'success');
            }
        }
    }

    function updateSavedCount() {
        const count = getSavedRecipes().length;
        savedCount.textContent = count;
        savedCount.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function displaySavedRecipes() {
        const savedRecipes = getSavedRecipes();
        
        if (savedRecipes.length === 0) {
            savedResults.innerHTML = `
                <div class="no-saved-recipes">
                    <i class="fas fa-heart-broken"></i>
                    <h3>No saved recipes yet</h3>
                    <p>Start saving your favorite recipes by clicking the heart icon!</p>
                </div>
            `;
            return;
        }

        savedResults.innerHTML = '';
        
        savedRecipes.forEach((recipe, index) => {
            const recipeCard = document.createElement('div');
            recipeCard.className = 'recipe-card';
            recipeCard.style.animationDelay = `${index * 0.1}s`;
            
            recipeCard.innerHTML = `
                <div class="recipe-header">
                    <h3>${recipe.title}</h3>
                    <div class="recipe-meta">
                        <span class="difficulty"><i class="fas fa-signal"></i> ${recipe.difficulty || 'Easy'}</span>
                        <span class="time"><i class="fas fa-clock"></i> ${recipe.time || '30 min'}</span>
                    </div>
                </div>
                <p class="description">${recipe.description}</p>
                
                <div class="ingredients">
                    <h4><i class="fas fa-list-ul"></i> Ingredients</h4>
                    <ul>
                        ${recipe.ingredients.map(ingredient => `<li><i class="fas fa-check"></i> ${ingredient}</li>`).join('')}
                    </ul>
                </div>
                
                <div class="steps">
                    <h4><i class="fas fa-tasks"></i> Instructions</h4>
                    <ol>
                        ${recipe.steps.map(step => `<li>${step}</li>`).join('')}
                    </ol>
                </div>
                
                <div class="recipe-footer">
                    <button class="save-btn saved" data-recipe-id="${generateRecipeId(recipe)}">
                        <i class="fas fa-heart"></i> Saved!
                    </button>
                    <button class="view-btn" data-recipe-index="${index}">
                        <i class="fas fa-eye"></i> View Recipe
                    </button>
                    <button class="share-btn"><i class="fas fa-share"></i> Share</button>
                </div>
            `;
            
            savedResults.appendChild(recipeCard);
        });

        // Add event listeners for saved recipe buttons
        savedResults.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const recipeId = this.getAttribute('data-recipe-id');
                removeSavedRecipe(recipeId);
                displaySavedRecipes();
                showNotification('Recipe removed from saved!', 'info');
            });
        });

        savedResults.querySelectorAll('.view-btn').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                const savedRecipes = getSavedRecipes();
                const recipe = savedRecipes[index];
                const recipeData = encodeURIComponent(JSON.stringify(recipe));
                window.open(`recipe-detail.html?recipe=${recipeData}`, '_blank');
            });
        });

        savedResults.querySelectorAll('.share-btn').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                const savedRecipes = getSavedRecipes();
                openShareModal(savedRecipes[index]);
            });
        });
    }

    // Share modal functions
    function openShareModal(recipe) {
        currentRecipeToShare = recipe;
        shareRecipePreview.innerHTML = `
            <h4>${recipe.title}</h4>
            <p>${recipe.description}</p>
        `;
        shareModal.style.display = 'block';
    }
    
    function closeShareModal() {
        shareModal.style.display = 'none';
        currentRecipeToShare = null;
    }
    
    function generateRecipeText(recipe) {
        return `🍽️ ${recipe.title}\n\n${recipe.description}\n\n📝 Ingredients:\n${recipe.ingredients.map(ing => `• ${ing}`).join('\n')}\n\n👨‍🍳 Instructions:\n${recipe.steps.map((step, i) => `${i + 1}. ${step}`).join('\n')}\n\n⏱️ Time: ${recipe.time || '30 min'} | 📊 Difficulty: ${recipe.difficulty || 'Easy'}\n\nFound on Recipe Finder! 🔍`;
    }
    
    function copyRecipeLink() {
        if (!currentRecipeToShare) return;
        
        const recipeText = generateRecipeText(currentRecipeToShare);
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(recipeText).then(() => {
                showNotification('Recipe copied to clipboard!', 'success');
                closeShareModal();
            }).catch(() => {
                fallbackCopyText(recipeText);
            });
        } else {
            fallbackCopyText(recipeText);
        }
    }
    
    function fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('Recipe copied to clipboard!', 'success');
        } catch (err) {
            showNotification('Could not copy recipe. Please try again.', 'warning');
        }
        document.body.removeChild(textArea);
        closeShareModal();
    }
    
    function shareToWhatsApp() {
        if (!currentRecipeToShare) return;
        
        const text = encodeURIComponent(generateRecipeText(currentRecipeToShare));
        const url = `https://wa.me/?text=${text}`;
        window.open(url, '_blank');
        closeShareModal();
    }
    
    function shareToEmail() {
        if (!currentRecipeToShare) return;
        
        const subject = encodeURIComponent(`Recipe: ${currentRecipeToShare.title}`);
        const body = encodeURIComponent(generateRecipeText(currentRecipeToShare));
        const url = `mailto:?subject=${subject}&body=${body}`;
        window.open(url);
        closeShareModal();
    }
    
    function shareToFacebook() {
        if (!currentRecipeToShare) return;
        
        const text = encodeURIComponent(`Check out this amazing recipe: ${currentRecipeToShare.title} - ${currentRecipeToShare.description}`);
        const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${text}`;
        window.open(url, '_blank', 'width=600,height=400');
        closeShareModal();
    }
    
    function shareToTwitter() {
        if (!currentRecipeToShare) return;
        
        const text = encodeURIComponent(`🍽️ ${currentRecipeToShare.title}\n\n${currentRecipeToShare.description}\n\n#Recipe #Cooking #Food`);
        const url = `https://twitter.com/intent/tweet?text=${text}&url=${encodeURIComponent(window.location.href)}`;
        window.open(url, '_blank', 'width=600,height=400');
        closeShareModal();
    }
    
    function shareToInstagram() {
        if (!currentRecipeToShare) return;
        
        // Instagram doesn't support direct URL sharing, so copy text for user to paste
        const text = `🍽️ ${currentRecipeToShare.title}\n\n${currentRecipeToShare.description}\n\n⏱️ ${currentRecipeToShare.time || '30 min'} | 📊 ${currentRecipeToShare.difficulty || 'Easy'}\n\n#Recipe #Cooking #Food #Homemade #Delicious`;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('Recipe text copied! Paste it in your Instagram post.', 'success');
                // Try to open Instagram web
                window.open('https://www.instagram.com/', '_blank');
            });
        } else {
            showNotification('Please copy this text for Instagram: ' + text, 'info');
        }
        closeShareModal();
    }
    
    function shareToTelegram() {
        if (!currentRecipeToShare) return;
        
        const text = encodeURIComponent(generateRecipeText(currentRecipeToShare));
        const url = `https://t.me/share/url?url=${encodeURIComponent(window.location.href)}&text=${text}`;
        window.open(url, '_blank');
        closeShareModal();
    }
    
    function shareToLinkedIn() {
        if (!currentRecipeToShare) return;
        
        const title = encodeURIComponent(`Recipe: ${currentRecipeToShare.title}`);
        const summary = encodeURIComponent(currentRecipeToShare.description);
        const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}&title=${title}&summary=${summary}`;
        window.open(url, '_blank', 'width=600,height=400');
        closeShareModal();
    }
    
    function shareToPinterest() {
        if (!currentRecipeToShare) return;
        
        const description = encodeURIComponent(`${currentRecipeToShare.title} - ${currentRecipeToShare.description}`);
        const url = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(window.location.href)}&description=${description}`;
        window.open(url, '_blank', 'width=600,height=400');
        closeShareModal();
    }
    
    function shareToSMS() {
        if (!currentRecipeToShare) return;
        
        const text = encodeURIComponent(`Check out this recipe: ${currentRecipeToShare.title}\n\n${currentRecipeToShare.description}\n\nTime: ${currentRecipeToShare.time || '30 min'}`);
        const url = `sms:?body=${text}`;
        window.open(url);
        closeShareModal();
    }



    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});