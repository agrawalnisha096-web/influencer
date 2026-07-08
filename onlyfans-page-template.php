<?php
/**
 * Template Name: OnlyFans Cluster
 * 
 * Custom page template for all pages under /onlyfans/
 * Suppresses global Bloghash theme header and footer.
 * Injects standalone OnlyFans cluster nav and footer.
 * 
 * HOW TO INSTALL:
 * 1. Upload this file to /wp-content/themes/bloghash/ (or your active child theme)
 * 2. In WordPress admin, go to Pages → any OnlyFans page → Page Attributes
 * 3. Under "Template", select "OnlyFans Cluster"
 * 4. Apply to: hub page, asian OF page, all future /onlyfans/ sub-pages
 * 
 * CONTACT FORM SETUP (Contact Form 7):
 * 1. Install Contact Form 7 plugin (free, from WP plugin directory)
 * 2. Create a new form — see field list in onlyfans-submit.html
 * 3. Copy the shortcode e.g. [contact-form-7 id="123"]
 * 4. Paste into the submit page where indicated below
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>

<?php
/**
 * ── Dynamic SEO Schema System ────────────────────────────────────────────
 *
 * Fully automatic — no template edits needed when publishing new pages.
 *
 * HOW IT WORKS:
 * 1. article:modified_time fires on every page (dynamic from WP)
 * 2. WebPage + BreadcrumbList fires on every page using WP data
 * 3. ItemList fires only on the hub page — update $hub_pages array
 *    when a new page goes live (one line per page, no other changes)
 * 4. FAQPage — pulled from a custom field on each page (see below)
 *
 * TO ADD A NEW PAGE (only change needed in this file):
 * Find $hub_pages array below and add one line:
 *   'Page Title' => home_url('/your-page-slug/'),
 * That's it. All other schemas generate automatically.
 *
 * FAQ SCHEMA SETUP (optional, per page):
 * Install "Custom Fields" or use RankMath's FAQ block.
 * Or add FAQs via a custom field named 'of_faqs' as JSON:
 * [{"q":"Question?","a":"Answer."},{"q":"Q2?","a":"A2."}]
 * The system picks it up automatically.
 */

// ── Core page data — all from WordPress, no hardcoding ───────────────────
$site_url    = home_url();
$page_url    = get_permalink();
$page_title  = get_the_title();
$page_date   = get_the_modified_date('c');
$page_desc   = get_the_excerpt() ?: wp_strip_all_tags(get_the_content());
$page_desc   = wp_trim_words($page_desc, 30, '');

// ── Hub page registry — FULLY AUTOMATIC ─────────────────────────────────
// Queries WordPress for all published pages using the OnlyFans Cluster
// template. No manual updates needed — publish a page and it appears.
// The hub page itself is excluded from the list.

$hub_pages = [];

$cluster_pages = get_pages([
    'meta_key'    => '_wp_page_template',
    'meta_value'  => 'onlyfans-page-template.php',
    'post_status' => 'publish',
    'sort_column' => 'menu_order,post_title',
]);

foreach ($cluster_pages as $p) {
    // Skip the hub page itself and the submit page
    $p_slug = basename(get_permalink($p->ID));
    if ($p->post_name === 'onlyfans' || $p->post_name === 'submit-profile') {
        continue;
    }
    $hub_pages[$p->post_title] = get_permalink($p->ID);
}

// ── Build breadcrumb for current page ────────────────────────────────────
$breadcrumb_items = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',
     'item' => esc_url($site_url)],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'OnlyFans Influencers',
     'item' => esc_url(home_url('/onlyfans/'))],
];

// Add current page as final breadcrumb item (skip for hub itself)
if (!is_page('onlyfans')) {
    $breadcrumb_items[] = [
        '@type'    => 'ListItem',
        'position' => 3,
        'name'     => $page_title,
        'item'     => esc_url($page_url),
    ];
}

// ── Build ItemList for hub page ───────────────────────────────────────────
$item_list_elements = [];
$pos = 1;
foreach ($hub_pages as $name => $url) {
    $item_list_elements[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'name'     => $name,
        'url'      => esc_url($url),
    ];
}

