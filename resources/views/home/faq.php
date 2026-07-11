<main class="info-page faq-page">
    <section class="info-panel faq-hero-panel">
        <p class="eyebrow">FAQ's</p>
        <h1>Questions people ask before shopping</h1>
        <p>Welcome to our FAQ. We have answered the most common questions about delivery, orders, products, payment, and warranty.</p>
    </section>

    <?php
    $faqGroups = [
        'Shipping & Returns' => [
            'intro' => 'Below are some common questions about shipping, returns, and exchanges.',
            'items' => [
                ['Will my order be delivered to my doorstep?', 'Yes. Your package will be delivered to your doorstep. If a dispatch rider cannot reach the provided address, you will be contacted to choose the nearest safe public pickup point or delivery arrangement.'],
                ['When will my order be shipped?', 'Once your order is received and confirmed, it will be processed and prepared for dispatch within 24 hours.'],
                ['What is the expected delivery timeframe?', 'Orders within Lagos usually arrive within 24-48 hours. Deliveries outside Lagos may take 3-7 business days depending on the courier route, weather, and local delivery conditions.'],
                ['What is your return policy?', 'We offer a 7-day return window when a wrong item is delivered. Please contact support as soon as possible with your order details and clear photos of the item received.'],
            ],
        ],
        'Orders' => [
            'intro' => 'Below are some common questions about placing and tracking orders.',
            'items' => [
                ['How do I make a purchase?', 'Browse or search for a product, open the product details, click Add to Cart, review your cart, then proceed to checkout and complete your billing, shipping, and payment information.'],
                ['How do I know if my order is confirmed?', 'After checkout, you should receive an order confirmation with your order details through the contact information you provided.'],
                ['How do I track my order?', 'Once your order is shipped, delivery updates will be sent to your email or phone number. You can also contact support with your order number.'],
                ['Can I change my delivery details after ordering?', 'If the order has not been dispatched, contact support quickly with your order number and the corrected delivery details.'],
            ],
        ],
        'Products' => [
            'intro' => 'Below are some common questions about our product information.',
            'items' => [
                ['Are your products brand new or used?', 'Each product page clearly states the product details available to us. Always review the title, description, images, and price before checkout.'],
                ['Do product photos show the exact item?', 'Product photos are used to help you understand the style and finish. For custom or limited items, the final appearance may vary slightly.'],
                ['Can I ask questions before buying?', 'Yes. If you need more information about size, availability, delivery, or product details, contact us before placing your order.'],
            ],
        ],
        'Payment' => [
            'intro' => 'Below are common questions about secure payment and checkout.',
            'items' => [
                ['What payment methods do you support?', 'The checkout is prepared for card and online payment options through supported payment partners. More payment methods can be added as the store grows.'],
                ['Is my payment secure?', 'Payment details are handled by payment providers. We do not ask you to send card details through chat or social media.'],
                ['What if payment is successful but my order does not show?', 'Contact support with your payment reference, name, phone number, and email address so the order can be checked.'],
            ],
        ],
        'Warranty' => [
            'intro' => 'Below are some common questions about warranty terms.',
            'items' => [
                ['Do I need my receipt for a warranty claim?', 'Yes. A valid receipt, invoice, or order confirmation is required for warranty or return requests.'],
                ['Does warranty cover physical damage?', 'No. Physical damage, liquid damage, cracks, dents, or user-caused faults are not covered.'],
                ['Will my data be safe during repair?', 'If your item stores personal data, back up your files before submitting it. We are not responsible for data loss during service checks.'],
            ],
        ],
    ];
    ?>

    <div class="faq-layout">
        <?php foreach ($faqGroups as $heading => $group): ?>
            <section class="faq-section">
                <div class="faq-section-head">
                    <h2><?= htmlspecialchars($heading) ?></h2>
                    <p><?= htmlspecialchars($group['intro']) ?></p>
                </div>
                <div class="faq-list">
                    <?php foreach ($group['items'] as [$question, $answer]): ?>
                        <details class="faq-item">
                            <summary><?= htmlspecialchars($question) ?></summary>
                            <p><?= htmlspecialchars($answer) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <section class="info-panel faq-contact-panel">
        <div>
            <p class="eyebrow">Still need help?</p>
            <h2>Didn’t find your answer?</h2>
            <p>Don't hesitate to contact us. Send a message and our support team will respond with the details you need.</p>
        </div>
        <form class="contact-form">
            <input type="text" placeholder="Name" aria-label="Name">
            <input type="email" placeholder="Email" aria-label="Email">
            <input type="tel" placeholder="Phone number" aria-label="Phone number">
            <textarea rows="5" placeholder="Message" aria-label="Message"></textarea>
            <button type="button" class="auth-submit">Send message</button>
        </form>
    </section>
</main>
