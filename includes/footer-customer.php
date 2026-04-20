<?php
/**
 * Customer layout footer — closes main + sidebar JS.
 */
?>
</main>

<script>
    function toggleSidebar() {
        document.body.classList.toggle('collapsed');
        const icon = document.getElementById('collapse-icon');
        icon.style.transform = document.body.classList.contains('collapsed')
            ? 'rotate(180deg)' : '';
    }
</script>

</body>
</html>
