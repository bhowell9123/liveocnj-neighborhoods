<?php
/**
 * ALL DATA EMBEDDED ACF Migration Script
 * 
 * This script contains ALL the neighborhood data embedded directly inside.
 * No file parsing needed - all content is hardcoded here.
 */

if (!defined('ABSPATH')) {
    if (defined('WP_CLI') && WP_CLI) {
        // WP-CLI will handle WordPress loading
    } else {
        die('This script must be run via WP-CLI');
    }
}

class All_Data_Embedded_Migration
{

    private $all_neighborhoods_data = [];
    private $log = [];
    private $landing_content = [];
    private $global_faq = [];
    private $image_assets = [];


    public function __construct()
    {
        $this->log("🎯 ALL DATA EMBEDDED Migration Starting");
        $this->log("All neighborhood content is embedded in this script");
        $this->load_all_embedded_data();
        $this->load_global_embedded_content();
    }

    public function run()
    {
        if (!$this->verify_environment()) {
            return false;
        }

        if (!$this->migrate_all_embedded_data()) {
            return false;
        }

        $this->migrate_global_content();
        $this->verify_migration();
        $this->log("🎉 ALL DATA EMBEDDED migration completed!");
        return true;
    }

    private function verify_environment()
    {
        $this->log("🔍 Verifying environment...");

        if (!function_exists('get_field') || !function_exists('update_field')) {
            $this->log("❌ ERROR: ACF functions not available");
            return false;
        }
        $this->log("✅ ACF functions available");

        return true;
    }

