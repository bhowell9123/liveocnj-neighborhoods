#!/bin/bash

# 🚨 EMERGENCY FIX SCRIPT - INDIVIDUAL NEIGHBORHOOD PAGES NOT WORKING
# This script implements the emergency fix for individual neighborhood pages not working
# in the OCNJ Neighborhoods plugin.

echo "🚨 EMERGENCY FIX - INDIVIDUAL NEIGHBORHOOD PAGES NOT WORKING"
echo "============================================================"
echo ""

# Step 1: Basic Diagnosis
echo "🔍 STEP 1: BASIC DIAGNOSIS"
echo "-------------------------"

echo "Checking if WordPress container is running..."
CONTAINER_STATUS=$(docker ps | grep wordpress)
if [ -z "$CONTAINER_STATUS" ]; then
  echo "❌ ERROR: WordPress container is not running!"
  exit 1
else
  echo "✅ WordPress container is running."
fi

echo ""
echo "Checking if neighborhood posts exist..."
POST_COUNT=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root post list --post_type=neighborhood --format=count)
echo "Found $POST_COUNT neighborhood posts."

echo ""
echo "Checking current rewrite rules..."
REWRITE_RULES=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root rewrite list | grep neighborhood)
if [ -z "$REWRITE_RULES" ]; then
  echo "❌ WARNING: No neighborhood rewrite rules found!"
else
  echo "✅ Neighborhood rewrite rules found."
fi

echo ""
echo "Press Enter to continue to Step 2..."
read

# Step 2: Force Fix Rewrite Rules
echo ""
echo "🔧 STEP 2: FORCE FIX REWRITE RULES"
echo "--------------------------------"

echo "Forcefully flushing rewrite rules..."
FLUSH_RESULT=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
delete_option('rewrite_rules');
flush_rewrite_rules(true);
echo 'FORCED REWRITE RULES FLUSH COMPLETE';
")
echo "$FLUSH_RESULT"

echo ""
echo "Press Enter to continue to Step 3..."
read

# Step 3: Verify Post Type Registration
echo ""
echo "🔍 STEP 3: VERIFY POST TYPE REGISTRATION"
echo "-------------------------------------"

echo "Checking if post type is properly registered..."
POST_TYPE_INFO=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
\$post_type = get_post_type_object('neighborhood');
if (\$post_type) {
    echo 'POST TYPE: REGISTERED' . \"\n\";
    echo 'SLUG: ' . \$post_type->rewrite['slug'] . \"\n\";
    echo 'ARCHIVE: ' . (\$post_type->has_archive ? 'YES' : 'NO') . \"\n\";
    echo 'QUERYABLE: ' . (\$post_type->publicly_queryable ? 'YES' : 'NO') . \"\n\";
} else {
    echo 'POST TYPE: NOT REGISTERED - THIS IS THE PROBLEM!' . \"\n\";
}
")
echo "$POST_TYPE_INFO"

# Check if post type is registered
if [[ "$POST_TYPE_INFO" == *"NOT REGISTERED"* ]]; then
  echo ""
  echo "❌ Post type is not registered. Proceeding to Step 4..."
  
  # Step 4: If Post Type Not Registered
  echo ""
  echo "🚨 STEP 4: POST TYPE NOT REGISTERED FIX"
  echo "------------------------------------"
  
  echo "Checking if plugin is active..."
  PLUGIN_STATUS=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root plugin list | grep liveocnj)
  echo "$PLUGIN_STATUS"
  
  echo ""
  echo "Force activating the plugin..."
  ACTIVATE_RESULT=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root plugin activate liveocnj-neighborhoods)
  echo "$ACTIVATE_RESULT"
  
  echo ""
  echo "Re-registering post type manually..."
  REGISTER_RESULT=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
  if (class_exists('LOCNJ_Neighborhoods_PostType_Neighborhood')) {
      LOCNJ_Neighborhoods_PostType_Neighborhood::register();
      flush_rewrite_rules(true);
      echo 'POST TYPE MANUALLY REGISTERED AND FLUSHED';
  } else {
      echo 'POST TYPE CLASS NOT FOUND - PLUGIN NOT LOADING';
  }
  ")
  echo "$REGISTER_RESULT"
else
  echo "✅ Post type is properly registered."
fi

echo ""
echo "Press Enter to continue to Step 5..."
read

# Step 5: Test Individual URL
echo ""
echo "🎯 STEP 5: TEST INDIVIDUAL URL"
echo "--------------------------"

