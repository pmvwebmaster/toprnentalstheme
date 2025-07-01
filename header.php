<?php
    $url_minha_conta = get_permalink( get_option('woocommerce_myaccount_page_id') );
    $usuario_logado = is_user_logged_in();
    $texto_botao = $usuario_logado ? 'Account' : 'Login';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/logo-sugesto-02.jpg-128x123.png" type="image/x-icon">

    <?php
    // SEO multilíngue dinâmico
    function get_seo_text($en, $pt) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if ($lang === 'pt') return $pt;
        return $en;
    }
    $site_name = get_bloginfo('name');
    $site_desc = get_seo_text(
        'Mobility scooter and stroller rental in Orlando, Florida. Free delivery and pickup. Book online for your vacation!',
        'Aluguel de scooter e carrinho de bebê em Orlando, Flórida. Entrega e retirada grátis. Reserve online para suas férias!'
    );
    $keywords = get_seo_text(
        'scooter rentals, mobility scooters, Orlando, strollers, rental, top rentals, Florida, travel, vacation, accessibility',
        'aluguel de scooter, carrinho de bebê, Orlando, aluguel, top rentals, Flórida, viagem, férias, acessibilidade'
    );
    $title = '';
    $desc = '';
    if (is_singular()) {
        $title = get_the_title() . ' | ' . $site_name;
        $desc = get_seo_text(
            get_the_excerpt(),
            has_excerpt() ? get_the_excerpt() : $site_desc
        );
    } else {
        $title = $site_name;
        $desc = $site_desc;
    }
    ?>
    <meta name="description" content="<?php echo esc_attr($desc); ?>">
    <meta name="keywords" content="<?php echo esc_attr($keywords); ?>">
    <meta name="author" content="Top Rentals Scooters">
    <title><?php echo esc_html($title); ?></title>
    <link rel="canonical" href="<?php echo esc_url( is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) ) ); ?>" />
    <meta property="og:title" content="<?php echo esc_attr($title); ?>" />
    <meta property="og:description" content="<?php echo esc_attr($desc); ?>" />
    <meta property="og:url" content="<?php echo is_singular() ? get_permalink() : home_url(); ?>" />
    <meta property="og:type" content="<?php echo is_singular() ? 'article' : 'website'; ?>" />
    <?php if ( is_singular() && has_post_thumbnail() ) : ?>
        <meta property="og:image" content="<?php echo get_the_post_thumbnail_url( null, 'large' ); ?>" />
    <?php else : ?>
        <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/logo-02-transp.png-455x236.png" />
    <?php endif; ?>
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr($desc); ?>" />
    <meta name="twitter:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/logo-02-transp.png-455x236.png" />
    <meta name="google-site-verification" content="google62184e24a15f16c4.html" />

    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/simple-line-icons/simple-line-icons.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/bootstrap/css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/bootstrap/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/parallax/jarallax.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/animatecss/animate.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/chatbutton/floating-wpp.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/popup-overlay-plugin/style.css">
    <!--<link rel="stylesheet" href="<?php //echo get_template_directory_uri(); ?>/assets/smart-cart/minicart-theme.css">-->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/dropdown/css/style.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/socicon/css/styles.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/theme/css/style.css">
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Inter+Tight:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter+Tight:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap"></noscript>
    <link rel="preload" as="style" href="<?php echo get_template_directory_uri(); ?>/assets/mobirise/css/mbr-additional.css?v=ResYi8">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/mobirise/css/mbr-additional.css?v=ResYi8" type="text/css">
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">
</head>

<body <?php body_class(); ?>>
    <section data-bs-version="5.1" class="menu menu1 cid-uLcMJyzaMN" once="menu" id="menu1-4f">
        <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
            <h3 class="menu-tite mbr-fonts-style display-4">
              <div class="right-wrap d-flex align-items-center">
                  <a href="<?php //echo wc_get_cart_url(); ?>/cart" >
                      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png" alt="Cart" >
                  </a>

                  <div class="gtranslate_wrapper"><?php echo do_shortcode('[gtranslate]'); ?></div>
              </div>
            </h3>
            <div class="container">
                <div class="navbar-brand">
                    <span class="navbar-logo">
                        <a href="<?php echo home_url(); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-02-transp.png-455x236.png" alt="" style="height: 8rem;">
                        </a>
                    </span>
                    <span class="navbar-caption-wrap skiptranslate">
                        <a class="navbar-caption text-primary display-7" href="#">Rentals Scooters</a>
                    </span>
                </div>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-toggle="collapse" data-target="#navbarSupportedContent" data-bs-target="#navbarSupportedContent" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <div class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav nav-dropdown nav-right" data-app-modern-menu="true">
                            <li class="nav-item skiptranslate"><a class="nav-link link text-primary display-4" href="<?php echo home_url(); ?>">Home</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/about-us'); ?>" aria-expanded="false">About Us</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/products'); ?>" aria-expanded="false">Products</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/how-it-works'); ?>">How it Works</a></li>
                            <li class="nav-item skiptranslate"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/faq'); ?>">FAQ</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/contact'); ?>">Contacts</a></li>
                        </ul>
                        <div class="account-link text-center mt-2">
                        <a href="<?php echo $url_minha_conta; ?>" style="text-decoration: none; color: inherit;">
                            <div class="nav-link link text-primary display-4" style="font-size: 0.9rem;">
                                👤
                                <?php echo $texto_botao; ?>
                            </div>
                        </a>
                    </div>

            </div>
        </nav>
    </section>

    <?php
    // List of slugs used in the navbar menu
    $menu_slugs = [
        '', // Home
        'about-us',
        'products',
        'how-it-works',
        'faq',
        'contact'
    ];

    // Get current page slug
    global $post;
    $current_slug = '';
    if (is_front_page() || is_home()) {
        $current_slug = '';
    } elseif (isset($post->post_name)) {
        $current_slug = $post->post_name;
    }

    // Show section only if current slug is NOT in menu slugs
    if (!in_array($current_slug, $menu_slugs)) :
    ?>
    <section data-bs-version="5.1" class="header02 trustm5 cid-uH94Fx8XKn mbr-parallax-background" id="header02-2v">
        <div class="mbr-overlay" style="opacity: 0.5; background-color:rgb(12, 44, 104);"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper">
                        <!-- Custom content if needed -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