    private function load_all_embedded_data()
    {
        $this->log("📚 Loading ALL embedded neighborhood data...");

        // NORTH END - ALL DATA
        $this->all_neighborhoods_data['north-end'] = [
            'title' => 'North End Neighborhood Guide 2025 | Live OCNJ',
            'heading' => 'North End Neighborhood Guide',
            'meta_description' => 'Explore Ocean City NJ\'s North End — historic homes, family-friendly lifestyle, walkable boardwalk access, and strong community appeal.',
            'about' => 'The North End is one of Ocean City\'s most established neighborhoods, prized for its history, accessibility, and community appeal. Bordering the boardwalk and downtown, the North End blends Victorian charm with modern convenience, making it attractive to both year-round residents and vacationers. Families, investors, and retirees alike are drawn to its safe, walkable streets and proximity to Ocean City\'s best amenities.',
            'facts' => [
                ['text' => 'Proximity to the Ocean City Boardwalk and downtown attractions'],
                ['text' => 'Diverse housing mix of historic homes, duplexes, and modern condos'],
                ['text' => 'Family-friendly with easy access to beaches, parks, and recreation'],
                ['text' => 'Highly walkable to shops, restaurants, and entertainment'],
                ['text' => 'Strong sense of community with frequent local events']
            ],
            'faq' => [
                [
                    'question' => 'What types of homes are in the North End?',
                    'answer' => 'The neighborhood features historic single-family homes, duplexes, and modern condos, offering variety for different needs and budgets.'
                ],
                [
                    'question' => 'Is the North End good for families?',
                    'answer' => 'Yes. The North End is known for its safe streets, beach access, and family-oriented environment, making it very popular with households.'
                ],
                [
                    'question' => 'How is parking in the North End?',
                    'answer' => 'Many homes include off-street parking. Street parking is available, though more competitive in peak summer months.'
                ],
                [
                    'question' => 'What kind of lifestyle can I expect?',
                    'answer' => 'The North End blends coastal relaxation with easy access to the boardwalk, shops, and downtown, creating a vibrant yet comfortable lifestyle.'
                ],
                [
                    'question' => 'Are there investment opportunities in the North End?',
                    'answer' => 'Yes. Consistent demand and strong location make it appealing. Pricing changes with market conditions; request a comp set or consult our Market Monitor.'
                ],
                [
                    'question' => 'What are transportation options in the North End?',
                    'answer' => 'The neighborhood is highly walkable and bike-friendly. Public transit and ride-share services also connect to other parts of Ocean City.'
                ],
                [
                    'question' => 'What is the general price range?',
                    'answer' => 'Values vary by home type and proximity to the beach. Pricing changes with market conditions; request a comp set or see our Market Monitor for details.'
                ]
            ],
            'lifestyle_community' => 'Life in the North End is lively yet laid-back. Families appreciate the quiet residential pockets balanced with entertainment nearby. Seasonal festivals, concerts, and community gatherings strengthen the neighborhood\'s identity and foster connections among residents. Its mix of year-round homeowners and vacationers contributes to a strong, diverse community feel.',
            'walkability_accessibility' => 'The North End is one of Ocean City\'s most walkable areas. Beaches, boardwalk, shops, and restaurants are all within a short walk or bike ride. Sidewalks and bike paths encourage a car-free lifestyle for daily needs. For longer trips, jitneys, public buses, and ride-sharing services provide easy island-wide connections.',
            'parking' => 'As in many popular beach neighborhoods, parking can be competitive in summer. Fortunately, many North End homes include driveways or off-street parking. Street parking is regulated but available, particularly outside of peak weeks.',
            'housing_stock_architecture' => 'The North End showcases Ocean City\'s architectural diversity. Historic Victorian homes line tree-shaded streets, duplexes provide multi-generational flexibility, and modern condos offer convenience and ocean views. This variety ensures housing for different lifestyles, whether buyers seek classic coastal character or contemporary design.',
            'price_snapshot_trends' => 'Homes in the North End are consistently in demand. Proximity to the boardwalk, beaches, and downtown supports long-term stability and rental performance. Exact values shift with property size, age, and market trends. For reliable insights, request a comp set or see the Market Monitor. Investors find the neighborhood appealing thanks to steady demand and limited turnover.',
            'local_attractions_amenities' => 'Residents enjoy immediate access to Ocean City\'s core attractions: the boardwalk, Music Pier, Gillian\'s Wonderland Pier, and Asbury Avenue shopping. Parks, playgrounds, and recreational spaces add to the neighborhood\'s appeal, while restaurants and cafes make dining out convenient and varied.',
            'community_spirit' => 'The North End\'s calendar is full of parades, concerts, and seasonal festivals that strengthen its identity. This culture of activity and togetherness makes it more than just a place to stay — it\'s a place to belong.',
            'schools_services' => 'Families in the North End benefit from the Ocean City School District, offering elementary through high school education with strong academics and extracurriculars. The area is also well-served by local businesses, healthcare providers, and professional services, ensuring everyday convenience.',
            'future_outlook' => 'The North End continues to balance preservation of its historic character with new development. Infrastructure improvements and community investments keep it vibrant and desirable. With its combination of tradition, location, and lifestyle, the North End is positioned as a strong long-term investment for buyers and investors.',
            'related_neighborhoods' => ['Central Boardwalk', 'Gold Coast', 'Bay Area']
        ];

        // THE GARDENS - ALL DATA
        $this->all_neighborhoods_data['the-gardens'] = [
            'title' => 'The Gardens Neighborhood Guide 2025 | Live OCNJ',
            'heading' => 'The Gardens Neighborhood Guide',
            'meta_description' => 'Explore Ocean City NJ\'s Gardens — a prestigious neighborhood with historic homes, luxury residences, family-friendly living, and direct beach access.',
            'about' => 'The Gardens is Ocean City\'s most prestigious neighborhood, known for its grand historic homes, tree-lined streets, and exclusive beachfront location. This upscale area attracts discerning buyers seeking luxury coastal living with a rich architectural heritage. The Gardens combines old-world charm with modern amenities, making it one of the most sought-after addresses in Ocean City.',
            'facts' => [
                ['text' => 'Ocean City\'s most prestigious and historic neighborhood'],
                ['text' => 'Grand Victorian and Colonial homes with architectural significance'],
                ['text' => 'Direct beachfront access with private beach areas'],
                ['text' => 'Tree-lined streets with mature landscaping'],
                ['text' => 'Exclusive location with limited inventory and high property values']
            ],
            'faq' => [
                [
                    'question' => 'What makes The Gardens special?',
                    'answer' => 'The Gardens is Ocean City\'s most prestigious neighborhood, featuring grand historic homes, direct beach access, and an exclusive location that has attracted affluent families for generations.'
                ],
                [
                    'question' => 'What types of homes are in The Gardens?',
                    'answer' => 'The Gardens features large Victorian and Colonial homes, many with historical significance. Properties typically include expansive lots, mature landscaping, and luxury amenities.'
                ],
                [
                    'question' => 'Is The Gardens good for families?',
                    'answer' => 'Absolutely. The Gardens offers a safe, upscale environment with excellent schools nearby, beach access, and a strong sense of community among residents.'
                ],
                [
                    'question' => 'What is the price range in The Gardens?',
                    'answer' => 'The Gardens represents Ocean City\'s luxury market, with homes typically ranging from $1.9 million to $2.47 million, depending on size, location, and amenities.'
                ],
                [
                    'question' => 'How is beach access in The Gardens?',
                    'answer' => 'The Gardens offers direct beach access with some of the most desirable beachfront locations in Ocean City. Many properties have private beach access or are just steps from the sand.'
                ],
                [
                    'question' => 'Are there investment opportunities in The Gardens?',
                    'answer' => 'Yes, The Gardens represents premium investment opportunities with strong appreciation potential and excellent rental performance for luxury vacation properties.'
                ],
                [
                    'question' => 'What amenities are nearby?',
                    'answer' => 'The Gardens is close to upscale dining, shopping, the boardwalk, and Ocean City\'s cultural attractions, while maintaining its exclusive residential character.'
                ]
            ],
            'lifestyle_community' => 'Life in The Gardens revolves around luxury coastal living and community prestige. Residents enjoy morning beach walks, elegant entertaining, and a refined lifestyle. The neighborhood attracts successful professionals, retirees, and families who value privacy, exclusivity, and architectural beauty. Community events and social gatherings reflect the area\'s upscale character.',
            'walkability_accessibility' => 'The Gardens offers excellent walkability to the beach and nearby amenities. Tree-lined sidewalks and well-maintained streets make walking and biking pleasant. While the neighborhood maintains its residential character, downtown attractions and the boardwalk are easily accessible.',
            'parking' => 'Most homes in The Gardens include private driveways and garages, addressing parking needs effectively. The upscale nature of the neighborhood means parking is generally less competitive than in other areas of Ocean City.',
            'housing_stock_architecture' => 'The Gardens showcases Ocean City\'s finest architectural heritage. Grand Victorian homes with wraparound porches, Colonial estates with formal gardens, and custom luxury residences define the streetscape. Many properties feature historical significance, original architectural details, and have been carefully maintained or restored.',
            'price_snapshot_trends' => 'The Gardens represents Ocean City\'s luxury real estate market with median prices ranging from $1.9 million to $2.47 million. Properties in this neighborhood consistently appreciate due to limited inventory, prestigious location, and strong demand from affluent buyers. The area shows resilience in market fluctuations and strong long-term investment potential.',
            'local_attractions_amenities' => 'The Gardens provides easy access to Ocean City\'s finest amenities while maintaining its exclusive character. Residents enjoy proximity to upscale restaurants, boutique shopping, the historic Music Pier, and cultural events. The neighborhood\'s beachfront location offers immediate access to pristine beaches and water activities.',
            'community_spirit' => 'The Gardens maintains a strong sense of community among its residents, with neighborhood associations, social events, and shared commitment to preserving the area\'s historic character. The community values privacy, architectural preservation, and maintaining the neighborhood\'s prestigious reputation.',
            'schools_services' => 'Families in The Gardens have access to excellent educational options through the Ocean City School District and nearby private schools. The area is well-served by premium services, healthcare providers, and professional services that cater to the neighborhood\'s upscale clientele.',
            'future_outlook' => 'The Gardens is positioned for continued appreciation and desirability. Ongoing preservation efforts, infrastructure improvements, and the limited supply of luxury beachfront properties support long-term value growth. The neighborhood\'s historic character and exclusive location ensure its continued status as Ocean City\'s premier residential area.',
            'related_neighborhoods' => ['North End', 'Central Boardwalk', 'Gold Coast']
        ];

        // CENTRAL BOARDWALK - ALL DATA
        $this->all_neighborhoods_data['central-boardwalk'] = [
            'title' => 'Central Boardwalk Neighborhood Guide 2025 | Live OCNJ',
            'heading' => 'Central Boardwalk Neighborhood Guide',
            'meta_description' => 'Discover Ocean City NJ\'s Central Boardwalk — prime boardwalk access, diverse housing, walkable lifestyle, and strong investment potential.',
            'about' => 'The Central Boardwalk neighborhood puts you at the heart of Ocean City\'s action while maintaining residential charm. This area offers the perfect balance of entertainment access and peaceful living, with diverse housing options from condos to single-family homes. Residents enjoy unparalleled access to the boardwalk, beaches, and downtown attractions.',
            'facts' => [
                ['text' => 'Prime location with direct boardwalk and beach access'],
                ['text' => 'Diverse housing options from condos to single-family homes'],
                ['text' => 'Walking distance to restaurants, shops, and entertainment'],
                ['text' => 'Strong rental potential due to tourist proximity'],
                ['text' => 'Mix of year-round residents and seasonal visitors']
            ],
            'faq' => [
                [
                    'question' => 'What makes Central Boardwalk unique?',
                    'answer' => 'Central Boardwalk offers the perfect balance of being in the heart of Ocean City\'s action while maintaining residential character. You get prime boardwalk access with diverse housing options.'
                ],
                [
                    'question' => 'What types of properties are available?',
                    'answer' => 'The area features a mix of modern condos, townhomes, and single-family houses, offering options for different budgets and lifestyle preferences.'
                ],
                [
                    'question' => 'Is it too busy for year-round living?',
                    'answer' => 'While active in summer, the neighborhood has quiet residential streets that provide peaceful living. Many year-round residents appreciate the seasonal energy and off-season tranquility.'
                ],
                [
                    'question' => 'How is the rental market?',
                    'answer' => 'Central Boardwalk has excellent rental potential due to its prime location. Properties here are highly sought after by vacationers, making it attractive for investors.'
                ],
                [
                    'question' => 'What amenities are within walking distance?',
                    'answer' => 'Residents can walk to the boardwalk, beaches, restaurants, shops, amusement rides, and most of Ocean City\'s main attractions.'
                ],
                [
                    'question' => 'How is parking in this area?',
                    'answer' => 'Parking can be competitive in summer due to the central location. Many properties include parking, and there are public lots and street parking available.'
                ],
                [
                    'question' => 'Is it good for families?',
                    'answer' => 'Yes, families love the easy beach access, nearby attractions, and the ability to walk everywhere. The area offers both excitement and residential comfort.'
                ]
            ],
            'lifestyle_community' => 'Central Boardwalk living means being at the center of Ocean City\'s vibrant community. Residents enjoy the energy of boardwalk life, from morning beach walks to evening entertainment. The area attracts a diverse mix of families, young professionals, and retirees who appreciate the convenience and excitement of central living.',
            'walkability_accessibility' => 'This neighborhood is exceptionally walkable, with most daily needs and entertainment options within easy walking distance. The boardwalk provides a scenic route for walking and biking, while streets are well-maintained with good sidewalk access to all major attractions.',
            'parking' => 'Parking in Central Boardwalk requires planning during peak season due to the high visitor traffic. Many residential properties include designated parking spaces, and residents often use a combination of private parking and public lots. Off-season parking is much more readily available.',
            'housing_stock_architecture' => 'Central Boardwalk features a diverse architectural mix reflecting different eras of Ocean City development. Modern condominiums with ocean views, traditional beach houses, and renovated historic properties create an eclectic streetscape that appeals to various tastes and budgets.',
            'price_snapshot_trends' => 'Properties in Central Boardwalk command premium prices due to their prime location and rental potential. The area shows strong appreciation driven by limited inventory and high demand from both owner-occupants and investors. Rental yields are typically strong due to tourist demand.',
            'local_attractions_amenities' => 'Central Boardwalk residents have immediate access to Ocean City\'s premier attractions including amusement rides, arcades, restaurants, shops, and entertainment venues. The beach, fishing pier, and water sports are all steps away, making this location ideal for those who want to be in the center of the action.',
            'community_spirit' => 'The Central Boardwalk community embraces Ocean City\'s vibrant culture, participating in festivals, events, and seasonal celebrations. Residents often form close bonds through shared experiences of boardwalk life and community activities.',
            'schools_services' => 'Families have access to Ocean City School District facilities, with the central location providing easy access to educational resources, libraries, and extracurricular activities. The area is well-served by essential services and healthcare facilities.',
            'future_outlook' => 'Central Boardwalk is positioned for continued growth and development, with ongoing boardwalk improvements and infrastructure investments. The area\'s prime location ensures sustained demand and appreciation potential, making it an attractive long-term investment.',
            'related_neighborhoods' => ['North End', 'The Gardens', 'South End']
        ];

        // RIVIERA - ALL DATA
        $this->all_neighborhoods_data['riviera'] = [
            'title' => 'Riviera Neighborhood Guide 2025 | Live OCNJ',
            'heading' => 'Riviera Neighborhood Guide',
            'meta_description' => 'Discover Ocean City NJ\'s Riviera — sophisticated bayfront living with lagoon access, boating lifestyle, and upscale residential charm.',
            'about' => 'The Riviera neighborhood offers sophisticated bayfront living with a unique boating lifestyle. Located along Ocean City\'s lagoons and waterways, this upscale area attracts residents who value water access, privacy, and luxury amenities. The Riviera combines the tranquility of lagoon living with easy access to both bay and ocean activities.',
            'facts' => [
                ['text' => 'Bayfront and lagoon properties with private docks'],
                ['text' => 'Upscale homes with water views and boat access'],
                ['text' => 'Quiet, residential atmosphere away from tourist areas'],
                ['text' => 'Strong appreciation with 70.1% year-over-year growth'],
                ['text' => 'Exclusive community with limited inventory']
            ],
            'faq' => [
                [
                    'question' => 'What makes the Riviera special?',
                    'answer' => 'The Riviera offers unique bayfront and lagoon living with private docks, water views, and a sophisticated boating lifestyle that\'s rare in Ocean City.'
                ],
                [
                    'question' => 'What types of homes are in the Riviera?',
                    'answer' => 'The Riviera features upscale waterfront homes, many with private docks, boat lifts, and luxury amenities. Properties range from contemporary designs to traditional coastal architecture.'
                ],
                [
                    'question' => 'Is the Riviera good for boating enthusiasts?',
                    'answer' => 'Absolutely. The Riviera is perfect for boating enthusiasts, offering direct water access, private docks, and easy navigation to both the bay and ocean.'
                ],
                [
                    'question' => 'How has the market performed?',
                    'answer' => 'The Riviera has shown exceptional appreciation with 70.1% year-over-year growth, reflecting strong demand for waterfront properties and limited inventory.'
                ],
                [
                    'question' => 'Is it family-friendly?',
                    'answer' => 'Yes, the Riviera offers a safe, upscale environment perfect for families who enjoy water activities, privacy, and a more residential feel away from tourist crowds.'
                ],
                [
                    'question' => 'What water activities are available?',
                    'answer' => 'Residents enjoy boating, fishing, kayaking, paddleboarding, and easy access to both bay and ocean waters for various recreational activities.'
                ],
                [
                    'question' => 'How is the investment potential?',
                    'answer' => 'The Riviera shows strong investment potential with exceptional appreciation rates, limited inventory, and growing demand for luxury waterfront properties.'
                ]
            ],
            'lifestyle_community' => 'Riviera living centers around water activities and upscale coastal lifestyle. Residents enjoy morning boat rides, waterfront entertaining, and the tranquility of lagoon living. The community attracts successful professionals and retirees who value privacy, luxury, and water access.',
            'walkability_accessibility' => 'While the Riviera is more car-dependent than central Ocean City, the neighborhood offers peaceful walking along waterfront areas and well-maintained streets. Boat transportation provides unique access to various parts of Ocean City via the waterways.',
            'parking' => 'Most Riviera properties include ample private parking, including garages and driveways. The residential nature of the neighborhood means parking is rarely an issue, with most homes designed to accommodate multiple vehicles and boat trailers.',
            'housing_stock_architecture' => 'The Riviera showcases luxury waterfront architecture with contemporary designs, traditional coastal styles, and custom homes built to maximize water views. Many properties feature private docks, boat lifts, and outdoor entertaining spaces designed for waterfront living.',
            'price_snapshot_trends' => 'The Riviera has experienced exceptional market performance with 70.1% year-over-year appreciation. This growth reflects strong demand for luxury waterfront properties, limited inventory, and the unique lifestyle the neighborhood offers. Properties here represent premium investments in Ocean City\'s luxury market.',
            'local_attractions_amenities' => 'While maintaining its residential character, the Riviera provides water access to Ocean City\'s attractions via boat. Residents enjoy private water recreation while being able to access restaurants, shopping, and entertainment by land or water.',
            'community_spirit' => 'The Riviera community is close-knit, centered around shared interests in boating and waterfront living. Residents often participate in water-based social activities, neighborhood events, and maintain a strong commitment to preserving the area\'s upscale character.',
            'schools_services' => 'Riviera families have access to quality education through Ocean City School District, with the upscale nature of the neighborhood ensuring access to premium services, healthcare, and professional services that cater to the community\'s needs.',
            'future_outlook' => 'The Riviera is positioned for continued appreciation and desirability. Limited waterfront inventory, growing demand for luxury properties, and Ocean City\'s ongoing development ensure strong long-term investment potential for this exclusive neighborhood.',
            'related_neighborhoods' => ['Bay Area', 'Merion Park', 'OC Homes']
        ];

        // SOUTH END - ALL DATA
        $this->all_neighborhoods_data['south-end'] = [
            'title' => 'South End Neighborhood Guide 2025 | Live OCNJ',
            'heading' => 'South End Neighborhood Guide',
            'meta_description' => 'Explore Ocean City NJ\'s South End — quiet residential charm, family-friendly atmosphere, and peaceful coastal living away from crowds.',
            'about' => 'The South End offers peaceful residential living away from the busier tourist areas while still providing easy access to Ocean City\'s amenities. This family-friendly neighborhood features quiet streets, affordable housing options, and a strong sense of community. The South End is perfect for those seeking a more relaxed coastal lifestyle.',
            'facts' => [
                ['text' => 'Quiet residential streets away from tourist crowds'],
                ['text' => 'Family-friendly atmosphere with safe neighborhoods'],
                ['text' => 'More affordable housing options compared to central areas'],
                ['text' => 'Easy access to beaches and Ocean City amenities'],
                ['text' => 'Strong community feel with local events and activities']
            ],
            'faq' => [
                [
                    'question' => 'What makes the South End appealing?',
                    'answer' => 'The South End offers peaceful residential living with a strong community feel, away from tourist crowds while still providing easy access to all Ocean City amenities.'
                ],
                [
                    'question' => 'Is the South End good for families?',
                    'answer' => 'Absolutely. The South End is known for its family-friendly atmosphere, safe streets, good schools, and community activities that bring neighbors together.'
                ],
                [
                    'question' => 'How are property values in the South End?',
                    'answer' => 'The South End offers more affordable housing options compared to central Ocean City, making it attractive for first-time buyers and families seeking value.'
                ],
                [
                    'question' => 'Is it too far from the action?',
                    'answer' => 'Not at all. While quieter than central areas, the South End provides easy access to the boardwalk, beaches, and downtown attractions by car, bike, or public transit.'
                ],
                [
                    'question' => 'What types of homes are available?',
                    'answer' => 'The South End features a mix of single-family homes, townhouses, and some condos, offering variety for different budgets and family sizes.'
                ],
                [
                    'question' => 'How is the community involvement?',
                    'answer' => 'The South End has strong community involvement with neighborhood associations, local events, and residents who take pride in maintaining the area\'s family-friendly character.'
                ],
                [
                    'question' => 'Are there investment opportunities?',
                    'answer' => 'Yes, the South End offers good investment potential with affordable entry points, steady rental demand, and potential for appreciation as Ocean City continues to develop.'
                ]
            ],
            'lifestyle_community' => 'South End living emphasizes community, family life, and peaceful coastal living. Residents enjoy neighborhood barbecues, local events, and a slower pace of life while still being part of the Ocean City community. The area attracts families, retirees, and those seeking affordable coastal living.',
            'walkability_accessibility' => 'The South End is moderately walkable with good sidewalks and bike-friendly streets. While some amenities require a short drive or bike ride, the neighborhood\'s layout encourages walking and outdoor activities. Public transportation and bike paths connect to other parts of Ocean City.',
            'parking' => 'Parking in the South End is generally excellent, with most homes including driveways and many having garages. Street parking is readily available, and the residential nature of the area means parking is rarely a concern even during peak season.',
            'housing_stock_architecture' => 'The South End features a mix of architectural styles including ranch homes, Cape Cod cottages, and newer construction. The area offers more affordable housing options while maintaining the coastal charm that defines Ocean City living.',
            'price_snapshot_trends' => 'The South End represents Ocean City\'s more affordable market segment, making it attractive for first-time buyers and families. Properties here show steady appreciation and offer good value compared to more central locations, with potential for growth as the area develops.',
            'local_attractions_amenities' => 'While maintaining its residential character, the South End provides access to local parks, community centers, and is a short distance from Ocean City\'s main attractions. Residents enjoy the quiet atmosphere while being able to easily access beaches, shopping, and entertainment.',
            'community_spirit' => 'The South End has a strong community spirit with active neighborhood associations, local events, and residents who work together to maintain the area\'s family-friendly character. Community involvement is high, with regular social gatherings and civic activities.',
            'schools_services' => 'South End families benefit from access to Ocean City School District facilities, with several schools located within or near the neighborhood. The area is well-served by essential services, healthcare facilities, and community resources.',
            'future_outlook' => 'The South End is positioned for steady growth and development as Ocean City continues to expand. The area\'s affordable housing, family-friendly character, and potential for improvement make it an attractive option for long-term investment and community development.',
            'related_neighborhoods' => ['Central Boardwalk', '18th-34th Street', 'Bay Area']
        ];

        $this->log("✅ Loaded ALL embedded data for " . count($this->all_neighborhoods_data) . " neighborhoods");
    }