// ── Check for FAQ custom field ────────────────────────────────────────────
$raw_faqs  = get_post_meta(get_the_ID(), 'of_faqs', true);
$faq_items = [];
if ($raw_faqs) {
    $decoded = json_decode($raw_faqs, true);
    if (is_array($decoded)) {
        foreach ($decoded as $faq) {
            if (!empty($faq['q']) && !empty($faq['a'])) {
                $faq_items[] = [
                    '@type'          => 'Question',
                    'name'           => esc_html($faq['q']),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => esc_html($faq['a']),
                    ],
                ];
            }
        }
    }
}

// ── Hub-page hardcoded FAQs (no custom field needed for hub) ──────────────
if (is_page('onlyfans')) {
    $faq_items = [
        ['@type'=>'Question','name'=>'Who are the best OnlyFans influencers in 2026?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'By total likes, the leading accounts are PeachJars (9.5M+ likes, free), Ginny Potter (9.4M+ likes, free) and Skylar Mae (6.3M+ likes). Rankings are updated monthly from verified profile data.']],
        ['@type'=>'Question','name'=>'Which OnlyFans influencers are completely free to follow?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'The highest-liked free OnlyFans accounts include PeachJars (9.5M likes), Ginny Potter (9.4M likes), Jessica Nigri (6.6M likes) and Brynn Woods (3.7M likes). Most run a free follow plus pay-per-view model.']],
        ['@type'=>'Question','name'=>'Who are the highest paid OnlyFans influencers?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Top earners include Sophie Rain ($95M+ lifetime), Bhad Bhabie ($71M+), Belle Delphine ($30M+ per year) and Amouranth ($57M+ lifetime). Blac Chyna and Corinna Kopf have retired from the platform.']],
        ['@type'=>'Question','name'=>'How does The Influencers Network rank OnlyFans influencers?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Every profile is ranked using a weighted value score: total likes (50%), content volume in photos and videos (30%), posting activity (20%), and a price-accessibility adjustment. Stats are re-verified monthly.']],
        ['@type'=>'Question','name'=>'What is the most liked OnlyFans account?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'PeachJars (@peachjars) holds the most total likes at 9.5M+ on a free subscription. Among paid accounts, Skylar Mae leads at 6.3M likes on a $30/month subscription.']],
        ['@type'=>'Question','name'=>'How often is this directory updated?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'All stats are re-pulled from live OnlyFans profiles every month. Influencers inactive for 60+ days are flagged and removed from rankings.']],
        ['@type'=>'Question','name'=>'Which celebrity OnlyFans accounts are still active in 2026?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Active celebrity accounts include Bhad Bhabie, Belle Delphine, Tana Mongeau, Angela White, Mia Khalifa, Paige VanZant and Kerry Katona. Retired accounts include Blac Chyna (2023), Iggy Azalea (2024) and Cardi B (2020).']],
        ['@type'=>'Question','name'=>'Are there OnlyFans influencers who post daily?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Yes — the most active creators post 100-164 times per month. Sofia Silk leads with approximately 164 posts per month on a free subscription. Eva Sky averages 138 posts per month, also free.']],
    ];
}

// ── Submit page hardcoded FAQs ────────────────────────────────────────────
if (is_page('submit-profile')) {
    $faq_items = [
        ['@type'=>'Question','name'=>'How long does it take to get listed after submitting?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Free submissions are reviewed within 30 days and included in the next monthly update if accepted. Featured submissions are reviewed within 72 hours.']],
        ['@type'=>'Question','name'=>'Is submitting an OnlyFans profile free?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Yes — standard submissions are free. Featured placements start from $75/month per creator for agencies wanting priority review and highlighted positioning.']],
        ['@type'=>'Question','name'=>'Is a free submission guaranteed to be listed?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'No — free submissions are reviewed at editorial discretion. Profiles must be active, have sufficient content, and fit the relevant category ranking.']],
        ['@type'=>'Question','name'=>'Can agencies submit multiple creators?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Yes — agencies managing 5 or more creators can use the Agency bulk option. Custom pricing starts at $40-50 per creator per month for rosters of 5 or more.']],
        ['@type'=>'Question','name'=>'How do I request removal of a profile?',
         'acceptedAnswer'=>['@type'=>'Answer','text'=>'Use the submission form, select Other as the category, and note Removal request in the notes field along with the profile URL. Processed within 7 business days.']],
    ];
}
?>

