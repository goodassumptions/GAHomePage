<?php
/**
 * Good Assumptions — Child Theme Functions
 * Parent: Twenty Twenty-Four
 * Version: 1.1.0
 */


/* ============================================================
   1. STYLESHEETS — Parent + Child
   ============================================================ */
add_action( 'wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'twentytwentyfour-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'good-assumptions-style',
        get_stylesheet_uri(),
        array( 'twentytwentyfour-style' ),
        wp_get_theme()->get( 'Version' )
    );

} );


/* ============================================================
   2. GOOGLE FONTS — DM Sans · DM Serif Display · DM Mono
   ============================================================ */
add_action( 'wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'good-assumptions-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300;1,9..40,400&family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&display=swap',
        array(),
        null
    );

} );

// Preconnect hints for Google Fonts (must be early in <head>)
add_action( 'wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );


/* ============================================================
   3. GLOBAL ANIMATION JS — Cacheable external file
   Replaces the inline footer script. Covers:
     · .reveal           — fade + slide up on intersection
     · .reveal-stagger   — staggered children reveal
     · Active nav state  — .ga-nav + WP block nav
     · Anchor pill sync  — .anchor-pill[href^="#"]
   ============================================================ */
add_action( 'wp_enqueue_scripts', function () {

	wp_enqueue_script(
    'good-assumptions-animations',
    get_stylesheet_directory_uri() . '/ga-animations.js',
    array(),
    '1.1.0',
    array(
        'strategy'  => 'defer',
        'in_footer' => true,
    )
);

} );


/* ============================================================
   4. EDITOR STYLES — Block editor matches front end
   ============================================================ */
add_action( 'after_setup_theme', function () {
    add_editor_style( 'style.css' );
} );


/* ============================================================
   5. THEME SUPPORT
   ============================================================ */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ) );
} );


/* ============================================================
   6. BLOCK PATTERN CATEGORY — "Good Assumptions"
   Registers the GA category that all patterns below are filed under.
   Shows in the block inserter under its own heading.
   ============================================================ */
add_action( 'init', function () {

    register_block_pattern_category(
        'good-assumptions',
        array( 'label' => __( 'Good Assumptions', 'good-assumptions' ) )
    );

} );


/* ============================================================
   7. BLOCK PATTERNS — Design modules
   Each pattern is stored in /patterns/ as an .html file.
   WordPress auto-discovers files in that folder (WP 6.0+),
   but we also register explicitly here for compatibility and
   to assign categories cleanly.

   Pattern files live at:
     wp-content/themes/good-assumptions/patterns/[name].php
   ============================================================ */
add_action( 'init', function () {

    $pattern_dir = get_stylesheet_directory() . '/patterns/';

    // ── Pattern: Section Header ──────────────────────────────
    // Eyebrow number + DM Serif Display title + lead text
    if ( file_exists( $pattern_dir . 'section-header.php' ) ) {
        register_block_pattern(
            'good-assumptions/section-header',
            array(
                'title'       => __( 'Section Header', 'good-assumptions' ),
                'description' => __( 'Numbered eyebrow, display title, and lead sentence. Use to open any content section.', 'good-assumptions' ),
                'categories'  => array( 'good-assumptions' ),
                'content'     => file_get_contents( $pattern_dir . 'section-header.php' ),
            )
        );
    }

    // ── Pattern: Concept Card ────────────────────────────────
    // Dark card with entity color left-bar, badge, title, body
    if ( file_exists( $pattern_dir . 'concept-card.php' ) ) {
        register_block_pattern(
            'good-assumptions/concept-card',
            array(
                'title'       => __( 'Concept Card', 'good-assumptions' ),
                'description' => __( 'Entity-typed card with colored left-bar, badge, ID, title, and body. Use in Concepts index and article sidebars.', 'good-assumptions' ),
                'categories'  => array( 'good-assumptions' ),
                'content'     => file_get_contents( $pattern_dir . 'concept-card.php' ),
            )
        );
    }

    // ── Pattern: SAEO Diagnostic Banner ─────────────────────
    // 4-column S/A/E/O layer row with color coding
    if ( file_exists( $pattern_dir . 'saeo-diagnostic-banner.php' ) ) {
        register_block_pattern(
            'good-assumptions/saeo-diagnostic-banner',
            array(
                'title'       => __( 'SAEO Diagnostic Banner', 'good-assumptions' ),
                'description' => __( 'Four-column row showing the Strategy / Application / Execution / Operations diagnostic layers with color coding.', 'good-assumptions' ),
                'categories'  => array( 'good-assumptions' ),
                'content'     => file_get_contents( $pattern_dir . 'saeo-diagnostic-banner.php' ),
            )
        );
    }

    // ── Pattern: Article CTA Block ───────────────────────────
    // Gap-creation conversion footer — drives SAEO signups
    if ( file_exists( $pattern_dir . 'article-cta.php' ) ) {
        register_block_pattern(
            'good-assumptions/article-cta',
            array(
                'title'       => __( 'Article CTA — SAEO Gap', 'good-assumptions' ),
                'description' => __( 'End-of-article conversion block. Creates the gap between GA education and SAEO depth. Drives signups.', 'good-assumptions' ),
                'categories'  => array( 'good-assumptions' ),
                'content'     => file_get_contents( $pattern_dir . 'article-cta.php' ),
            )
        );
    }

    // ── Pattern: Entity Badge Row ────────────────────────────
    // LIM / COG / CV / PT chips with dot indicators and IDs
    if ( file_exists( $pattern_dir . 'entity-badge-row.php' ) ) {
        register_block_pattern(
            'good-assumptions/entity-badge-row',
            array(
                'title'       => __( 'Entity Badge Row', 'good-assumptions' ),
                'description' => __( 'Horizontal row of entity type chips — Liminal, Cognitive, Choice Vector, Primary Tactic. Use at top of concept and article pages.', 'good-assumptions' ),
                'categories'  => array( 'good-assumptions' ),
                'content'     => file_get_contents( $pattern_dir . 'entity-badge-row.php' ),
            )
        );
    }

} );


/* ============================================================
   8. CUSTOM BLOCK STYLES — Additional .is-style-* variants
   Registered here so they appear in the block style switcher
   in the editor sidebar without requiring custom JS.
   ============================================================ */
add_action( 'init', function () {

    // Group block: GA Card surface
    register_block_style( 'core/group', array(
        'name'  => 'ga-card',
        'label' => __( 'GA Card', 'good-assumptions' ),
    ) );

    // Group block: GA Card with entity left-bar (color set via custom CSS class)
    register_block_style( 'core/group', array(
        'name'  => 'ga-entity-card',
        'label' => __( 'GA Entity Card', 'good-assumptions' ),
    ) );

    // Paragraph: eyebrow label style
    register_block_style( 'core/paragraph', array(
        'name'  => 'ga-eyebrow',
        'label' => __( 'GA Eyebrow', 'good-assumptions' ),
    ) );

    // Separator: full-bleed hairline
    register_block_style( 'core/separator', array(
        'name'  => 'ga-hairline',
        'label' => __( 'GA Hairline', 'good-assumptions' ),
    ) );

} ); 
/* ============================================================
   9. Optimization suppresion for animation .js
   ============================================================ */

add_filter( 'js_do_concat', function( $do_concat, $handle ) {
    if ( $handle === 'good-assumptions-animations' ) {
        return false;
    }
    return $do_concat;
}, 10, 2 );
