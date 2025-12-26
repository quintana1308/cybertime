<?php
/**
 * CYBERTIME - Footer del Panel de Administración
 */

if (!defined('ADMIN_PAGE')) {
    die('Acceso directo no permitido');
}
?>
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="assets/js/admin.js"></script>
    <?php if (isset($extra_scripts)): ?>
        <?php foreach ($extra_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