echo "Testing if individual URLs work now..."
TEST_RESULT=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
\$posts = get_posts(['post_type' => 'neighborhood', 'posts_per_page' => 1]);
if (\$posts) {
    \$post = \$posts[0];
    echo 'TEST POST: ' . \$post->post_title . \"\n\";
    echo 'TEST URL: ' . get_permalink(\$post->ID) . \"\n\";
    echo 'SLUG: ' . \$post->post_name . \"\n\";
} else {
    echo 'NO NEIGHBORHOOD POSTS FOUND!';
}
")
echo "$TEST_RESULT"

# Extract the test URL
TEST_URL=$(echo "$TEST_RESULT" | grep "TEST URL:" | sed 's/TEST URL: //')

echo ""
echo "🔍 To verify the fix, please visit this URL in your browser:"
echo "$TEST_URL"
echo ""
echo "If the individual neighborhood page loads correctly, the fix was successful."
echo "If you still see the archive page, proceed to Step 6..."

echo ""
echo "Press Enter to continue to Step 6 (only if needed)..."
read

# Step 6: Nuclear Option - Recreate Post Type
echo ""
echo "🔥 STEP 6: NUCLEAR OPTION - RECREATE POST TYPE"
echo "------------------------------------------"
echo "WARNING: Only proceed with this step if all previous steps failed!"
echo ""

echo "Are you sure you want to proceed with the nuclear option? (y/n)"
read CONFIRM

if [ "$CONFIRM" = "y" ]; then
  echo "Recreating post type from scratch..."
  NUCLEAR_RESULT=$(docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval "
  // Unregister existing post type
  unregister_post_type('neighborhood');

  // Re-register with explicit settings
  register_post_type('neighborhood', [
      'public' => true,
      'publicly_queryable' => true,
      'query_var' => true,
      'has_archive' => 'ocean-city-neighborhoods',
      'rewrite' => [
          'slug' => 'ocean-city-neighborhoods',
          'with_front' => false
      ],
      'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
      'labels' => [
          'name' => 'Neighborhoods',
          'singular_name' => 'Neighborhood'
      ]
  ]);

  // Force flush
  delete_option('rewrite_rules');
  flush_rewrite_rules(true);

  echo 'POST TYPE RECREATED AND FLUSHED';
  ")
  echo "$NUCLEAR_RESULT"
else
  echo "Nuclear option skipped."
fi

echo ""
echo "Press Enter to continue to Step 7..."
read

# Step 7: Final Test
echo ""
echo "🎉 STEP 7: FINAL TEST"
echo "-----------------"

echo "To verify the fix, please:"
echo "1. Go to: http://localhost:8888/ocean-city-neighborhoods/"
echo "2. Click on any neighborhood card"
echo "3. URL should change to: http://localhost:8888/ocean-city-neighborhoods/gold-coast/"
echo "4. Page should load: Individual neighborhood content (not archive)"

echo ""
echo "If the fix still doesn't work, run this diagnostic:"
echo ""
echo "docker exec liveocnj-neighborhoods-wordpress-1 wp --allow-root eval \"
echo '=== EMERGENCY DIAGNOSTIC ===' . \"\\\n\";
echo 'WordPress Version: ' . get_bloginfo('version') . \"\\\n\";
echo 'Active Theme: ' . get_template() . \"\\\n\";
echo 'Permalink Structure: ' . get_option('permalink_structure') . \"\\\n\";

\\\$post_type = get_post_type_object('neighborhood');
echo 'Post Type Registered: ' . (\\\$post_type ? 'YES' : 'NO') . \"\\\n\";

\\\$posts = get_posts(['post_type' => 'neighborhood', 'posts_per_page' => 1]);
echo 'Neighborhood Posts: ' . count(\\\$posts) . \"\\\n\";

if (\\\$posts) {
    echo 'Sample URL: ' . get_permalink(\\\$posts[0]->ID) . \"\\\n\";
}

\\\$rules = get_option('rewrite_rules');
\\\$neighborhood_rules = 0;
foreach (\\\$rules as \\\$pattern => \\\$replacement) {
    if (strpos(\\\$pattern, 'ocean-city-neighborhoods') !== false) {
        \\\$neighborhood_rules++;
    }
}
echo 'Neighborhood Rewrite Rules: ' . \\\$neighborhood_rules . \"\\\n\";
\""

echo ""
echo "🚨 EMERGENCY FIX PROCESS COMPLETE"
echo "================================"