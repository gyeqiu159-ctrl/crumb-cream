<?php
/**
 * Crumb & Cream — Graham Bars Landing Page
 * Single-product landing page. No database, no cart, no backend logic
 * beyond simple PHP variables so the content is easy to edit in one place.
 */

// ---- Site configuration (edit these to update the page content) ----
$site = [
    'brand'       => 'Crumb & Cream',
    'tagline'     => 'Sweet moments, one bite at a time.',
    'year'        => date('Y'),
];

$product = [
    'name'        => 'Graham Bars',
    'price_from'  => '30.00',
    'sizes'       => [
        ['label' => '1 Pieces',  'price' => '30.00'],
        ['label' => '2 Pieces',  'price' => '60.00'],
        ['label' => '4 Pieces', 'price' => '120.00'],
    ],
];

$contact = [
    'facebook'  => 'facebook.com/crumbandcream',
    'instagram' => 'instagram.com/crumbandcream',
    'messenger' => 'm.me/crumbandcream',
    'phone'     => '+63 991 059 5874',
    'email'     => 'bachelorofsis@gmail.com',
    'location'  => 'Blk 6, Ipil St, Hill Crest Village, Caloocan City, Metro Manila',
];

// ---- Order Inquiry form handling (optional MySQL storage via Laragon) ----
// The rest of the page works fine even if the database isn't set up yet;
// only this form needs it. See README.md for the Laragon setup guide.
require_once __DIR__ . '/config/database.php';

