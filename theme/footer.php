</div><!-- #content -->

<footer class="site-footer" role="contentinfo" style="background-color: #2e7d32; color: white; padding: 40px 20px; margin-top: 50px; text-align: center;">
    <div class="footer-container">
        <p>&copy; <?php echo date( 'Y' ); ?> - <?php bloginfo( 'name' ); ?>. Association de loi 1901.</p>
        <p>
            <a href="<?php echo esc_url( home_url( '/mentions-legales' ) ); ?>" style="color: #c8e6c9;">Mentions légales</a> |
            <a href="<?php echo esc_url( home_url( '/politique-de-confidentialite' ) ); ?>" style="color: #c8e6c9;">Politique de confidentialité</a>
        </p>
    </div>
</footer>

<?php wp_footer(); // Fonction vitale pour charger les scripts de pied de page ?>
</body>
</html>