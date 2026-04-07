<footer class="site-footer">
    <div class="container footer-wrap">

        <div class="footer-grid">

            <!-- BRAND -->
            <div class="footer-col">
                <h3>Glowé</h3>
                <p>
                    Skincare berkualitas dengan bahan alami untuk membantu kulitmu tampil sehat, cerah, dan glowing setiap hari.
                </p>
            </div>

            <!-- KONTAK -->
            <div class="footer-col">
                <h4>Kontak</h4>
                <p>Email: <a href="mailto:contact@glowe.com">contact@glowe.com</a></p>
                <p>Alamat: Telang, Bangkalan, Jawa Timur</p>
            </div>

            <!-- WHATSAPP -->
            <div class="footer-col">
                <p>Butuh bantuan? Hubungi kami via WhatsApp:</p>
                <p>
                    <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" target="_blank" class="wa-link">
                        WA: <?= WHATSAPP_NUMBER ?>
                    </a>
                </p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Glowé. All rights reserved.</p>
        </div>

    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>