/**
 * منطق أزرار الصعود والنزول في الصفحة
 */

document.addEventListener("DOMContentLoaded", function() {
    const scrollTopBtn = document.getElementById('scroll-to-top-btn');
    const scrollBottomBtn = document.getElementById('scroll-to-bottom-btn');
    if(scrollTopBtn && scrollBottomBtn) {
        window.addEventListener('scroll', function() {
            window.scrollY > 300 ? scrollTopBtn.style.display = 'block' : scrollTopBtn.style.display = 'none';
        });
        scrollTopBtn.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
        scrollBottomBtn.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); });
    }
});