    private function load_global_embedded_content()
    {
        $this->log("📦 Loading landing content, global FAQ, and image assets...");

        // Landing content
        $this->landing_content = [
            'hero_html' => <<<'HERO_HTML'
<section class="ocnj-hero">
  <div class="ocnj-hero-bg" aria-hidden="true">
    <img src="/wp-content/uploads/2025/08/ocean-city-nj-bay-aerial-neighborhoods-hero.png" alt="" loading="eager" decoding="async">
  </div>
  <div class="ocnj-hero-shade" aria-hidden="true"></div>
  <div class="ocnj-hero__inner">
    <h1>Ocean City, NJ Real Estate: A Complete Neighborhood &amp; FAQ Guide</h1>
    <p class="ocnj-hero__sub">Discover your perfect Ocean City neighborhood</p>
    <div class="ocnj-hero__cta">
      <a class="ocnj-btn ocnj-btn--primary" href="#ocnj-profiles">Explore Neighborhoods</a>
      <a class="ocnj-btn ocnj-btn--ghost" href="#ocnj-faq">Read the FAQ</a>
    </div>
    <ul class="ocnj-stats">
      <li><span class="ocnj-stat-value">$1,045,659</span><span class="ocnj-stat-label">Median Price</span></li>
      <li><span class="ocnj-stat-value">-0.2%</span><span class="ocnj-stat-label">1-Year Change</span></li>
      <li><span class="ocnj-stat-value">35</span><span class="ocnj-stat-label">Days on Market</span></li>
      <li><span class="ocnj-stat-value">187</span><span class="ocnj-stat-label">Homes for Sale</span></li>
    </ul>
  </div>
</section>
HERO_HTML,
            'map_html' => <<<'MAP_HTML'
<section class="ocnj-map-intro" aria-labelledby="ocnj-map-title">
  <div class="ocnj-section__inner">
    <h2 id="ocnj-map-title">Map of all Areas in Ocean City NJ</h2>
    <p>Explore Ocean City's 11 distinct neighborhoods. Click on any area to learn more about its unique character, market data, and lifestyle offerings.</p>
    <div class="ocnj-map-container">
      <img src="/wp-content/plugins/liveocnj-neighborhoods-cpt-fixed/assets/img/Ocean_City_NJ_Map.jpg" alt="Map of Ocean City neighborhoods" class="ocnj-map-image" width="1600" height="900" loading="lazy" decoding="async">
    </div>
  </div>
</section>
MAP_HTML
        ];

        // Image assets
        $this->image_assets = [
            'main_images' => [
                '/wp-content/uploads/2025/08/ocean-city-nj-bay-aerial-neighborhoods-hero.png',
                '/wp-content/plugins/liveocnj-neighborhoods-cpt-fixed/assets/img/Ocean_City_NJ_Map.jpg'
            ],
            'other_images' => [
                '/liveocnj-neighborhoods-cpt/assets/img/card-fallback.jpg',
                '/liveocnj-neighborhoods-cpt/assets/img/ocean-city-map.webp',
                '/liveocnj-neighborhoods-cpt/assets/img/ocean-city-nj-bay-aerial-neighborhoods-hero.jpg',
                '/liveocnj-neighborhoods-cpt/assets/img/ocean-city-nj-bay-aerial-neighborhoods-hero.svg'
            ]
        ];

        // Global FAQ
        $this->global_faq = [
            [
                'category' => 'Buying a Home in Ocean City, NJ',
                'items' => [
                    ['question' => 'What is the best neighborhood in Ocean City NJ for families?', 'answer' => 'For families, consider Merion Park for its peaceful single-family homes and year-round community feel. The North End offers great access to boardwalk attractions while maintaining residential charm. The Gardens provides premium estate living with spacious lots if budget allows. Each neighborhood offers different advantages, so consider your priorities for beach access, quiet streets, and proximity to amenities.'],
                    ['question' => 'Which Ocean City neighborhoods have the strongest rental income potential?', 'answer' => 'The Central Boardwalk and North End areas offer the highest rental income potential with weekly rates of $4,000-$10,000 during peak season due to their proximity to the boardwalk, beaches, and attractions. The Gold Coast commands premium rates of $8,000-$15,000 weekly for luxury oceanfront properties. Properties with 4+ bedrooms, parking, and modern amenities typically achieve the best occupancy rates and rental income.'],
                    ['question' => 'How should I evaluate flood risk when buying in Ocean City?', 'answer' => 'Flood risk varies significantly by neighborhood and even block by block. The Gardens and Merion Park generally have better elevation profiles. Always check FEMA flood maps, request elevation certificates, and get current flood insurance quotes before purchasing. Consider homes with raised mechanical systems, flood vents, and proper drainage. Properties on the bay side often face different flooding challenges than oceanfront locations.'],
                    ['question' => 'What are the most exclusive neighborhoods in Ocean City?', 'answer' => 'The Gardens is Ocean City\'s most exclusive neighborhood with estate-sized lots and median prices from $1.9-2.47 million. The Gold Coast (18th-34th Street along the ocean) represents premium beachfront living with luxury properties reaching $1.55 million and up. The Riviera offers sophisticated bayfront living with lagoon access for boating enthusiasts. These areas feature the largest lots, most distinctive architecture, and highest-end amenities in Ocean City.'],
                    ['question' => 'Which Ocean City neighborhoods are best for boating enthusiasts?', 'answer' => 'The Riviera and Bay Area neighborhoods are ideal for boating enthusiasts. The Riviera (between 16th-23rd Streets) features protected inland lagoons with direct water access and private docks. The Bay Area (North Street to 16th Street along Bay Avenue) offers premium waterfront properties with boat slips and immediate bay access. Both neighborhoods provide the infrastructure needed for recreational boating while maintaining access to Ocean City\'s other amenities.'],
                    ['question' => 'How do Ocean City neighborhoods differ in terms of beach experience?', 'answer' => 'Beach experiences vary significantly across Ocean City neighborhoods. The South End offers wide, uncrowded beaches with natural beauty near Corson\'s Inlet State Park. The Central Boardwalk and North End beaches provide convenient access to boardwalk amenities but tend to be more crowded. The Gold Coast features premium beach access with many oceanfront properties having private or semi-private beach areas. Consider your preference for natural settings versus convenience when choosing a neighborhood.']
                ]
            ],
            [
                'category' => 'Selling in OCNJ',
                'items' => [
                    ['question' => 'What should I know about the seasonal nature of Ocean City neighborhoods?', 'answer' => 'Ocean City transforms dramatically between seasons. Summer brings peak crowds, especially in the Central Boardwalk and North End areas. The shoulder seasons (May, September, October) offer pleasant weather with fewer crowds. Winter sees many businesses closed, particularly in tourist-focused areas. Neighborhoods like Merion Park and Bay Landings maintain more year-round residents and community feel, while boardwalk-adjacent areas experience more seasonal fluctuation in activity and population.'],
                    ['question' => 'Which Ocean City neighborhoods offer the best investment appreciation potential?', 'answer' => 'Recent data shows the Riviera with exceptional appreciation of 70.1% year-over-year, while Merion Park saw 105.5% appreciation. The Gardens and Gold Coast maintain strong long-term appreciation due to their irreplaceable locations and limited supply. The South End offers value acquisition opportunities in a buyer\'s market with potential for future appreciation as natural areas become increasingly scarce. Investment strategy should align with neighborhood characteristics and your timeline.'],
                    ['question' => 'What is the first step to buying in Ocean City?', 'answer' => 'Get pre-approved before touring so you can act fast. In the Central-Boardwalk area by Asbury Avenue, start with a lender pre-approval so you can write a strong offer the day a match appears. Sellers in premier neighborhoods like The Gardens expect proof of funds before considering terms. See our Ocean City buyer\'s guide.'],
                    ['question' => 'Do you need flood insurance in Ocean City, NJ?', 'answer' => 'Most financed homes require flood insurance if the property sits in a designated zone. In bayside areas like Merion Park, request an elevation certificate and compare NFIP vs. private quotes. Your lender and insurance pro can confirm requirements.'],
                    ['question' => 'How long does a financed purchase take in OCNJ?', 'answer' => 'Plan for ~30–60 days from contract to closing. In The Riviera, the timeline allows for appraisal, title, municipal Certificate of Occupancy (CO), and New Jersey\'s 3-day attorney review. See our closing process guide.'],
                    ['question' => 'When is the best time of year to buy?', 'answer' => 'Fall to early winter brings calmer showings and more negotiability. In the South End by Corson\'s Inlet, post-summer months reduce competition. Spring has more inventory, but offers move faster.']
                ]
            ],
            [
                'category' => 'Investing in OCNJ',
                'items' => [
                    ['question' => 'How do Saturday turnovers affect showings near the Boardwalk?', 'answer' => 'Turnover windows (often 10 a.m.–2 p.m.) tighten access and parking. In the Central-Boardwalk blocks, mid-week previews are easier for buyers and tenants alike.'],
                    ['question' => 'What\'s the best time to list in OCNJ?', 'answer' => 'Late winter through spring maximizes exposure before peak rental season. We also see strong activity after Labor Day when serious buyers return. Your micro-market matters—The Gardens vs. South End can behave differently.'],
                    ['question' => 'How should I price a shore home?', 'answer' => 'Use recent comps, condition, and rental history to frame value. We\'ll evaluate view corridors, proximity to beach/boardwalk, parking, and flood zone. A data-driven range helps you avoid stale days on market. Get a free valuation.'],
                    ['question' => 'Do I need to stage my property?', 'answer' => 'Light staging and professional photos typically pay off. Bright, neutral spaces photograph best. Edit decor, freshen linens, and emphasize outdoor living (decks, docks, outdoor showers) to highlight OCNJ lifestyle.'],
                    ['question' => 'What documents will I need to list?', 'answer' => 'Common items include seller disclosure, prior permits, survey (if available), flood/elevation info, and association docs for condos. We\'ll help assemble a clean package that builds buyer confidence.'],
                    ['question' => 'Should I renovate before selling?', 'answer' => 'Target small, high-ROI items. Paint, lighting, hardware, and deep cleaning outperform major remodels on tight timelines. For bigger projects, we\'ll estimate buyer value vs. cost and timing.']
                ]
            ],
            [
                'category' => 'Living in OCNJ',
                'items' => [
                    ['question' => 'How long will my home take to sell?', 'answer' => 'Market-ready listings in prime locations can move quickly; others may need a full cycle. Price, presentation, and seasonality drive days on market. We\'ll set expectations for your specific segment.'],
                    ['question' => 'Are short-term rentals allowed?', 'answer' => 'Weekly rentals are common, but rules vary by property type and association. Confirm municipal licensing/inspections and any condo/HOA restrictions before you buy. We\'ll point you to current city guidelines.'],
                    ['question' => 'What returns do OCNJ investors typically see?', 'answer' => 'Returns vary widely by location, configuration, and management. We model conservative scenarios (seasonal + shoulder weeks) and include cleaning, linens, utilities, management, and cap-ex so you see a realistic net.'],
                    ['question' => 'Can I use a 1031 exchange?', 'answer' => 'Yes—many investors do—but follow IRS timelines and like-kind rules. Work with a qualified intermediary and your CPA. We coordinate property dates to keep your exchange on track.'],
                    ['question' => 'Condo vs. duplex vs. single—what\'s best for rentals?', 'answer' => 'Condos offer lower maintenance; duplexes add flexibility (live/rent); singles can maximize privacy and outdoor value. Your goals and budget decide the fit. We\'ll compare real-world revenue, fees, and upkeep.'],
                    ['question' => 'Do I need a property manager?', 'answer' => 'Self-managing works if you\'re nearby and hands-on; otherwise, a manager reduces hassle. Expect management fees to trade for time, marketing, guest screening, and emergency response.']
                ]
            ],
            [
                'category' => 'Working with an Agent',
                'items' => [
                    ['question' => 'Is there off-season demand?', 'answer' => 'Yes—holidays, events, and long weekends create shoulder-season bookings. Well-heated, nicely furnished homes with fast Wi-Fi attract remote workers and snow-bird stays.'],
                    ['question' => 'What\'s parking like?', 'answer' => 'It depends on the neighborhood and property type. The North End and Central-Boardwalk are walkable but tighter on parking. Bay-side streets and South End blocks generally offer easier parking and garages/driveways.'],
                    ['question' => 'Which schools serve Ocean City?', 'answer' => 'Most on-island homes feed Ocean City School District. Always verify current boundaries, programs, and transportation with the district before purchase.'],
                    ['question' => 'Do I need beach tags?', 'answer' => 'Yes—seasonal beach tags are typically required in summer. Family bundles and pre-season discounts are common. Confirm current pricing and dates with the city before beach season.'],
                    ['question' => 'Are dogs allowed on the beach/boardwalk?', 'answer' => 'Rules are seasonal and time-specific. Dogs are generally restricted during the main season/daytime. Check current city regulations and any HOA/condo pet rules.'],
                    ['question' => 'How\'s the commute to Philly or Atlantic City?', 'answer' => 'OCNJ connects via the 9th St. and 34th St. bridges. Many residents commute to Atlantic City, Somers Point, or mainland employers; Philadelphia is a longer but doable trip for hybrid schedules.']
                ]
            ],
            [
                'category' => 'The Real Estate Process',
                'items' => [
                    ['question' => 'How can I mitigate flood risk?', 'answer' => 'Elevation, proper grading, flood vents, and resilient materials help. Ask for elevation certificates, study flood maps, and consult local contractors on mitigation options for your specific lot.'],
                    ['question' => 'Why work with an on-island agent?', 'answer' => 'Micro-location and timing drive value here. A local agent knows turnover windows, rental calendars, and block-by-block nuance you won\'t see on a map—critical for both primary use and investment returns.'],
                    ['question' => 'How does buyer representation work?', 'answer' => 'Representation, services, and compensation are set by agreement. We\'ll outline options up front so you know exactly how we work for you. Ask us for the current buyer-agency explainer.'],
                    ['question' => 'Is dual agency allowed in NJ?', 'answer' => 'Yes, with informed written consent. We\'ll explain your choices and safeguards so you can decide what\'s right for your transaction. When in doubt, consult your attorney.'],
                    ['question' => 'How do you compete in multiple-offer situations?', 'answer' => 'Preparation wins—pre-approval, clean terms, and clear timelines. We tailor price/terms, use local comps, and coordinate fast inspections to strengthen your position without overreaching.'],
                    ['question' => 'Do you find off-market or pre-market opportunities?', 'answer' => 'Yes—we work our on-island network and watch listings approaching market. Tell us your \'instant yes\' criteria and we\'ll alert you early.'],
                    [
                        'question' => 'How will we communicate and tour?',
                        'answer' => 'We sync around rental turnovers and access windows. Expect fast texts, showing calendars, and digital signatures so you can move as soon as the right home appears.

---'
                    ]
                ]
            ]
        ];
    }

