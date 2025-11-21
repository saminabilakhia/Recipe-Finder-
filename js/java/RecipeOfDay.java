import java.util.Random;
import java.util.ArrayList;
import java.util.List;

/**
 * Java component for Recipe Finder application
 * Provides "Recipe of the Day" functionality
 */
public class RecipeOfDay {
    
    // List of featured recipes
    private static final String[][] FEATURED_RECIPES = {
        {"Mediterranean Quinoa Bowl", "Healthy quinoa bowl with fresh vegetables and feta cheese"},
        {"Thai Green Curry", "Authentic Thai curry with coconut milk and fresh herbs"},
        {"Italian Risotto", "Creamy arborio rice with mushrooms and parmesan"},
        {"Mexican Fish Tacos", "Fresh fish tacos with lime and cilantro"},
        {"Japanese Ramen", "Rich tonkotsu ramen with soft-boiled egg"},
        {"French Coq au Vin", "Classic French chicken braised in wine"},
        {"Indian Butter Chicken", "Creamy tomato-based chicken curry"}
    };
    
    public static void main(String[] args) {
        try {
            // Generate random recipe of the day
            Recipe dailyRecipe = getRandomRecipe();
            
            // Output as JSON for PHP to consume
            System.out.println(dailyRecipe.toJson());
            
        } catch (Exception e) {
            // Output error as JSON
            System.out.println("{\"title\":\"Error\",\"description\":\"Unable to generate recipe of the day\"}");
        }
    }
    
    /**
     * Get a random recipe from the featured recipes list
     */
    private static Recipe getRandomRecipe() {
        Random random = new Random();
        int index = random.nextInt(FEATURED_RECIPES.length);
        
        String title = FEATURED_RECIPES[index][0];
        String description = FEATURED_RECIPES[index][1];
        
        return new Recipe(title, description);
    }
    
    /**
     * Simple Recipe class to hold recipe data
     */
    static class Recipe {
        private String title;
        private String description;
        
        public Recipe(String title, String description) {
            this.title = title;
            this.description = description;
        }
        
        /**
         * Convert recipe to JSON string
         */
        public String toJson() {
            return String.format(
                "{\"title\":\"%s\",\"description\":\"%s\"}", 
                escapeJson(title), 
                escapeJson(description)
            );
        }
        
        /**
         * Escape special characters for JSON
         */
        private String escapeJson(String str) {
            return str.replace("\"", "\\\"").replace("\n", "\\n").replace("\r", "\\r");
        }
    }
}