<?php
/**
 * Admin layout footer — closes content area and HTML.
 * Include at the bottom of every admin page.
 */
?>
        </main>
    </div><!-- /.flex-1 -->
</div><!-- /.flex.min-h-screen -->

<script>
    function toggleSidebar() {
        const sb = document.getElementById('sidebar');
        sb.classList.toggle('-translate-x-full');
    }
</script>

</body>
</html>