$orderFeedback = ['type' => null, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_inquiry'])) {
    $name        = trim($_POST['customer_name'] ?? '');
    $contactInfo = trim($_POST['contact_info'] ?? '');
    $size        = trim($_POST['order_size'] ?? '');
    $qty         = (int) ($_POST['order_qty'] ?? 1);
    $message     = trim($_POST['order_message'] ?? '');

    if ($name === '' || $contactInfo === '' || $size === '') {
        $orderFeedback = [
            'type'    => 'error',
            'message' => 'Please fill in your name, contact info, and size before submitting.',
        ];
    } else {
        $qty = max(1, min(50, $qty));
        $pdo = get_db_connection();

        if ($pdo === null) {
            $orderFeedback = [
                'type'    => 'error',
                'message' => 'We could not save your inquiry right now (database unavailable). Please message us directly instead.',
            ];
        } else {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO orders (customer_name, contact_info, size, quantity, message)
                     VALUES (:name, :contact, :size, :qty, :message)'
                );
                $stmt->execute([
                    ':name'    => $name,
                    ':contact' => $contactInfo,
                    ':size'    => $size,
                    ':qty'     => $qty,
                    ':message' => $message !== '' ? $message : null,
                ]);
                $orderFeedback = [
                    'type'    => 'success',
                    'message' => 'Thank you, ' . $name . '! We received your order inquiry and will reach out shortly.',
                ];
                // Clear submitted values after a successful save.
                $name = $contactInfo = $message = '';
                $size = '';
                $qty = 1;
            } catch (PDOException $e) {
                error_log('Order insert failed: ' . $e->getMessage());
                $orderFeedback = [
                    'type'    => 'error',
                    'message' => 'Something went wrong saving your inquiry. Please try again or message us directly.',
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crumb & Cream | Handcrafted Graham Bars</title>
    <meta name="description" content="Crumb & Cream Graham Bars — creamy, crunchy, handcrafted layered dessert bars made fresh to order. A little sweetness in every bite.">
    <meta name="keywords" content="graham bars, graham cake, dessert, Filipino dessert, Crumb and Cream">

    <!-- Open Graph -->
    <meta property="og:title" content="Crumb & Cream | Handcrafted Graham Bars">
    <meta property="og:description" content="Creamy, crunchy, handcrafted Graham Bars made fresh to order.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="graham-bars.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="graham-bars.svg" type="image/svg+xml">

    <!-- PWA -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#C1873F">
    <link rel="apple-touch-icon" href="icon-512.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Crumb & Cream">
    <link rel="icon" href="icon-512.png" sizes="512x512" type="image/png">
</head>
<body>

    <!-- ================= NAVIGATION ================= -->
    <header class="navbar" id="navbar">
        <div class="container nav-inner">
            <a href="#home" class="logo">
                <span class="logo-mark" aria-hidden="true"></span>
                <?php echo htmlspecialchars($site['brand']); ?>
            </a>

            <nav>
                <ul class="nav-links" id="navLinks">
                    <li><a href="#home" class="nav-link">Home</a></li>
                    <li><a href="#about" class="nav-link">About</a></li>
                    <li><a href="#product" class="nav-link">Product</a></li>
                    <li><a href="#why-us" class="nav-link">Why Us</a></li>
                    <li><a href="#reviews" class="nav-link">Reviews</a></li>
                    <li><a href="#faq" class="nav-link">FAQ</a></li>
                    <li><a href="#contact" class="nav-link">Contact</a></li>
                </ul>
            </nav>

            <div class="nav-cta">
                <a href="#contact" class="btn btn-primary">Order Now</a>
                <button class="hamburger" id="hamburger" aria-label="Toggle navigation menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- ================= HERO ================= -->
    <section class="hero" id="home">
        <div class="container hero-inner">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="dot"></span> Handcrafted with Love
                </div>
                <h1 class="hero-title">A Little <em>Sweetness</em> in Every Bite.</h1>
                <p class="hero-text">Indulge in our creamy, crunchy, and delicious Graham Bars — made to turn every snack break into a sweet moment.</p>
                <div class="hero-actions">
                    <a href="#contact" class="btn btn-primary">Order Now</a>
                    <a href="#product" class="btn btn-outline">Explore Product</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-visual-frame">
                    <div class="hero-shape shape-1" aria-hidden="true"></div>
                    <div class="hero-shape shape-2" aria-hidden="true"></div>
                    <img src="graham-bars.svg" alt="Layered Graham Bars with creamy filling, ready to be sliced and served" class="hero-product-img">
                    <div class="floating-card">
                        <div class="fc-icon" aria-hidden="true"><i class="fa-solid fa-leaf"></i></div>
                        <div>
                            <div class="fc-title">Freshly Made</div>
                            <div class="fc-sub">Made with care</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= PRODUCT HIGHLIGHT ================= -->
    <section class="highlight section-pad" id="about">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">The Recipe</span>
                <h2>Made for Sweet Moments</h2>
                <p>Our Graham Bars combine delicious layers of creamy goodness and crunchy graham goodness in one satisfying treat.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon"><i class="fa-solid fa-ice-cream"></i></div>
                    <h3>Creamy</h3>
                    <p>Smooth and delicious cream in every bite.</p>
                </div>
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon"><i class="fa-solid fa-cookie"></i></div>
                    <h3>Crunchy</h3>
                    <p>Perfectly layered graham crackers for that satisfying texture.</p>
                </div>
                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon"><i class="fa-solid fa-seedling"></i></div>
                    <h3>Fresh</h3>
                    <p>Prepared with care to give you a delicious snack every time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= PRODUCT SHOWCASE ================= -->
    <section class="showcase section-pad" id="product">
        <div class="container showcase-inner">
            <div class="showcase-visual reveal">
                <img src="graham-bars.svg" alt="Close-up of stacked <?php echo htmlspecialchars($product['name']); ?> showing layers of graham crust and cream">
                <div class="price-tag">
                    <span class="amount">₱<?php echo htmlspecialchars($product['price_from']); ?></span>
                    <span class="from">starts at</span>
                </div>
            </div>

            <div class="showcase-content reveal reveal-delay-1">
                <span class="eyebrow">Our Signature</span>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p>Layers of buttery graham crust hugging a smooth, creamy filling — sliced into bars and ready whenever a craving hits. Every batch is made fresh, never rushed.</p>

                <span class="size-label">Available Sizes</span>
                <div class="size-options" id="sizeOptions">
                    <?php foreach ($product['sizes'] as $i => $size): ?>
                        <button type="button" class="size-option<?php echo $i === 0 ? ' active' : ''; ?>" data-price="<?php echo htmlspecialchars($size['price']); ?>">
                            <?php echo htmlspecialchars($size['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="qty-row">
                    <span class="qty-label">Quantity</span>
                    <div class="qty-selector">
                        <button type="button" class="qty-btn" id="qtyMinus" aria-label="Decrease quantity">−</button>
                        <span class="qty-value" id="qtyValue">1</span>
                        <button type="button" class="qty-btn" id="qtyPlus" aria-label="Increase quantity">+</button>
                    </div>
                </div>

                <a href="#contact" class="btn btn-primary">Order Now</a>
            </div>
        </div>
    </section>

    <!-- ================= WHY CHOOSE US ================= -->
    <section class="why-us section-pad" id="why-us">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Why Choose Us</span>
                <h2>Why You'll Love Our Graham Bars</h2>
            </div>

            <div class="why-grid">
                <div class="why-card reveal">
                    <div class="why-icon"><i class="fa-solid fa-clock"></i></div>
                    <h3>Freshly Prepared</h3>
                    <p>Made in small batches so every bar tastes its best.</p>
                </div>
                <div class="why-card reveal reveal-delay-1">
                    <div class="why-icon"><i class="fa-solid fa-heart"></i></div>
                    <h3>Delicious & Creamy</h3>
                    <p>A rich, smooth filling balanced with crunchy layers.</p>
                </div>
                <div class="why-card reveal reveal-delay-2">
                    <div class="why-icon"><i class="fa-solid fa-peso-sign"></i></div>
                    <h3>Affordable</h3>
                    <p>Treat yourself without stretching your budget.</p>
                </div>
                <div class="why-card reveal reveal-delay-3">
                    <div class="why-icon"><i class="fa-solid fa-gift"></i></div>
                    <h3>Perfect for Every Occasion</h3>
                    <p>Great for snacks, gifts, or sharing with friends.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= HOW TO ORDER ================= -->
    <section class="order-steps section-pad">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Simple Process</span>
                <h2>How to Order</h2>
                <p>From craving to first bite in three easy steps.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card reveal">
                    <div class="step-number">01</div>
                    <h3>Choose</h3>
                    <p>Choose your preferred quantity.</p>
                </div>
                <div class="step-card reveal reveal-delay-1">
                    <div class="step-number">02</div>
                    <h3>Order</h3>
                    <p>Send us your order through our contact channels.</p>
                </div>
                <div class="step-card reveal reveal-delay-2">
                    <div class="step-number">03</div>
                    <h3>Enjoy</h3>
                    <p>Receive your Graham Bars and enjoy every bite.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= REVIEWS ================= -->
    <section class="reviews section-pad" id="reviews">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Testimonials</span>
                <h2>What Our Customers Say</h2>
            </div>

            <div class="review-grid">
                <div class="review-card reveal">
                    <div class="stars">★★★★★</div>
                    <p>"Super creamy and delicious! The graham layers give it the perfect crunch."</p>
                    <div class="reviewer">
                        <div class="reviewer-avatar">M</div>
                        <div class="reviewer-name">Maria</div>
                    </div>
                </div>
                <div class="review-card reveal reveal-delay-1">
                    <div class="stars">★★★★★</div>
                    <p>"Perfect for merienda! Will definitely order again."</p>
                    <div class="reviewer">
                        <div class="reviewer-avatar">A</div>
                        <div class="reviewer-name">Angela</div>
                    </div>
                </div>
                <div class="review-card reveal reveal-delay-2">
                    <div class="stars">★★★★★</div>
                    <p>"Simple, affordable, and really tasty!"</p>
                    <div class="reviewer">
                        <div class="reviewer-avatar">J</div>
                        <div class="reviewer-name">John</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= CALL TO ACTION ================= -->
    <section class="cta-section section-pad">
        <div class="container cta-inner reveal">
            <h2>Ready for Your Next Sweet Bite?</h2>
            <p>Treat yourself to our delicious Graham Bars today.</p>
            <a href="#contact" class="btn btn-primary">Order Your Graham Bars</a>
        </div>
    </section>

    <!-- ================= FAQ ================= -->
    <section class="faq section-pad" id="faq">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Good to Know</span>
                <h2>Frequently Asked Questions</h2>
            </div>

            <div class="faq-list">
                <?php
                $faqs = [
                    ['q' => 'How much are the Graham Bars?', 'a' => 'Prices start at ₱' . $product['price_from'] . ' for 3 pieces. See the Product section above for our full size and price list.'],
                    ['q' => 'What sizes are available?', 'a' => 'We offer 3 Pieces, 6 Pieces, and 12 Pieces boxes — perfect for a quick snack or for sharing.'],
                    ['q' => 'Do you offer delivery?', 'a' => 'Yes! We coordinate delivery within our local area and can arrange meet-ups or courier booking for farther locations.'],
                    ['q' => 'How should I store the Graham Bars?', 'a' => 'Keep them chilled in the refrigerator and consume within a few days for the best taste and texture.'],
                    ['q' => 'Can I order in bulk?', 'a' => 'Absolutely. Message us ahead of time for bulk or event orders so we can prepare enough for your occasion.'],
                    ['q' => 'How can I place an order?', 'a' => 'Simply message us on Facebook, Messenger, or give us a call — we will guide you through the rest.'],
                ];
                foreach ($faqs as $index => $faq):
                ?>
                <div class="faq-item<?php echo $index === 0 ? ' active' : ''; ?>">
                    <button type="button" class="faq-question">
                        <span><?php echo htmlspecialchars($faq['q']); ?></span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p><?php echo htmlspecialchars($faq['a']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ================= CONTACT ================= -->
    <section class="contact-section section-pad" id="contact">
        <div class="container contact-inner">
            <div class="reveal">
                <span class="eyebrow">Get in Touch</span>
                <h2>Let's Get You Some Graham Bars</h2>

                <ul class="contact-list">
                    <li>
                        <div class="contact-icon"><i class="fa-brands fa-facebook-f"></i></div>
                        <div>
                            <strong>Facebook</strong>
                            <span><?php echo htmlspecialchars($contact['facebook']); ?></span>
                        </div>
                    </li>
                    <li>
                        <div class="contact-icon"><i class="fa-brands fa-facebook-messenger"></i></div>
                        <div>
                            <strong>Messenger</strong>
                            <span><?php echo htmlspecialchars($contact['messenger']); ?></span>
                        </div>
                    </li>
                    <li>
                        <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <strong>Phone</strong>
                            <span><?php echo htmlspecialchars($contact['phone']); ?></span>
                        </div>
                    </li>
                    <li>
                        <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <strong>Email</strong>
                            <span><?php echo htmlspecialchars($contact['email']); ?></span>
                        </div>
                    </li>
                    <li>
                        <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <strong>Location</strong>
                            <span><?php echo htmlspecialchars($contact['location']); ?></span>
                        </div>
                    </li>
                </ul>

                <div class="contact-actions">
                    <a href="https://<?php echo htmlspecialchars($contact['messenger']); ?>" class="btn btn-primary" target="_blank" rel="noopener">Message Us</a>
                    <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $contact['phone'])); ?>" class="btn btn-outline">Order Now</a>
                </div>
            </div>

            <div class="contact-visual reveal reveal-delay-1">
                <h3>Send an Order Inquiry</h3>
                <p class="form-intro">Fill this in and it's saved straight to our order list — we'll reach out to confirm.</p>

                <?php if ($orderFeedback['type']): ?>
                    <div class="form-alert form-alert-<?php echo htmlspecialchars($orderFeedback['type']); ?>" role="status">
                        <?php echo htmlspecialchars($orderFeedback['message']); ?>
                    </div>
                <?php endif; ?>

                <form class="order-form" method="POST" action="#contact">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Karl Christan Samonte"
                               value="<?php echo htmlspecialchars($name ?? ''); ?>" required maxlength="120">
                    </div>

                    <div class="form-group">
                        <label for="contact_info">Phone or Email</label>
                        <input type="text" id="contact_info" name="contact_info" placeholder="09xx xxx xxxx or you@email.com"
                               value="<?php echo htmlspecialchars($contactInfo ?? ''); ?>" required maxlength="150">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="order_size">Size</label>
                            <select id="order_size" name="order_size" required>
                                <option value="">Select size</option>
                                <?php foreach ($product['sizes'] as $sizeOption): ?>
                                    <option value="<?php echo htmlspecialchars($sizeOption['label']); ?>"
                                        <?php echo (isset($size) && $size === $sizeOption['label']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sizeOption['label']); ?> — ₱<?php echo htmlspecialchars($sizeOption['price']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="order_qty">Quantity</label>
                            <input type="number" id="order_qty" name="order_qty" min="1" max="50"
                                   value="<?php echo htmlspecialchars((string) ($qty ?? 1)); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="order_message">Message (optional)</label>
                        <textarea id="order_message" name="order_message" rows="3" placeholder="Preferred pickup date, delivery notes, etc."><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>

                    <button type="submit" name="order_inquiry" value="1" class="btn btn-primary btn-block">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="footer">
        <div class="container footer-top">
            <div>
                <a href="#home" class="logo">
                    <span class="logo-mark" aria-hidden="true"></span>
                    <?php echo htmlspecialchars($site['brand']); ?>
                </a>
                <p class="footer-tagline"><?php echo htmlspecialchars($site['tagline']); ?></p>
            </div>

            <div>
                <div class="footer-heading">Explore</div>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#product">Product</a></li>
                    <li><a href="#reviews">Reviews</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-heading">Follow Us</div>
                <div class="footer-social">
                    <a href="https://<?php echo htmlspecialchars($contact['facebook']); ?>" aria-label="Facebook" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://<?php echo htmlspecialchars($contact['instagram']); ?>" aria-label="Instagram" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://<?php echo htmlspecialchars($contact['messenger']); ?>" aria-label="Messenger" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-messenger"></i></a>
                </div>
            </div>
        </div>

        <div class="container footer-bottom">
            &copy; <?php echo htmlspecialchars($site['year']); ?> <?php echo htmlspecialchars($site['brand']); ?>. All Rights Reserved.
        </div>
    </footer>

    <button id="installBtn" class="install-btn" type="button" hidden>
        <i class="fa-solid fa-download" aria-hidden="true"></i> Install App
    </button>

    <script src="script.js"></script>
</body>
</html>
