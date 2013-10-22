<table class="blob">
    <tbody>
        <tr>
            <td class="ln">
                <?php
                $totalLines = count(explode("\n", $this->blob->getContent()));
                for ($i = 1; $i <= $totalLines; $i++) {
                    echo '<span>'. $i .'</span>';
                }
                ?>
            </td>
            <td class="contents code-display"><pre><code><?php echo htmlentities($this->blob->getContent(), ENT_QUOTES, "utf-8"); ?></code></pre></td>
        </tr>
    </tbody>
</table>

<script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/js/highlight.js/highlight.pack.js"></script>