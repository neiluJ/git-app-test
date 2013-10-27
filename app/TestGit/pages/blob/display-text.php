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
            <td class="contents code-display"><pre><code id="blobCtx"<?php if($this->language != false): ?> class="<?php echo $this->language; ?>"<?php endif; ?>><?php echo htmlentities(trim($this->blob->getContent()), ENT_QUOTES, "utf-8"); ?></code></pre></td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">hljs.highlightBlock($('#blobCtx')[0]);</script>
<p>Download this file: <a href="<?php echo $this->_helper->url('BlobRaw', array('name' => $this->name, 'path' => $this->path, 'branch' => $this->branch), true); ?>">click here</a></p>
