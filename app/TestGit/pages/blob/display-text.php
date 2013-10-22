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
            <td class="contents code-display"><pre><code id="blobCtx"><?php echo htmlentities($this->blob->getContent(), ENT_QUOTES, "utf-8"); ?></code></pre></td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">hljs.highlightBlock($('#blobCtx')[0]);</script>