<!-- Article modified time — Google freshness signal, auto-updates on WP save -->
<meta property="article:modified_time" content="<?php echo esc_attr($page_date); ?>">

<!-- WebPage + BreadcrumbList — fires on every page automatically -->
<script type="application/ld+json">
<?php echo wp_json_encode([
    '@context'  => 'https://schema.org',
    '@type'     => 'WebPage',
    'name'      => $page_title,
    'url'       => esc_url($page_url),
    'dateModified' => $page_date,
    'inLanguage'   => 'en-US',
    'publisher' => [
        '@type' => 'Organization',
        'name'  => 'The Influencers Network',
        'url'   => esc_url($site_url),
    ],
    'breadcrumb' => [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $breadcrumb_items,
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
</script>

<?php if (is_page('onlyfans')) : ?>
<!-- ItemList — hub page only, auto-built from $hub_pages array above -->
<script type="application/ld+json">
<?php echo wp_json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => 'OnlyFans Influencer Rankings by Category',
    'description'     => 'Complete directory of OnlyFans influencer rankings by category, demographic, price and content type.',
    'url'             => esc_url(home_url('/onlyfans/')),
    'itemListElement' => $item_list_elements,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
</script>
<?php endif; ?>

<?php if (!empty($faq_items)) : ?>
<!-- FAQPage — fires when FAQ data exists (hub/submit: hardcoded; others: of_faqs custom field) -->
<script type="application/ld+json">
<?php echo wp_json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $faq_items,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
</script>
<?php endif; ?>


<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ── OnlyFans Cluster Design System ─────────────────────────────────── */
:root{
  --of-bg:#0c0c0f;--of-bg-card:#131318;--of-bg-hover:#18181f;--of-bg-subtle:#1a1a24;
  --of-border:rgba(255,255,255,0.07);--of-border-accent:rgba(255,255,255,0.14);
  --of-text:#f0eee8;--of-text-2:#9997a0;--of-text-3:#5a5866;
  --of-accent:#e8426a;--of-accent-soft:rgba(232,66,106,0.12);
  --of-gold:#d4a853;--of-gold-soft:rgba(212,168,83,0.12);
  --of-green:#3ecf8e;--of-green-soft:rgba(62,207,142,0.10);
  --of-blue:#5b9cf6;
  --of-font-display:'Playfair Display',Georgia,serif;
  --of-font-body:'DM Sans',sans-serif;
  --of-font-mono:'DM Mono',monospace;
  --of-radius-sm:6px;--of-radius-md:12px;--of-radius-lg:18px;--of-radius-xl:24px;
}

/* Reset global theme styles for this template */
body.of-cluster {
  background: var(--of-bg) !important;
  color: var(--of-text) !important;
  font-family: var(--of-font-body) !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* Hide ALL global theme elements */
body.of-cluster #masthead,
body.of-cluster .site-header,
body.of-cluster header.site-header,
body.of-cluster #site-header,
body.of-cluster .main-navigation,
body.of-cluster #colophon,
body.of-cluster .site-footer,
body.of-cluster footer.site-footer,
body.of-cluster #footer,
body.of-cluster .breadcrumb-trail,
body.of-cluster .widget-area,
body.of-cluster #secondary,
body.of-cluster .sidebar { display: none !important; }

/* ── Cluster Sticky Nav ───────────────────────────────────────────── */
.of-nav {
  position: sticky;
  top: 0;
  background: rgba(12,12,15,0.95);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--of-border);
  z-index: 9999;
  padding: 0 24px;
  font-family: var(--of-font-body);
}
.of-nav-inner {
  max-width: 960px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 54px;
  gap: 16px;
}
.of-nav-logo {
  font-family: var(--of-font-display);
  font-size: 15px;
  font-weight: 700;
  color: var(--of-text);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
}
.of-nav-logo .of-dot { color: var(--of-accent); }
.of-nav-logo .of-site-name {
  font-size: 11px;
  font-family: var(--of-font-mono);
  color: var(--of-text-3);
  font-weight: 400;
}
.of-nav-links {
  display: flex;
  align-items: center;
  gap: 2px;
  flex: 1;
  justify-content: center;
}
.of-nav-link {
  font-size: 12px;
  font-family: var(--of-font-mono);
  color: var(--of-text-2);
  padding: 5px 11px;
  border-radius: var(--of-radius-sm);
  text-decoration: none;
  transition: color .2s, background .2s;
  white-space: nowrap;
}
.of-nav-link:hover { color: var(--of-text); background: var(--of-bg-subtle); text-decoration: none; }
.of-nav-link.of-active { color: var(--of-accent); }
.of-nav-link.of-new {
  position: relative;
}
.of-nav-link.of-new::after {
  content: 'new';
  font-size: 8px;
  background: var(--of-accent);
  color: #fff;
  padding: 1px 4px;
  border-radius: 3px;
  position: absolute;
  top: -2px;
  right: -2px;
  letter-spacing: .04em;
  text-transform: uppercase;
}
.of-nav-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}
.of-nav-submit {
  font-size: 11px;
  font-family: var(--of-font-mono);
  color: #fff;
  background: var(--of-accent);
  padding: 5px 14px;
  border-radius: 100px;
  text-decoration: none;
  transition: opacity .2s;
  white-space: nowrap;
  letter-spacing: .03em;
}
.of-nav-submit:hover { opacity: .85; text-decoration: none; color: #fff; }
.of-nav-home {
  font-size: 11px;
  font-family: var(--of-font-mono);
  color: var(--of-text-3);
  text-decoration: none;
  padding: 5px 8px;
  border-radius: var(--of-radius-sm);
  transition: color .2s;
}
.of-nav-home:hover { color: var(--of-text-2); text-decoration: none; }

/* Mobile nav */
@media(max-width:700px) {
  .of-nav-links { display: none; }
  .of-nav-logo .of-site-name { display: none; }
}
@media(max-width:480px) {
  .of-nav-home { display: none; }
}

/* ── Progress bar ─────────────────────────────────────────────────── */
.of-progress {
  position: fixed;
  top: 0;
  left: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--of-accent), var(--of-gold));
  z-index: 10000;
  width: 0%;
  transition: width .1s linear;
}

/* ── Cluster Footer ───────────────────────────────────────────────── */
.of-footer {
  border-top: 1px solid var(--of-border);
  padding: 40px 24px;
  font-family: var(--of-font-body);
  background: var(--of-bg);
}
.of-footer-inner {
  max-width: 960px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 40px;
}
.of-footer-brand { }
.of-footer-logo {
  font-family: var(--of-font-display);
  font-size: 18px;
  font-weight: 700;
  color: var(--of-text);
  margin-bottom: 10px;
}
.of-footer-logo span { color: var(--of-accent); }
.of-footer-desc {
  font-size: 13px;
  color: var(--of-text-2);
  line-height: 1.7;
  max-width: 300px;
  margin-bottom: 16px;
}
.of-footer-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 10px;
  font-family: var(--of-font-mono);
  color: var(--of-text-3);
  border: 1px solid var(--of-border);
  padding: 3px 10px;
  border-radius: 100px;
}
.of-footer-col-title {
  font-size: 11px;
  font-family: var(--of-font-mono);
  color: var(--of-text-3);
  text-transform: uppercase;
  letter-spacing: .1em;
  margin-bottom: 14px;
}
.of-footer-links { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.of-footer-links a {
  font-size: 13px;
  color: var(--of-text-2);
  text-decoration: none;
  transition: color .2s;
}
.of-footer-links a:hover { color: var(--of-accent); }
.of-footer-bottom {
  max-width: 960px;
  margin: 32px auto 0;
  padding-top: 24px;
  border-top: 1px solid var(--of-border);
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}
.of-footer-disclaimer {
  font-size: 11px;
  color: var(--of-text-3);
  line-height: 1.7;
  max-width: 580px;
}
.of-footer-copy {
  font-size: 11px;
  font-family: var(--of-font-mono);
  color: var(--of-text-3);
  white-space: nowrap;
}
@media(max-width:700px) {
  .of-footer-inner { grid-template-columns: 1fr; gap: 28px; }
  .of-footer-bottom { flex-direction: column; }
}

/* ── Page content wrapper ─────────────────────────────────────────── */
.of-page-content {
  font-family: var(--of-font-body);
  background: var(--of-bg);
  min-height: 60vh;
}
</style>
</head>

<body <?php body_class('of-cluster'); ?>>

<!-- Progress bar -->
<div class="of-progress" id="ofProgress"></div>

<!-- ── OnlyFans Cluster Navigation ─────────────────────────────────── -->
<nav class="of-nav" id="ofNav" aria-label="OnlyFans directory navigation">
  <div class="of-nav-inner">

    <!-- Logo -->
    <a href="<?php echo home_url('/onlyfans/'); ?>" class="of-nav-logo" aria-label="OnlyFans Influencers Directory">
      <span>TIN</span>
      <span class="of-dot">·</span>
      <span>OnlyFans</span>
      <span class="of-site-name">by theinfluencersnetwork.com</span>
    </a>

    <!-- Nav links — add/remove as pages go live -->
    <div class="of-nav-links" role="list">
      <a href="<?php echo home_url('/onlyfans/'); ?>" class="of-nav-link <?php echo is_page('onlyfans') ? 'of-active' : ''; ?>">Directory</a>
      <a href="<?php echo home_url('/best-asian-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('best-asian-onlyfans-influencers') ? 'of-active' : ''; ?>">Asian</a>
      <a href="<?php echo home_url('/best-onlyfans-agencies/'); ?>" class="of-nav-link <?php echo is_page('best-onlyfans-agencies') ? 'of-active' : ''; ?>">Agencies</a>

      <?php
      /* ── UNCOMMENT AS PAGES GO LIVE ──────────────────────────────────
      // Week 1 launches:
      ?>
      <a href="<?php echo home_url('/onlyfans/best-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('best-onlyfans-influencers') ? 'of-active' : ''; ?>">Best</a>
      <a href="<?php echo home_url('/onlyfans/free-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('free-onlyfans-influencers') ? 'of-active' : ''; ?>">Free</a>
      <a href="<?php echo home_url('/onlyfans/highest-paid-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('highest-paid-onlyfans-influencers') ? 'of-active' : ''; ?>">Highest Paid</a>
      <?php
      // Week 2+ launches:
      ?>
      <a href="<?php echo home_url('/onlyfans/celebrity-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('celebrity-onlyfans-influencers') ? 'of-active' : ''; ?>">Celebrity</a>
      <a href="<?php echo home_url('/onlyfans/milf-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('milf-onlyfans-influencers') ? 'of-active' : ''; ?>">MILF</a>
      <a href="<?php echo home_url('/onlyfans/latina-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('latina-onlyfans-influencers') ? 'of-active' : ''; ?>">Latina</a>
      <a href="<?php echo home_url('/onlyfans/couple-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('couple-onlyfans-influencers') ? 'of-active' : ''; ?>">Couples</a>
      <a href="<?php echo home_url('/onlyfans/male-onlyfans-influencers/'); ?>" class="of-nav-link <?php echo is_page('male-onlyfans-influencers') ? 'of-active' : ''; ?>">Male</a>
      <?php
      ─────────────────────────────────────────────────────────────────── */
      ?>

    </div>

    <!-- Actions -->
    <div class="of-nav-actions">
      <a href="<?php echo home_url(); ?>" class="of-nav-home" title="Main site">← Main site</a>
      <a href="<?php echo home_url('/onlyfans/submit-profile/'); ?>" class="of-nav-submit">Submit profile</a>
    </div>

  </div>
</nav>

<!-- ── Page Content ─────────────────────────────────────────────────── -->
<div class="of-page-content">
  <?php
  while (have_posts()) :
    the_post();
    the_content();
  endwhile;
  ?>
</div>

<!-- ── OnlyFans Cluster Footer ─────────────────────────────────────── -->
<footer class="of-footer" aria-label="OnlyFans directory footer">
  <div class="of-footer-inner">

    <!-- Brand col -->
    <div class="of-footer-brand">
      <div class="of-footer-logo">TIN <span>·</span> OnlyFans</div>
      <p class="of-footer-desc">The most comprehensive OnlyFans influencer directory. Every ranking built from verified profile data — no paid placements, no guesswork.</p>
      <span class="of-footer-badge">Updated monthly · <?php echo date('F Y'); ?></span>
    </div>

    <!-- Rankings col -->
    <div>
      <div class="of-footer-col-title">Rankings</div>
      <ul class="of-footer-links">
        <li><a href="<?php echo home_url('/best-asian-onlyfans-influencers/'); ?>">Asian OnlyFans</a></li>
        <li><a href="<?php echo home_url('/best-onlyfans-agencies/'); ?>">OF Agencies</a></li>
        <!-- UNCOMMENT AS PAGES GO LIVE:
        <li><a href="<?php echo home_url('/onlyfans/best-onlyfans-influencers/'); ?>">Best OnlyFans</a></li>
        <li><a href="<?php echo home_url('/onlyfans/free-onlyfans-influencers/'); ?>">Free OnlyFans</a></li>
        <li><a href="<?php echo home_url('/onlyfans/highest-paid-onlyfans-influencers/'); ?>">Highest Paid</a></li>
        <li><a href="<?php echo home_url('/onlyfans/celebrity-onlyfans-influencers/'); ?>">Celebrity</a></li>
        <li><a href="<?php echo home_url('/onlyfans/milf-onlyfans-influencers/'); ?>">MILF</a></li>
        <li><a href="<?php echo home_url('/onlyfans/latina-onlyfans-influencers/'); ?>">Latina</a></li>
        <li><a href="<?php echo home_url('/onlyfans/couple-onlyfans-influencers/'); ?>">Couples</a></li>
        <li><a href="<?php echo home_url('/onlyfans/male-onlyfans-influencers/'); ?>">Male</a></li>
        -->
      </ul>
    </div>

    <!-- Info col -->
    <div>
      <div class="of-footer-col-title">Directory</div>
      <ul class="of-footer-links">
        <li><a href="<?php echo home_url('/onlyfans/'); ?>">Hub</a></li>
        <li><a href="<?php echo home_url('/onlyfans/submit-profile/'); ?>">Submit a Profile</a></li>
        <li><a href="<?php echo home_url(); ?>">Main Site</a></li>
        <li><a href="<?php echo home_url('/about/'); ?>">About</a></li>
        <li><a href="<?php echo home_url('/privacy-policy/'); ?>">Privacy</a></li>
      </ul>
    </div>

  </div>

  <div class="of-footer-bottom">
    <p class="of-footer-disclaimer">Independently operated. Not affiliated with OnlyFans or Fenix International Limited. All stats sourced from public OnlyFans profile data and re-verified monthly. All featured creators are verified adults 18+. No paid placements in ranked positions.</p>
    <span class="of-footer-copy">© <?php echo date('Y'); ?> theinfluencersnetwork.com</span>
  </div>
</footer>

<script>
// Progress bar
const ofProg = document.getElementById('ofProgress');
if (ofProg) {
  window.addEventListener('scroll', () => {
    const pct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    ofProg.style.width = Math.min(pct, 100) + '%';
  }, {passive: true});
}

// Active nav link highlight by URL
document.querySelectorAll('.of-nav-link').forEach(link => {
  if (link.href === window.location.href || 
      (link.href !== '<?php echo home_url('/onlyfans/'); ?>' && window.location.href.startsWith(link.href))) {
    link.classList.add('of-active');
  }
});
</script>

<?php wp_footer(); ?>
</body>
</html>