    private function migrate_all_embedded_data()
    {
        $this->log("🔄 Migrating ALL embedded data...");

        $success_count = 0;
        $error_count = 0;

        foreach ($this->all_neighborhoods_data as $slug => $data) {
            $this->log("📝 Migrating embedded data for: $slug");

            $post = get_page_by_path($slug, OBJECT, 'neighborhood');

            if (!$post) {
                $this->log("❌ Post not found for slug: $slug");
                $error_count++;
                continue;
            }

            $post_id = $post->ID;
            $this->log("✅ Found post: " . $post->post_title . " (ID: $post_id)");

            // Update ALL ACF fields with embedded data
            $field_updates = [
                'neighborhood_heading' => $data['heading'],
                'neighborhood_meta_description' => $data['meta_description'],
                'neighborhood_about' => $data['about'],
                'neighborhood_facts' => $data['facts'],
                'neighborhood_faq' => $data['faq'],

                // All detailed sections
                'neighborhood_lifestyle_community' => $data['lifestyle_community'],
                'neighborhood_walkability_accessibility' => $data['walkability_accessibility'],
                'neighborhood_parking' => $data['parking'],
                'neighborhood_housing_stock_architecture' => $data['housing_stock_architecture'],
                'neighborhood_price_snapshot_trends' => $data['price_snapshot_trends'],
                'neighborhood_local_attractions_amenities' => $data['local_attractions_amenities'],
                'neighborhood_community_spirit' => $data['community_spirit'],
                'neighborhood_schools_services' => $data['schools_services'],
                'neighborhood_future_outlook' => $data['future_outlook'],

                // Related neighborhoods
                'neighborhood_related_list' => $data['related_neighborhoods']
            ];

            $post_success = true;
            $updated_count = 0;

            foreach ($field_updates as $field_name => $value) {
                if (empty($value) && !is_array($value)) {
                    $this->log("  ⏭️ Skipping empty: $field_name");
                    continue;
                }

                $result = update_field($field_name, $value, $post_id);

                if ($result) {
                    $this->log("  ✅ Updated: $field_name");
                    $updated_count++;
                } else {
                    $this->log("  ❌ Failed: $field_name");
                    $post_success = false;
                }
            }

            $this->log("📊 Updated $updated_count fields for $slug");

            if ($post_success) {
                $success_count++;
                $this->log("✅ Successfully migrated: $slug");
            } else {
                $error_count++;
                $this->log("❌ Errors in migration: $slug");
            }
        }

        $this->log("📊 Migration summary:");
        $this->log("  ✅ Successful: $success_count");
        $this->log("  ❌ Errors: $error_count");

        return $success_count > 0;
    }


