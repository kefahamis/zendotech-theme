<?php
/**
 * Template Name: FAQ Layout
 */
get_header();
?>

<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-bar">
    <div class="container breadcrumb-inner">
        <h1 class="page-title">Frequently Asked Questions</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo esc_url(home_url('/')); ?>"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="separator"><i class="fa-solid fa-angle-right"></i></li>
            <li class="current">FAQs</li>
        </ul>
    </div>
</div>

<!-- ===== FAQ SECTION ===== -->
<section class="section faq-section">
    <div class="container">
        <div class="max-w-800 mx-auto">
            <div class="section-header text-center mb-50">
                <h2 class="title-lg">How can we help you?</h2>
                <p class="text-muted">Find answers to common questions about our products, shipping, and returns.
                </p>
            </div>

            <!-- Accordion Groups -->
            <div class="faq-accordion-group">
                <h3 class="faq-category-title"><i class="fa-solid fa-box-open"></i> Orders & Shipping</h3>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">How long does shipping take?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-content">
                            <p>Standard shipping typically takes 3-5 business days within the continental US.
                                Express shipping options (1-2 days) are available at checkout. International orders
                                usually arrive within 7-14 business days depending on customs processing.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">Can I track my order?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-content">
                            <p>Yes! Once your order ships, you will receive a confirmation email with a tracking
                                number. You can also track your order status directly from your account dashboard or
                                by clicking "Track Order" in the top bar.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">Do you ship internationally?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-content">
                            <p>We ship to over 50 countries worldwide. Shipping costs and delivery times vary by
                                location. Please note that international customers are responsible for any customs
                                duties or taxes that may apply.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-accordion-group mt-40">
                <h3 class="faq-category-title"><i class="fa-solid fa-rotate-left"></i> Returns & Warranty</h3>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">What is your return policy?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-content">
                            <p>We offer a 30-day hassle-free return policy. If you're not completely satisfied with
                                your purchase, you can return it within 30 days of receipt for a full refund or
                                exchange, provided the item is in like-new condition with all original packaging.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">How do I claim valid warranty?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-content">
                            <p>All new products come with a minimum 1-year manufacturer warranty. To make a claim,
                                please contact our support team with your order number and a description of the
                                issue. We'll guide you through the repair or replacement process.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-accordion-group mt-40">
                <h3 class="faq-category-title"><i class="fa-solid fa-credit-card"></i> Payment & Account</h3>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">What payment methods do you accept?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-content">
                            <p>We accept all major credit cards (Visa, MasterCard, Amex, Discover), PayPal, Apple
                                Pay, and Google Pay. We also offer financing options through Affirm for orders over
                                $250.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>