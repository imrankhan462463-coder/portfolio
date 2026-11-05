/* ==========================================================================
   IMRAN — PORTFOLIO
   Concept project data used by demo.html
   --------------------------------------------------------------------------
   Every project below is a concept piece created for this portfolio. The
   demo pages are generated from this file, so copy, colours and code
   excerpts can be edited in one place. There are no real client claims.
   ========================================================================== */
window.AURORA_PROJECTS = {
  lumen: {
    id: "lumen",
    name: "Lumen Studio",
    shortName: "Lumen",
    category: "WordPress · Custom Theme",
    focus: "Brand-led WordPress design",
    stack: "WordPress · PHP · CSS · JavaScript",
    summary: "A studio website concept built around editorial typography, a flexible WordPress editor experience and a calm, confident brand voice.",
    year: "2026",
    theme: {
      mode: "light",
      serif: true,
      bg: "#F3EEE5",
      surface: "#FBF7F0",
      elevated: "#FFFFFF",
      text: "#1A1712",
      muted: "#6B5B49",
      border: "rgba(26,23,18,0.13)",
      accent: "#6D4AFF",
      accent2: "#C9B3FF",
      onAccent: "#FFFFFF",
      radius: "18px",
      pageRadius: "0px"
    },
    brand: "LUMEN®",
    nav: [
      { label: "Work", href: "#work" },
      { label: "Studio", href: "#story" },
      { label: "Approach", href: "#features" },
      { label: "Contact", href: "index.html#contact" }
    ],
    eyebrow: "CREATIVE STUDIO — EST. 2019",
    title: "Brands that feel <em>alive.</em>",
    subtitle: "An independent studio building identities, websites and moments that make people stop scrolling — and start caring.",
    primaryCta: "Explore the work",
    primaryHref: "#work",
    secondaryCta: "Our studio",
    secondaryHref: "#story",
    cover: "assets/images/projects/project-lumen.svg",
    coverAlt: "Lumen Studio concept website preview",
    floatStat: { value: "48", label: "Projects shipped", meta: "since 2019" },
    logos: ["NORTHWIND", "KINFILL", "FORMA", "ARC", "VELA"],
    featuresLabel: "WHAT WE DO",
    featuresTitle: "Strategy, design and a little <em>magic.</em>",
    featuresText: "A considered process that turns a rough idea into a brand people recognise — and a website that earns its keep.",
    features: [
      { no: "01", title: "Brand strategy", text: "Positioning, naming and messaging that give every design decision a reason to exist." },
      { no: "02", title: "Digital design", text: "Editorial websites with confident type, quiet motion and room for the work to breathe." },
      { no: "03", title: "WordPress build", text: "A flexible, editor-friendly build that keeps the studio independent long after launch." }
    ],
    split: {
      label: "THE STUDIO",
      title: "Small team. <em>Big care.</em>",
      text: "Lumen is a two-person studio with a simple belief: the best work happens when strategy and craft sit at the same table. We keep the team small so the thinking stays sharp.",
      points: [
        "Direct access to the people doing the work",
        "A collaborative, no-jargon process",
        "Built to be edited, not locked down"
      ],
      visualLabel: "The Lumen approach",
      visualWord: "Care in every detail.",
      visualMeta: "Est. 2019 · Copenhagen"
    },
    quote: {
      text: "Good design does not shout. It gives people a reason to lean in, and a reason to remember.",
      name: "The Lumen philosophy"
    },
    cta: {
      eyebrow: "HAVE A PROJECT IN MIND?",
      title: "Let's make something <em>worth remembering.</em>",
      text: "Tell us what you are building. We will come back with honest thoughts, a clear scope and a plan that feels achievable.",
      button: "Start a conversation",
      href: "index.html#contact"
    },
    footerNote: "Concept demo · Designed and built by Imran",
    snippet: {
      filename: "inc/project-cards.php",
      language: "php",
      code: `<?php
/**
 * Register the Lumen case-study post type.
 * Kept in a small plugin so the content survives a theme switch.
 */
add_action( 'init', function () {
    register_post_type( 'case_study', array(
        'labels' => array(
            'name'          => __( 'Case Studies', 'lumen' ),
            'singular_name' => __( 'Case Study', 'lumen' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'work' ),
        'menu_icon'    => 'dashicons-portfolio',
        'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'show_in_rest' => true,
    ) );
} );

/**
 * Render a project card with escaped output and a safe image fallback.
 */
function lumen_project_card( WP_Post $post ): void {
    $year = get_post_meta( $post->ID, '_project_year', true );
    ?>
    <article class="project-card">
        <?php if ( has_post_thumbnail( $post ) ) : ?>
            <?php echo get_the_post_thumbnail( $post, 'large', array( 'loading' => 'lazy' ) ); ?>
        <?php endif; ?>
        <h3><?php echo esc_html( get_the_title( $post ) ); ?></h3>
        <?php if ( $year ) : ?>
            <span><?php echo esc_html( $year ); ?></span>
        <?php endif; ?>
    </article>
    <?php
}`
    }
  },

  nomad: {
    id: "nomad",
    name: "Nomad Supply",
    shortName: "Nomad",
    category: "WooCommerce · Storefront",
    focus: "Storefront & checkout",
    stack: "WooCommerce · PHP · CSS · JavaScript",
    summary: "A storefront concept exploring how a considered product story, clear filtering and a frictionless checkout can live in one WooCommerce build.",
    year: "2025",
    theme: {
      mode: "dark",
      serif: false,
      bg: "#051416",
      surface: "#0A2325",
      elevated: "#0D2A2C",
      text: "#F1FFFC",
      muted: "#8FB8B4",
      border: "rgba(255,255,255,0.11)",
      accent: "#21E6B4",
      accent2: "#0E4A4E",
      onAccent: "#05201B",
      radius: "18px",
      pageRadius: "0px"
    },
    brand: "NOMAD",
    nav: [
      { label: "Shop", href: "#work" },
      { label: "Collections", href: "#features" },
      { label: "Story", href: "#story" },
      { label: "Contact", href: "index.html#contact" }
    ],
    eyebrow: "ALPINE SERIES — OUT NOW",
    title: "Built for the <em>long way round.</em>",
    subtitle: "Weatherproof essentials for the trail and everywhere in between. Designed once, repaired forever, made to be used hard.",
    primaryCta: "Shop the series",
    primaryHref: "#work",
    secondaryCta: "Our story",
    secondaryHref: "#story",
    cover: "assets/images/projects/project-nomad.svg",
    coverAlt: "Nomad Supply concept storefront preview",
    floatStat: { value: "4.9", label: "Customer rating", meta: "2,480 reviews" },
    logos: ["FIELD NOTES", "TRAIL CO", "ALPINE CLUB", "NORTH 45", "BASE CAMP"],
    featuresLabel: "WHY NOMAD",
    featuresTitle: "Less gear. <em>More outside.</em>",
    featuresText: "A storefront built around clarity: the right products, the right details, and a checkout that gets out of the way.",
    features: [
      { no: "01", title: "Smart filtering", text: "Find the right layer for the conditions in seconds, with filters that understand how people actually shop." },
      { no: "02", title: "One-page checkout", text: "A fast, secure Stripe flow with clear delivery options and zero surprise fees." },
      { no: "03", title: "Built to scale", text: "A lean WooCommerce foundation that can grow into new collections without slowing down." }
    ],
    split: {
      label: "THE NOMAD WAY",
      title: "Buy once. <em>Keep it for years.</em>",
      text: "Every piece is designed to be repaired, re-proofed and passed on. The site makes that promise tangible through honest product pages and a lifetime repair programme.",
      points: [
        "Carbon-neutral delivery on every order",
        "Lifetime repairs, no questions asked",
        "Small runs, traceable materials"
      ],
      visualLabel: "Lifetime repairs",
      visualWord: "Built to be used, not replaced.",
      visualMeta: "Est. 2018 · Reykjavík"
    },
    quote: {
      text: "The best outdoor gear is the gear you forget you are wearing. That is the standard we hold every product to.",
      name: "The Nomad design brief"
    },
    cta: {
      eyebrow: "NEW SEASON, NEW TRAILS",
      title: "Pack lighter. <em>Go further.</em>",
      text: "The Alpine Series is a limited run of 120 pieces. When they are gone, they are gone — but they will last a lifetime.",
      button: "Explore the collection",
      href: "index.html#contact"
    },
    footerNote: "Concept demo · Designed and built by Imran",
    snippet: {
      filename: "inc/woocommerce-extras.php",
      language: "php",
      code: `<?php
/**
 * Add a small "repaired for life" badge to marked products.
 */
add_action( 'woocommerce_before_shop_loop_item_title', function () {
    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $repairable = (bool) get_post_meta( $product->get_id(), '_nomad_repairable', true );

    if ( ! $repairable ) {
        return;
    }

    printf(
        '<span class="product-badge">%s</span>',
        esc_html__( 'Lifetime repairs', 'nomad' )
    );
}, 15 );

/**
 * Keep the cart light: remove unused WooCommerce assets on non-shop pages.
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( is_front_page() ) {
        wp_dequeue_style( 'woocommerce-general' );
    }
}, 20 );`
    }
  },

  pulseboard: {
    id: "pulseboard",
    name: "Pulseboard",
    shortName: "Pulseboard",
    category: "SaaS · Product UI",
    focus: "Product UI & data design",
    stack: "HTML · CSS · JavaScript · SVG",
    summary: "A product interface concept that turns dense analytics into a calm, keyboard-friendly dashboard without relying on a charting library.",
    year: "2025",
    theme: {
      mode: "dark",
      serif: false,
      bg: "#080B18",
      surface: "#0C1224",
      elevated: "#111731",
      text: "#F4F2FF",
      muted: "#8B91AE",
      border: "rgba(255,255,255,0.1)",
      accent: "#7C5CFF",
      accent2: "#45E0B7",
      onAccent: "#FFFFFF",
      radius: "18px",
      pageRadius: "0px"
    },
    brand: "pulseboard",
    nav: [
      { label: "Product", href: "#features" },
      { label: "Why it works", href: "#story" },
      { label: "Metrics", href: "#work" },
      { label: "Contact", href: "index.html#contact" }
    ],
    eyebrow: "ANALYTICS THAT CALM THE NOISE",
    title: "Every metric.<br>One <em>calm place.</em>",
    subtitle: "Pulseboard brings revenue, activation and retention into a single view — so your team can stop reporting and start deciding.",
    primaryCta: "Start a free trial",
    primaryHref: "#work",
    secondaryCta: "See the interface",
    secondaryHref: "#story",
    cover: "assets/images/projects/project-pulseboard.svg",
    coverAlt: "Pulseboard concept analytics dashboard preview",
    floatStat: { value: "99.99%", label: "Uptime", meta: "last 12 months" },
    logos: ["NORTHSTAR", "ACME", "FIELDWORK", "LOOP", "KINSHIP"],
    featuresLabel: "THE PRODUCT",
    featuresTitle: "Less reporting. <em>More deciding.</em>",
    featuresText: "A focused interface for teams who need the signal, not another spreadsheet. Fast by default, clear by design.",
    features: [
      { no: "01", title: "One shared truth", text: "Revenue, activation and retention in one place, with the same definitions across every team." },
      { no: "02", title: "Built for focus", text: "Quiet dashboards that surface what changed and why, instead of drowning you in charts." },
      { no: "03", title: "Instant answers", text: "Filter, segment and drill down with keyboard-first controls and sub-second feedback." }
    ],
    split: {
      label: "WHY PULSEBOARD",
      title: "Clarity is a <em>feature.</em>",
      text: "The product was designed around a simple question: what decision does this screen help someone make? Every chart earns its place, or it does not ship.",
      points: [
        "Sub-second queries on large datasets",
        "Keyboard-first navigation",
        "SOC 2-ready security posture"
      ],
      visualLabel: "The interface",
      visualWord: "A dashboard you can actually read.",
      visualMeta: "Designed for product teams"
    },
    quote: {
      text: "Data should create confidence, not anxiety. The best dashboard is the one you understand before you finish reading the first number.",
      name: "The Pulseboard product principle"
    },
    cta: {
      eyebrow: "14 DAYS, NO CARD",
      title: "See what your data has been <em>trying to say.</em>",
      text: "Set up a workspace in minutes. Invite your team, connect a source and get your first clear answer before the coffee goes cold.",
      button: "Start a free trial",
      href: "index.html#contact"
    },
    footerNote: "Concept demo · Designed and built by Imran",
    snippet: {
      filename: "assets/js/dashboard.js",
      language: "js",
      code: `/**
 * Render a lightweight, accessible revenue sparkline.
 * No chart library: just SVG, so the dashboard stays fast.
 */
const pointCount = 24;
const maxValue = Math.max(...metrics.map((point) => point.value));

const points = metrics
  .slice(-pointCount)
  .map((point, index) => {
    const x = (index / (pointCount - 1)) * 100;
    const y = 100 - (point.value / maxValue) * 100;
    return \`\${x.toFixed(2)},\${y.toFixed(2)}\`;
  })
  .join(" ");

const sparkline = document.querySelector("[data-sparkline]");

if (sparkline) {
  sparkline.setAttribute("points", points);
  sparkline.closest("figure").setAttribute(
    "aria-label",
    \`Monthly recurring revenue: \${formatCurrency(metrics.at(-1).value)}\`
  );
}

const lastPoint = metrics.at(-1);
if (lastPoint) {
  document.querySelector("[data-mrr]").textContent =
    formatCurrency(lastPoint.value);
}`
    }
  },

  verdant: {
    id: "verdant",
    name: "Verdant",
    shortName: "Verdant",
    category: "Custom Theme · Brand",
    focus: "Retail & content design",
    stack: "WordPress · PHP · CSS · JavaScript",
    summary: "A botanical skincare concept balancing rich product storytelling with a simple, refill-friendly shopping experience.",
    year: "2025",
    theme: {
      mode: "light",
      serif: true,
      bg: "#F3F7EE",
      surface: "#E9F0E1",
      elevated: "#FFFFFF",
      text: "#183D2A",
      muted: "#5A7058",
      border: "rgba(24,61,42,0.14)",
      accent: "#4F7B4C",
      accent2: "#A9C293",
      onAccent: "#F6F8F1",
      radius: "18px",
      pageRadius: "0px"
    },
    brand: "VERDANT",
    nav: [
      { label: "Shop", href: "#work" },
      { label: "Rituals", href: "#features" },
      { label: "Our roots", href: "#story" },
      { label: "Contact", href: "index.html#contact" }
    ],
    eyebrow: "BOTANICAL SKINCARE — SMALL BATCH",
    title: "Rooted in <em>better care.</em>",
    subtitle: "Plant-powered formulas for skin that thrives. Nothing hidden, nothing wasted, nothing you do not need.",
    primaryCta: "Explore products",
    primaryHref: "#work",
    secondaryCta: "Our ingredients",
    secondaryHref: "#story",
    cover: "assets/images/projects/project-verdant.svg",
    coverAlt: "Verdant concept skincare website preview",
    floatStat: { value: "100%", label: "Certified organic", meta: "COSMOS standard" },
    logos: ["FOREST DEW", "MOSS & CO", "BOTANICA", "EARTH NOTE", "WILD ROOT"],
    featuresLabel: "THE RITUAL",
    featuresTitle: "A slower kind of <em>skincare.</em>",
    featuresText: "Formulas built around what your skin actually needs, with ingredients you can trace back to the field.",
    features: [
      { no: "01", title: "Traceable botanicals", text: "Every ingredient is sourced from growers we know, with a short supply chain and no filler." },
      { no: "02", title: "Small-batch care", text: "Made in small runs to keep potency high and waste low. Freshness you can feel." },
      { no: "03", title: "Refill, not landfill", text: "Returnable glass and refill pouches designed to keep packaging out of the waste stream." }
    ],
    split: {
      label: "OUR ROOTS",
      title: "What goes on your skin <em>matters.</em>",
      text: "Verdant began with a simple frustration: too many products, too little honesty. The site was designed to make the ingredient story as beautiful as the product itself.",
      points: [
        "Certified organic, cruelty-free formulas",
        "Recyclable glass and refill pouches",
        "1% of revenue back to soil health"
      ],
      visualLabel: "From field to face",
      visualWord: "Nothing hidden. Nothing wasted.",
      visualMeta: "Est. 2020 · Somerset"
    },
    quote: {
      text: "The healthiest skin is not a trend. It is a daily ritual built on patience, good ingredients and a little less noise.",
      name: "The Verdant formulation note"
    },
    cta: {
      eyebrow: "FIND YOUR RITUAL",
      title: "Better skin starts with <em>better ingredients.</em>",
      text: "Take the two-minute ritual finder and build a routine that fits your skin, your values and your morning.",
      button: "Find my ritual",
      href: "index.html#contact"
    },
    footerNote: "Concept demo · Designed and built by Imran",
    snippet: {
      filename: "functions.php",
      language: "php",
      code: `<?php
/**
 * Verdant theme setup — intentionally small and dependency-free.
 */
add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'verdant', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );

    add_image_size( 'verdant-product', 720, 900, true );

    register_nav_menus( array(
        'primary' => __( 'Primary navigation', 'verdant' ),
    ) );
} );

/**
 * Front-end assets, versioned by filemtime for cache busting.
 */
add_action( 'wp_enqueue_scripts', function () {
    $theme = wp_get_theme();

    wp_enqueue_style(
        'verdant',
        get_stylesheet_uri(),
        array(),
        $theme->get( 'Version' )
    );

    wp_enqueue_script(
        'verdant',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        $theme->get( 'Version' ),
        true
    );
} );`
    }
  },

  framecast: {
    id: "framecast",
    name: "FrameCast",
    shortName: "FrameCast",
    category: "WordPress Plugin · Product",
    focus: "WordPress admin tooling",
    stack: "WordPress · PHP · REST API · JavaScript",
    summary: "A plugin interface concept for creative teams, focused on batch export workflows, clear admin UI and capability-safe endpoints.",
    year: "2024",
    theme: {
      mode: "dark",
      serif: false,
      bg: "#08060F",
      surface: "#120E1E",
      elevated: "#171222",
      text: "#F4F1FF",
      muted: "#8F88A8",
      border: "rgba(255,255,255,0.1)",
      accent: "#F472B6",
      accent2: "#22D3EE",
      onAccent: "#240B16",
      radius: "18px",
      pageRadius: "0px"
    },
    brand: "FrameCast",
    nav: [
      { label: "Dashboard", href: "#work" },
      { label: "Workflow", href: "#features" },
      { label: "Why FrameCast", href: "#story" },
      { label: "Contact", href: "index.html#contact" }
    ],
    eyebrow: "WORDPRESS PLUGIN — V2.4",
    title: "Frames that <em>ship faster.</em>",
    subtitle: "A focused WordPress plugin that turns a messy export workflow into one clear action. Batch it, name it, export it — done.",
    primaryCta: "Open the dashboard",
    primaryHref: "#work",
    secondaryCta: "See the workflow",
    secondaryHref: "#features",
    cover: "assets/images/projects/project-framecast.svg",
    coverAlt: "FrameCast concept plugin dashboard preview",
    floatStat: { value: "42%", label: "Faster exports", meta: "in the first month" },
    logos: ["STUDIO NINE", "MONO", "KINFOLK", "MADE", "NORTH"],
    featuresLabel: "THE WORKFLOW",
    featuresTitle: "One click. <em>Every format.</em>",
    featuresText: "Built for creative teams who need consistent exports without memorising a manual. It fits inside the WordPress admin they already know.",
    features: [
      { no: "01", title: "Batch exports", text: "Select a collection, choose your sizes and export every frame in a single action." },
      { no: "02", title: "Preset naming", text: "Consistent file names and folder structures, saved as reusable project presets." },
      { no: "03", title: "Zero lock-in", text: "Your frames stay in WordPress. Deactivate the plugin and nothing disappears." }
    ],
    split: {
      label: "WHY IT EXISTS",
      title: "The boring work, <em>handled.</em>",
      text: "FrameCast started as an internal tool for a design studio that was losing hours to manual exports. It is now a clean, focused plugin with one job: get finished work out the door.",
      points: [
        "Native WordPress admin UI",
        "REST API for automation",
        "Secure, capability-checked endpoints"
      ],
      visualLabel: "The result",
      visualWord: "More time for the work that matters.",
      visualMeta: "Built for creative teams"
    },
    quote: {
      text: "The best tools disappear into the work. If you have to think about the export, the tool is not finished yet.",
      name: "The FrameCast build note"
    },
    cta: {
      eyebrow: "READY TO SHIP?",
      title: "Give your exports <em>their afternoon back.</em>",
      text: "FrameCast is a concept product exploring how far a focused WordPress plugin can go. Start a conversation if you have a workflow worth fixing.",
      button: "Talk about a plugin",
      href: "index.html#contact"
    },
    footerNote: "Concept demo · Designed and built by Imran",
    snippet: {
      filename: "includes/rest-export.php",
      language: "php",
      code: `<?php
/**
 * Register a capability-checked REST endpoint for batch exports.
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'framecast/v1', '/export', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'framecast_handle_export',
        'permission_callback' => function () {
            return current_user_can( 'upload_files' );
        },
        'args'                => array(
            'frame_ids' => array(
                'required'          => true,
                'type'              => 'array',
                'items'             => array( 'type' => 'integer' ),
                'sanitize_callback' => function ( $ids ) {
                    return array_map( 'absint', (array) $ids );
                },
            ),
        ),
    ) );
} );

function framecast_handle_export( WP_REST_Request $request ) {
    $ids = $request->get_param( 'frame_ids' );

    if ( empty( $ids ) ) {
        return new WP_Error(
            'framecast_empty',
            __( 'Choose at least one frame.', 'framecast' ),
            array( 'status' => 400 )
        );
    }

    $exported = array();

    foreach ( $ids as $id ) {
        if ( 'frame' !== get_post_type( $id ) ) {
            continue;
        }
        $exported[] = framecast_render_frame( $id );
    }

    return rest_ensure_response( array(
        'count' => count( $exported ),
        'files' => $exported,
    ) );
}`
    }
  },

  atelier: {
    id: "atelier",
    name: "Atelier North",
    shortName: "Atelier",
    category: "Editorial · Art Direction",
    focus: "Editorial art direction",
    stack: "WordPress · PHP · CSS · JavaScript",
    summary: "An editorial concept for a furniture studio, using generous type, quiet motion and a gallery rhythm that lets the objects lead.",
    year: "2024",
    theme: {
      mode: "light",
      serif: true,
      bg: "#F4EEE6",
      surface: "#FBF6EE",
      elevated: "#FFFFFF",
      text: "#241D17",
      muted: "#6F6255",
      border: "rgba(36,29,23,0.14)",
      accent: "#B45309",
      accent2: "#E3C6A4",
      onAccent: "#F7F2EA",
      radius: "18px",
      pageRadius: "0px"
    },
    brand: "ATELIER NORTH",
    nav: [
      { label: "Work", href: "#work" },
      { label: "Studio", href: "#story" },
      { label: "Objects", href: "#features" },
      { label: "Contact", href: "index.html#contact" }
    ],
    eyebrow: "INTERIOR OBJECTS — COPENHAGEN",
    title: "Objects with a <em>quiet presence.</em>",
    subtitle: "A considered collection of handcrafted furniture and objects for the modern home — made slowly, kept for decades.",
    primaryCta: "View the collection",
    primaryHref: "#work",
    secondaryCta: "Our philosophy",
    secondaryHref: "#story",
    cover: "assets/images/projects/project-atelier.svg",
    coverAlt: "Atelier North concept editorial website preview",
    floatStat: { value: "14", label: "Years of craft", meta: "Copenhagen · New York" },
    logos: ["NORDHAVN", "FORMA", "KINFILL", "STONE 07", "OAK SERIES"],
    featuresLabel: "THE COLLECTION",
    featuresTitle: "Made to be <em>lived with.</em>",
    featuresText: "Furniture with honest materials and proportions that age well. Designed in Copenhagen, made by people who care about the join.",
    features: [
      { no: "01", title: "Honest materials", text: "Solid oak, untreated stone and wool chosen for how they wear, not just how they look on day one." },
      { no: "02", title: "Slow production", text: "Small runs made with a handful of workshops we have worked with for over a decade." },
      { no: "03", title: "Kept for decades", text: "Every piece can be repaired, re-oiled and passed on. Good design should outlast the trend cycle." }
    ],
    split: {
      label: "THE ATELIER",
      title: "A quieter way to <em>make things.</em>",
      text: "Atelier North is a design studio and workshop built around a simple idea: fewer, better objects. The website gives the work room to breathe, with an editorial rhythm that feels like walking through the studio.",
      points: [
        "Designed and made in small batches",
        "Traceable, responsibly sourced materials",
        "Repair service for every piece"
      ],
      visualLabel: "The studio",
      visualWord: "Fewer objects. Better rooms.",
      visualMeta: "Est. 2014 · Copenhagen"
    },
    quote: {
      text: "A room should feel collected, not decorated. The objects that stay are the ones that earn their place over time.",
      name: "The Atelier North design note"
    },
    cta: {
      eyebrow: "VISIT THE STUDIO",
      title: "Come see the work <em>in person.</em>",
      text: "The Copenhagen showroom is open by appointment. Bring a floor plan, a question or just a curiosity about how things are made.",
      button: "Book a visit",
      href: "index.html#contact"
    },
    footerNote: "Concept demo · Designed and built by Imran",
    snippet: {
      filename: "assets/js/editorial.js",
      language: "js",
      code: `/**
 * Editorial reveal: progressively enhance content with a quiet fade.
 * Content stays visible when JS is unavailable or motion is reduced.
 */
const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
const pieces = document.querySelectorAll("[data-reveal]");

if (!reduceMotion.matches && "IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.14, rootMargin: "0px 0px -8% 0px" }
  );

  pieces.forEach((piece, index) => {
    piece.style.setProperty("--delay", \`\${Math.min(index * 60, 360)}ms\`);
    observer.observe(piece);
  });
}

/**
 * Keep the current section visible in the minimal top navigation.
 */
const sections = [...document.querySelectorAll("main section[id]")];
const navLinks = [...document.querySelectorAll("[data-section-link]")];

const syncNavigation = () => {
  const marker = window.scrollY + window.innerHeight * 0.35;
  let current = sections[0];

  sections.forEach((section) => {
    if (section.offsetTop <= marker) current = section;
  });

  navLinks.forEach((link) => {
    const isCurrent = link.hash === \`#\${current.id}\`;
    link.toggleAttribute("aria-current", isCurrent);
  });
};

document.addEventListener("scroll", syncNavigation, { passive: true });
syncNavigation();`
    }
  }
};
