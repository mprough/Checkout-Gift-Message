<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (!defined('CHECKOUT_GIFT_MESSAGE_STATUS') || CHECKOUT_GIFT_MESSAGE_STATUS !== 'true') {
    return;
}

$giftMessagePages = ['checkout_shipping', 'checkout_payment', 'checkout_confirmation', 'checkout_one', 'checkout_one_confirmation'];
if (!in_array($current_page_base, $giftMessagePages, true)) {
    return;
}

$giftMessageName = trim((string)CHECKOUT_GIFT_MESSAGE_NAME) !== '' ? CHECKOUT_GIFT_MESSAGE_NAME : 'Gift message';
$giftMessageDirections = (string)CHECKOUT_GIFT_MESSAGE_DIRECTIONS;
$giftMessagePlaceholder = (string)CHECKOUT_GIFT_MESSAGE_PLACEHOLDER;
$giftMessageMaxLength = max(1, min(5000, (int)CHECKOUT_GIFT_MESSAGE_MAX_LENGTH));
$giftMessageValue = (string)($_SESSION['checkout_gift_message'] ?? '');
?>
<script>
(() => {
    'use strict';
    const page = <?= json_encode($current_page_base) ?>;
    const config = <?= json_encode([
        'name' => $giftMessageName,
        'directions' => $giftMessageDirections,
        'placeholder' => $giftMessagePlaceholder,
        'maxLength' => $giftMessageMaxLength,
        'value' => $giftMessageValue,
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    function element(tag, attributes, text) {
        const node = document.createElement(tag);
        Object.entries(attributes || {}).forEach(([key, value]) => node.setAttribute(key, value));
        if (text !== undefined && text !== '') node.textContent = text;
        return node;
    }

    function buildEditor() {
        const wrapper = element('fieldset', {id: 'checkoutGiftMessage', class: 'checkout-gift-message'});
        wrapper.appendChild(element('legend', {}, config.name));
        if (config.directions) wrapper.appendChild(element('p', {class: 'checkout-gift-message-directions'}, config.directions));
        const label = element('label', {for: 'checkoutGiftMessageInput', class: 'inputLabel'}, config.name);
        const textarea = element('textarea', {
            id: 'checkoutGiftMessageInput',
            name: 'checkout_gift_message',
            rows: '4',
            maxlength: String(config.maxLength),
            placeholder: config.placeholder,
        });
        textarea.value = config.value;
        textarea.addEventListener('input', () => { config.value = textarea.value; });
        wrapper.append(label, textarea);
        return wrapper;
    }

    function buildReview() {
        if (!config.value) return null;
        const wrapper = element('div', {id: 'checkoutGiftMessageReview', class: 'checkout-gift-message-review'});
        wrapper.appendChild(element('h3', {}, config.name));
        wrapper.appendChild(element('p', {}, config.value));
        return wrapper;
    }

    function syncConfirmationForm() {
        const form = document.querySelector('form#checkout_confirmation, form[name="checkout_confirmation"]');
        if (!form) return;
        let input = form.querySelector('input[name="checkout_gift_message"]');
        if (!input) {
            input = element('input', {type: 'hidden', name: 'checkout_gift_message'});
            form.appendChild(input);
        }
        input.value = config.value;
        if (page === 'checkout_one' && !document.getElementById('checkoutGiftMessageReview')) {
            const review = buildReview();
            if (review) form.prepend(review);
        }
    }

    function inject() {
        syncConfirmationForm();
        if (document.getElementById('checkoutGiftMessage') || document.getElementById('checkoutGiftMessageReview')) return;
        const isReview = page === 'checkout_confirmation' || page === 'checkout_one_confirmation';
        if (isReview) {
            const form = document.querySelector('form#checkout_confirmation, form[name="checkout_confirmation"]');
            const review = buildReview();
            if (form && review) form.prepend(review);
            return;
        }

        let form;
        if (page === 'checkout_shipping') form = document.querySelector('form[name="checkout_address"]');
        if (page === 'checkout_payment') form = document.querySelector('form[name="checkout_payment"]');
        if (page === 'checkout_one') form = document.querySelector('form#checkout_payment, form[name="checkout_payment"], form[action*="checkout_one_confirmation"]');
        if (!form) return;
        const actions = form.querySelector('.buttonRow.forward, .buttonRow, [type="submit"]');
        if (actions && actions.parentNode) actions.parentNode.insertBefore(buildEditor(), actions);
        else form.appendChild(buildEditor());
    }

    document.addEventListener('DOMContentLoaded', inject);
    if (page === 'checkout_one') new MutationObserver(inject).observe(document.documentElement, {childList: true, subtree: true});
})();
</script>
