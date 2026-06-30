/* =========================================================
   WONOGIRI WISATA - MAIN JS
   Interaksi sederhana: toggle menu mobile, navbar scroll effect
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Toggle menu mobile ----
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function () {
            navMenu.classList.toggle('show-mobile');
        });
    }

    // ---- Navbar berubah style saat discroll ----
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 20) {
                navbar.style.boxShadow = '0 4px 18px rgba(0,0,0,0.06)';
            } else {
                navbar.style.boxShadow = 'none';
            }
        });
    }

    // ---- Tombol favorit (toggle ikon hati) ----
    const favBtn = document.querySelector('.icon-btn[title="Favorit"]');
    if (favBtn) {
        favBtn.addEventListener('click', function () {
            const icon = favBtn.querySelector('i');
            icon.classList.toggle('fa-regular');
            icon.classList.toggle('fa-solid');
            icon.style.color = icon.classList.contains('fa-solid') ? '#d4711f' : '';
        });
    }

    // ---- Dropdown pemilih bahasa (auto-translate) ----
    const langSwitcher = document.getElementById('langSwitcher');
    const langBtn = document.getElementById('langBtn');
    const langDropdown = document.getElementById('langDropdown');
    const langCurrentLabel = document.getElementById('langCurrentLabel');
    const langOptions = document.querySelectorAll('.lang-option');

    const labelMap = { id: 'ID', en: 'EN', zh: '中文', ja: 'JP', ar: 'AR', ko: 'KR' };
    const googleCodeMap = { id: 'id', en: 'en', zh: 'zh-CN', ja: 'ja', ar: 'ar', ko: 'ko' };

    if (langBtn && langDropdown) {
        langBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            langDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (langSwitcher && !langSwitcher.contains(e.target)) {
                langDropdown.classList.remove('show');
            }
        });
    }

    // Fungsi untuk benar-benar memicu Google Translate berdasarkan kode bahasa
    function terapkanBahasa(kode) {
        const targetCode = googleCodeMap[kode] || 'id';

        // Cara paling stabil untuk memicu Google Translate widget secara terprogram:
        // mengatur cookie googtrans lalu memicu select onchange dari combo bawaan Google.
        const cocokSelect = document.querySelector('.goog-te-combo');
        if (cocokSelect) {
            cocokSelect.value = targetCode;
            cocokSelect.dispatchEvent(new Event('change'));
        } else {
            // Jika widget belum siap, set cookie dan reload agar translate berlaku saat halaman dimuat ulang
            document.cookie = 'googtrans=/id/' + targetCode + '; path=/';
            document.cookie = 'googtrans=/id/' + targetCode + '; domain=' + window.location.hostname + '; path=/';
            location.reload();
        }
        localStorage.setItem('wonogiri_lang', kode);
    }

    langOptions.forEach(function (opt) {
        opt.addEventListener('click', function () {
            const kode = this.getAttribute('data-lang');
            langOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            langCurrentLabel.textContent = labelMap[kode] || 'ID';
            langDropdown.classList.remove('show');
            terapkanBahasa(kode);
        });
    });

    // Saat halaman dimuat, terapkan kembali bahasa yang tersimpan sebelumnya (jika bukan default ID)
    const bahasaTersimpan = localStorage.getItem('wonogiri_lang');
    if (bahasaTersimpan && bahasaTersimpan !== 'id') {
        langOptions.forEach(o => o.classList.remove('active'));
        const target = document.querySelector('.lang-option[data-lang="' + bahasaTersimpan + '"]');
        if (target) target.classList.add('active');
        if (langCurrentLabel) langCurrentLabel.textContent = labelMap[bahasaTersimpan] || 'ID';

        // Tunggu widget Google Translate selesai dimuat sebelum menerapkan
        let percobaan = 0;
        const interval = setInterval(function () {
            percobaan++;
            const cocokSelect = document.querySelector('.goog-te-combo');
            if (cocokSelect) {
                clearInterval(interval);
                cocokSelect.value = googleCodeMap[bahasaTersimpan];
                cocokSelect.dispatchEvent(new Event('change'));
            }
            if (percobaan > 40) clearInterval(interval); // berhenti coba setelah ~10 detik
        }, 250);
    }

});