    private function migrate_global_content()
    {
        $this->log("🌐 Migrating global landing content and FAQ...");

        $updated_any = false;

        // Try to find a landing page to attach hero/map content
        $landing = get_page_by_path('ocean-city-neighborhoods', OBJECT, 'page');
        if ($landing) {
            $this->log("🧭 Landing page found: ID " . $landing->ID);
            $fields = [
                'ocnj_landing_hero_html' => $this->landing_content['hero_html'],
                'ocnj_landing_map_html' => $this->landing_content['map_html'],
                'ocnj_image_assets_main' => $this->image_assets['main_images'],
                'ocnj_image_assets_other' => $this->image_assets['other_images'],
            ];
            foreach ($fields as $name => $value) {
                $res = update_field($name, $value, $landing->ID);
                $this->log($res ? "  ✅ Updated $name on landing page" : "  ❌ Failed to update $name on landing page");
                if ($res)
                    $updated_any = true;
            }
        } else {
            $this->log("ℹ️ Landing page not found by slug 'ocean-city-neighborhoods'. Skipping page-specific fields.");
        }

        // Also store to Options, in case the theme/plugin reads from there
        $fields_option = [
            'ocnj_landing_hero_html' => $this->landing_content['hero_html'],
            'ocnj_landing_map_html' => $this->landing_content['map_html'],
            'ocnj_image_assets_main' => $this->image_assets['main_images'],
            'ocnj_image_assets_other' => $this->image_assets['other_images'],
        ];
        foreach ($fields_option as $name => $value) {
            $res = update_field($name, $value, 'option');
            $this->log($res ? "  ✅ Updated $name in Options" : "  ❌ Failed to update $name in Options");
            if ($res)
                $updated_any = true;
        }

        // Global FAQ as nested repeaters: category -> items
        $faq_value = [];
        foreach ($this->global_faq as $cat) {
            $faq_items = [];
            foreach ($cat['items'] as $it) {
                $faq_items[] = [
                    'question' => $it['question'],
                    'answer' => $it['answer'],
                ];
            }
            $faq_value[] = [
                'category_name' => $cat['category'],
                'faq_items' => $faq_items,
            ];
        }

        // Try options first
        $res_opt = update_field('ocnj_global_faq', $faq_value, 'option');
        $this->log($res_opt ? "  ✅ Updated ocnj_global_faq in Options" : "  ❌ Failed to update ocnj_global_faq in Options");
        if ($res_opt)
            $updated_any = true;

        // If landing page exists, also set there
        if (isset($landing) && $landing) {
            $res_page = update_field('ocnj_global_faq', $faq_value, $landing->ID);
            $this->log($res_page ? "  ✅ Updated ocnj_global_faq on landing page" : "  ❌ Failed to update ocnj_global_faq on landing page");
            if ($res_page)
                $updated_any = true;
        }

        if ($updated_any) {
            $this->log("✅ Global content migration done");
            return true;
        } else {
            $this->log("⚠️ No global content fields were updated");
            return false;
        }
    }

