document.addEventListener('DOMContentLoaded', () => {
    const CART_KEY = 'vtuCartItems';
    const DISCOUNT_KEY = 'vtuCartDiscount';
    const FREE_SHIPPING_THRESHOLD = 200000;
    const DISCOUNT_CODES = {
        SAVE10: { label: 'SAVE10', type: 'percent', value: 10, message: '10% discount applied.' },
        WELCOME5: { label: 'WELCOME5', type: 'percent', value: 5, message: '5% welcome discount applied.' },
        DEAL5000: { label: 'DEAL5000', type: 'fixed', value: 5000, min: 50000, message: '₦5,000 discount applied.' },
    };
    const cartCountElements = document.querySelectorAll('[data-cart-count]');
    const drawer = document.querySelector('[data-cart-drawer]');
    const backdrop = document.querySelector('[data-cart-backdrop]');
    const drawerItems = document.querySelector('[data-cart-drawer-items]');
    const drawerEmpty = document.querySelector('[data-cart-empty]');
    const pageItems = document.querySelector('[data-cart-page-items]');
    const pageEmpty = document.querySelector('[data-cart-page-empty]');
    const checkoutItems = document.querySelector('[data-checkout-items]');

    let quantity = 1;
    const quantityValue = document.getElementById('quantityValue');

    const formatNaira = (amount) => `₦${Number(amount || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));

    const readCart = () => {
        try {
            const items = JSON.parse(localStorage.getItem(CART_KEY) || '[]');
            return Array.isArray(items) ? items : [];
        } catch (error) {
            return [];
        }
    };

    const writeCart = (items) => {
        localStorage.setItem(CART_KEY, JSON.stringify(items));
        localStorage.setItem('vtuCartCount', String(items.reduce((sum, item) => sum + item.quantity, 0)));
    };

    const readDiscount = () => {
        const code = localStorage.getItem(DISCOUNT_KEY);
        return code && DISCOUNT_CODES[code] ? { code, ...DISCOUNT_CODES[code] } : null;
    };

    const discountAmount = (subtotal, discount) => {
        if (!discount) return 0;
        if (discount.min && subtotal < discount.min) return 0;
        if (discount.type === 'percent') return Math.round(subtotal * (discount.value / 100));
        return Math.min(subtotal, discount.value);
    };

    const seedCartPage = () => {
        if (localStorage.getItem(CART_KEY) || !pageItems) return;
        const seed = document.querySelector('[data-cart-seed]');
        if (!seed?.textContent) return;

        try {
            const items = JSON.parse(seed.textContent);
            if (Array.isArray(items) && items.length) {
                writeCart(items);
            }
        } catch (error) {
        }
    };

    const syncDiscountInputs = () => {
        const discount = readDiscount();
        document.querySelectorAll('[data-discount-code]').forEach((input) => {
            input.value = discount?.code || '';
        });
    };

    const cartTotals = (items) => {
        const subtotal = items.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity || 1)), 0);
        const count = items.reduce((sum, item) => sum + Number(item.quantity || 1), 0);
        const discount = readDiscount();
        const discountValue = discountAmount(subtotal, discount);
        return { subtotal, count, discount, discountValue, total: Math.max(0, subtotal - discountValue) };
    };

    const setShippingState = (subtotal) => {
        const remaining = Math.max(0, FREE_SHIPPING_THRESHOLD - subtotal);
        const progress = Math.min(100, (subtotal / FREE_SHIPPING_THRESHOLD) * 100);
        const message = remaining > 0
            ? `Spend ${formatNaira(remaining)} more to reach free shipping!`
            : 'You are eligible for free shipping.';

        document.querySelectorAll('[data-shipping-message]').forEach((element) => {
            element.textContent = message;
        });

        document.querySelectorAll('[data-shipping-progress]').forEach((element) => {
            element.style.width = `${progress}%`;
        });
    };

    const updateCountAndTotals = (items) => {
        const { subtotal, count, discount, discountValue, total } = cartTotals(items);

        cartCountElements.forEach((element) => {
            element.textContent = String(count);
        });

        document.querySelectorAll('[data-cart-subtotal]').forEach((element) => {
            element.textContent = `${formatNaira(total)} NGN`;
        });

        document.querySelectorAll('[data-cart-discount-row]').forEach((element) => {
            element.hidden = !(discount && discountValue > 0);
        });

        document.querySelectorAll('[data-cart-discount-label]').forEach((element) => {
            element.textContent = discount ? `Discount (${discount.code})` : 'Discount';
        });

        document.querySelectorAll('[data-cart-discount]').forEach((element) => {
            element.textContent = `-${formatNaira(discountValue)}`;
        });

        setShippingState(subtotal);
    };

    const makeCartLine = (item, compact = false) => {
        const name = escapeHtml(item.shortName || item.name);
        const fullName = escapeHtml(item.name || item.shortName);
        const category = escapeHtml(item.category);
        const image = escapeHtml(item.image);
        const quantity = Number(item.quantity || 1);
        const price = Number(item.price || 0);
        const lineTotal = price * quantity;
        const discount = Number(item.discount || 0);

        if (compact) {
            return `
                <article class="drawer-cart-line" data-cart-line data-product-id="${escapeHtml(item.id)}">
                    <img src="${image}" alt="${fullName}">
                    <div>
                        <h3>${name}</h3>
                        <p>${category}${discount > 0 ? ` <span>${discount}% off</span>` : ''}</p>
                        <strong>${formatNaira(lineTotal)}</strong>
                        <div class="cart-line-actions">
                            <button type="button" data-cart-decrease aria-label="Decrease ${name} quantity">−</button>
                            <span>${quantity}</span>
                            <button type="button" data-cart-increase aria-label="Increase ${name} quantity">+</button>
                            <button type="button" data-remove-line>Remove</button>
                        </div>
                    </div>
                </article>
            `;
        }

        return `
            <article class="cart-line" data-cart-line data-product-id="${escapeHtml(item.id)}">
                <img src="${image}" alt="${fullName}">
                <div>
                    <h2>${fullName}${discount > 0 ? ` <span>(${discount}% off)</span>` : ''}</h2>
                    <p>${category}</p>
                    <strong>${formatNaira(price)}</strong>
                </div>
                <input type="number" min="1" value="${quantity}" aria-label="${name} quantity" data-cart-qty>
                <button type="button" data-remove-line>Remove</button>
                <strong data-line-total>${formatNaira(lineTotal)}</strong>
            </article>
        `;
    };

    const renderCart = () => {
        const items = readCart();
        updateCountAndTotals(items);

        if (drawerItems) {
            drawerItems.innerHTML = items.map((item) => makeCartLine(item, true)).join('');
        }

        if (pageItems) {
            pageItems.innerHTML = items.map((item) => makeCartLine(item)).join('');
        }

        if (checkoutItems) {
            checkoutItems.innerHTML = items.length
                ? items.map((item) => `<span>${escapeHtml(item.shortName || item.name)} × ${Number(item.quantity || 1)}</span>`).join('')
                : '<span>Your cart is empty.</span>';
        }

        const isEmpty = items.length === 0;
        drawerEmpty?.classList.toggle('is-visible', isEmpty);
        if (drawerEmpty) drawerEmpty.hidden = !isEmpty;
        if (drawerItems) drawerItems.hidden = isEmpty;
        pageEmpty?.classList.toggle('is-visible', isEmpty);
        if (pageEmpty) pageEmpty.hidden = !isEmpty;
        if (pageItems) pageItems.hidden = isEmpty;
        document.body.classList.toggle('cart-is-empty', isEmpty);
        drawer?.classList.toggle('has-items', !isEmpty);
    };

    const addToCart = (product, quantityToAdd = 1) => {
        const items = readCart();
        const existing = items.find((item) => String(item.id) === String(product.id));
        const nextQuantity = Math.max(1, Number(quantityToAdd || 1));

        if (existing) {
            existing.quantity += nextQuantity;
        } else {
            items.push({ ...product, quantity: nextQuantity });
        }

        writeCart(items);
        renderCart();
    };

    const changeQuantity = (productId, nextQuantity) => {
        const items = readCart()
            .map((item) => String(item.id) === String(productId)
                ? { ...item, quantity: Math.max(1, Number(nextQuantity || 1)) }
                : item);
        writeCart(items);
        renderCart();
    };

    const removeFromCart = (productId) => {
        writeCart(readCart().filter((item) => String(item.id) !== String(productId)));
        renderCart();
    };

    const applyDiscount = (code) => {
        const normalized = String(code || '').trim().toUpperCase();
        const discount = DISCOUNT_CODES[normalized];
        const subtotal = cartTotals(readCart()).subtotal;

        if (!normalized) {
            localStorage.removeItem(DISCOUNT_KEY);
            renderCart();
            return 'Enter a discount code first.';
        }

        if (!discount) {
            localStorage.removeItem(DISCOUNT_KEY);
            renderCart();
            return 'That discount code is not valid. Try SAVE10, WELCOME5, or DEAL5000.';
        }

        if (discount.min && subtotal < discount.min) {
            localStorage.removeItem(DISCOUNT_KEY);
            renderCart();
            return `${discount.label} works on orders from ${formatNaira(discount.min)}.`;
        }

        localStorage.setItem(DISCOUNT_KEY, normalized);
        document.querySelectorAll('[data-discount-code]').forEach((input) => {
            input.value = normalized;
        });
        renderCart();
        return discount.message;
    };

    const openCart = () => {
        drawer?.classList.add('is-open');
        backdrop?.classList.add('is-visible');
        document.body.classList.add('cart-drawer-open');
        drawer?.setAttribute('aria-hidden', 'false');
    };

    const closeCart = () => {
        drawer?.classList.remove('is-open');
        backdrop?.classList.remove('is-visible');
        document.body.classList.remove('cart-drawer-open');
        drawer?.setAttribute('aria-hidden', 'true');
        document.documentElement.scrollLeft = 0;
        document.body.scrollLeft = 0;
    };

    seedCartPage();
    syncDiscountInputs();
    renderCart();

    document.querySelectorAll('[data-cart-open]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            openCart();
        });
    });

    document.querySelectorAll('[data-cart-close], [data-cart-backdrop]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            closeCart();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeCart();
        }
    });

    document.querySelectorAll('[data-qty-minus]').forEach((button) => {
        button.addEventListener('click', () => {
            quantity = Math.max(1, quantity - 1);
            if (quantityValue) quantityValue.value = quantity;
            if (quantityValue) quantityValue.textContent = String(quantity);
        });
    });

    document.querySelectorAll('[data-qty-plus]').forEach((button) => {
        button.addEventListener('click', () => {
            quantity += 1;
            if (quantityValue) quantityValue.value = quantity;
            if (quantityValue) quantityValue.textContent = String(quantity);
        });
    });

    document.querySelectorAll('.thumb').forEach((button) => {
        button.addEventListener('click', () => {
            const mainImage = document.getElementById('mainProductImage');
            if (mainImage && button.dataset.image) {
                mainImage.src = button.dataset.image;
            }
        });
    });

    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = button.closest('.password-control')?.querySelector('[data-password]');
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            button.textContent = input.type === 'password' ? 'Show' : 'Hide';
        });
    });

    document.querySelectorAll('[data-cart-add]').forEach((button) => {
        button.addEventListener('click', () => {
            const quantitySource = button.dataset.productQuantitySource
                ? document.getElementById(button.dataset.productQuantitySource)
                : null;
            const requestedQuantity = quantitySource
                ? Number(quantitySource.value || quantitySource.textContent || 1)
                : 1;

            addToCart({
                id: button.dataset.productId,
                name: button.dataset.productName,
                shortName: button.dataset.productShortName,
                category: button.dataset.productCategory,
                price: Number(button.dataset.productPrice || 0),
                image: button.dataset.productImage,
                discount: Number(button.dataset.productDiscount || 0),
            }, requestedQuantity);

            const original = button.textContent;
            button.textContent = 'Added';
            button.disabled = true;
            openCart();

            setTimeout(() => {
                button.textContent = original;
                button.disabled = false;
            }, 900);
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-cart-close]') || event.target.closest('[data-cart-backdrop]')) {
            closeCart();
            return;
        }

        const line = event.target.closest('[data-cart-line]');
        if (!line) return;

        const productId = line.dataset.productId;
        if (event.target.closest('[data-remove-line]')) {
            removeFromCart(productId);
        }

        if (event.target.closest('[data-cart-increase]')) {
            const item = readCart().find((cartItem) => String(cartItem.id) === String(productId));
            changeQuantity(productId, Number(item?.quantity || 1) + 1);
        }

        if (event.target.closest('[data-cart-decrease]')) {
            const item = readCart().find((cartItem) => String(cartItem.id) === String(productId));
            changeQuantity(productId, Math.max(1, Number(item?.quantity || 1) - 1));
        }
    });

    document.addEventListener('change', (event) => {
        const input = event.target.closest('[data-cart-qty]');
        if (!input) return;
        const line = input.closest('[data-cart-line]');
        if (!line) return;
        changeQuantity(line.dataset.productId, input.value);
    });

    document.querySelectorAll('[data-discount-apply]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = button.closest('.cart-tool-card, .cart-drawer-tools') || document;
            const input = panel.querySelector('[data-discount-code]');
            const note = panel.querySelector('[data-discount-note]');
            const message = applyDiscount(input?.value);

            if (note) {
                note.textContent = message;
            }
        });
    });

    document.querySelectorAll('[data-shipping-calculate]').forEach((button) => {
        button.addEventListener('click', () => {
            const note = button.closest('.cart-tool-card')?.querySelector('[data-shipping-note]');
            if (note) {
                note.textContent = 'Standard Shipping: ₦8,000.00. Free shipping applies above ₦200,000.00.';
            }
        });
    });

    document.querySelectorAll('[data-paystack-pay]').forEach((button) => {
        button.addEventListener('click', async () => {
            const form = button.closest('[data-checkout-form]');
            const message = form?.querySelector('[data-payment-message]');
            const formData = new FormData(form);
            const items = readCart();
            const discount = readDiscount();
            const name = `${formData.get('first_name') || ''} ${formData.get('last_name') || ''}`.trim();

            if (!items.length) {
                if (message) message.textContent = 'Your cart is empty.';
                return;
            }

            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Redirecting...';
            if (message) message.textContent = '';

            try {
                const response = await fetch('paystack/initialize', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: formData.get('email'),
                        name,
                        phone: formData.get('phone'),
                        discount_code: discount?.code || '',
                        items: items.map((item) => ({ id: item.id, quantity: item.quantity })),
                    }),
                });
                const payload = await response.json();

                if (!payload.status || !payload.authorization_url) {
                    throw new Error(payload.message || 'Could not start payment.');
                }

                window.location.href = payload.authorization_url;
            } catch (error) {
                if (message) message.textContent = error.message;
                button.disabled = false;
                button.textContent = originalText;
            }
        });
    });

    document.querySelectorAll('[data-category-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const group = button.closest('.category-group');
            if (!group) return;

            document.querySelectorAll('.category-group.is-open').forEach((openGroup) => {
                if (openGroup !== group) {
                    openGroup.classList.remove('is-open');
                }
            });

            group.classList.toggle('is-open');
        });
    });

    document.querySelectorAll('[data-account-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const menu = button.closest('.account-menu');
            if (!menu) return;
            const isOpen = menu.classList.toggle('is-open');
            button.setAttribute('aria-expanded', String(isOpen));
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('.account-menu')) return;
        document.querySelectorAll('.account-menu.is-open').forEach((menu) => {
            menu.classList.remove('is-open');
            menu.querySelector('[data-account-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    });
});
