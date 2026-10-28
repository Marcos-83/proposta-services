// public/js/app.js - Proposta Services

// ===================== SIDEBAR TOGGLE =====================
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');
const hamburger = document.getElementById('hamburger');

if (hamburger) {
    hamburger.addEventListener('click', toggleSidebar);
}
if (overlay) {
    overlay.addEventListener('click', closeSidebar);
}

function toggleSidebar() {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
}

function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
}

// ===================== PHONE MASK =====================
document.querySelectorAll('.phone-mask').forEach(input => {
    input.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        if (v.length > 6) {
            v = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
        } else if (v.length > 2) {
            v = '(' + v.slice(0,2) + ') ' + v.slice(2);
        } else if (v.length > 0) {
            v = '(' + v;
        }
        this.value = v;
    });
});

// ===================== CURRENCY MASK =====================
document.querySelectorAll('.currency-mask').forEach(input => {
    input.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '');
        v = (parseInt(v || '0') / 100).toFixed(2);
        this.value = v.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    });
    // Initial format if value exists
    if (input.value) {
        let ev = new Event('input');
        // Convert float to masked
        let num = parseFloat(input.dataset.value || input.value.replace(',', '.'));
        if (!isNaN(num)) {
            let v = num.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = v;
        }
    }
});

// ===================== DATE MASK =====================
document.querySelectorAll('.date-mask').forEach(input => {
    input.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 2 && v.length <= 4) v = v.slice(0,2) + '/' + v.slice(2);
        else if (v.length > 4) v = v.slice(0,2) + '/' + v.slice(2,4) + '/' + v.slice(4,8);
        this.value = v;
    });
});

// ===================== AUTO-FILL ITEM FROM PRODUCT =====================
const productSelect = document.getElementById('product_id');
if (productSelect) {
    productSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const desc = opt.dataset.description || '';
        const price = opt.dataset.price || '';
        const descInput = document.getElementById('item_description');
        const priceInput = document.getElementById('item_unit_price');
        if (descInput && desc) descInput.value = desc;
        if (priceInput && price) {
            const num = parseFloat(price);
            if (!isNaN(num)) {
                // Feed as centesimal string so the currency-mask event formats correctly
                priceInput.value = Math.round(num * 100).toString();
                priceInput.dispatchEvent(new Event('input'));
            }
        }
        recalcItemTotal();
    });
}

// Recalc item total on quantity/price change
function recalcItemTotal() {
    const qty   = parseFloat((document.getElementById('item_quantity')?.value || '1').replace(',', '.')) || 0;
    const price = parseFloat((document.getElementById('item_unit_price')?.value || '0').replace(/\./g,'').replace(',', '.')) || 0;
    const total = qty * price;
    const display = document.getElementById('item_total_display');
    if (display) {
        display.textContent = 'R$ ' + total.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
}

document.getElementById('item_quantity')?.addEventListener('input', recalcItemTotal);
document.getElementById('item_unit_price')?.addEventListener('input', recalcItemTotal);

// ===================== CONFIRM DELETE =====================
document.querySelectorAll('.confirm-delete').forEach(form => {
    form.addEventListener('submit', function (e) {
        if (!confirm('Tem certeza que deseja excluir? Esta ação não pode ser desfeita.')) {
            e.preventDefault();
        }
    });
});

// ===================== FLASH AUTO-DISMISS =====================
const flash = document.querySelector('.alert');
if (flash) {
    setTimeout(() => {
        flash.style.transition = 'opacity .5s';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 500);
    }, 4000);
}