    private function verify_migration()
    {
        $this->log("🔍 Verifying migration results...");

        foreach ($this->all_neighborhoods_data as $slug => $expected_data) {
            $post = get_page_by_path($slug, OBJECT, 'neighborhood');

            if (!$post) {
                continue;
            }

            $this->log("🏠 Checking: " . $post->post_title);

            $fields_to_check = [
                'neighborhood_heading' => 'Heading',
                'neighborhood_about' => 'About',
                'neighborhood_facts' => 'Facts',
                'neighborhood_faq' => 'FAQ',
                'neighborhood_lifestyle_community' => 'Lifestyle & Community',
                'neighborhood_walkability_accessibility' => 'Walkability & Accessibility',
                'neighborhood_parking' => 'Parking',
                'neighborhood_housing_stock_architecture' => 'Housing & Architecture',
                'neighborhood_price_snapshot_trends' => 'Price & Trends',
                'neighborhood_local_attractions_amenities' => 'Attractions & Amenities',
                'neighborhood_community_spirit' => 'Community Spirit',
                'neighborhood_schools_services' => 'Schools & Services',
                'neighborhood_future_outlook' => 'Future Outlook'
            ];

            foreach ($fields_to_check as $field_name => $display_name) {
                $value = get_field($field_name, $post->ID);
                $has_value = !empty($value);

                if (is_array($value)) {
                    $status = $has_value ? "✅ " . count($value) . " items" : "❌ Empty";
                } else {
                    $char_count = strlen($value);
                    $status = $has_value ? "✅ $char_count chars" : "❌ Empty";
                }

                $this->log("  $display_name: $status");
            }

            // Additionally verify global fields if present
            $this->log("🔎 Verifying global landing content and FAQ...");
            $global_checks = [
                'ocnj_landing_hero_html' => 'Hero HTML',
                'ocnj_landing_map_html' => 'Map HTML',
                'ocnj_image_assets_main' => 'Main Image Assets',
                'ocnj_image_assets_other' => 'Other Image Assets',
                'ocnj_global_faq' => 'Global FAQ'
            ];

            // Check Options
            foreach ($global_checks as $fname => $label) {
                $val = get_field($fname, 'option');
                $status = empty($val) ? '❌ Empty' : (is_array($val) ? '✅ ' . count((array) $val) . ' items' : '✅ ' . strlen($val) . ' chars');
                $this->log("  Options $label: $status");
            }
        }
    }

    private function log($message)
    {
        $this->log[] = $message;

        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::line($message);
        } else {
            echo $message . "\n";
        }
    }
}

// Run the migration with ALL embedded data
$migration = new All_Data_Embedded_Migration();
$success = $migration->run();

if ($success) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::success("ALL DATA EMBEDDED migration completed successfully!");
    }
} else {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::error("ALL DATA EMBEDDED migration failed!");
    }
}
?>