</main>

<footer>
    <div class="container">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem; font-family: var(--font-heading); font-size: 2rem;">Savoria.</h3>
        <p>&copy; <?= date('Y') ?> Savoria Catering. Hak Cipta Dilindungi.</p>
        <p style="font-size: 0.9rem; color: #a0aec0; margin-top: 0.5rem;">Menyajikan hidangan terbaik untuk setiap momen berharga Anda.</p>
    </div>
</footer>

<script>
    // --- Mobile Menu Toggle ---
    const mobileBtn = document.getElementById('mobile-btn');
    const navLinks = document.getElementById('nav-links');

    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // --- Toast Notification ---
    function showToast(message) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<i class="fas fa-check-circle"></i> <span>${message}</span>`;
        
        container.appendChild(toast);
        
        // Trigger reflow & add class to animate in
        setTimeout(() => toast.classList.add('show'), 10);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // --- AJAX Add to Cart ---
    const forms = document.querySelectorAll('.add-to-cart-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah reload halaman
            
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            // UI Feedback
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Kembalikan tombol
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.success) {
                    // Update cart badge
                    const badge = document.getElementById('cart-counter-badge');
                    if (badge) {
                        badge.style.display = 'inline-block';
                        badge.innerText = data.cartCount;
                    }
                    // Tampilkan notifikasi
                    showToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    });
</script>
</body>
</html>
