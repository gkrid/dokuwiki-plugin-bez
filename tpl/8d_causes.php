<?php if (count($causes) > 0): ?>	
    <table>
    <?php foreach ($causes as $cause): ?>
            <tr>
            <td>
                <a href="?id=<?php echo $this->id('issue', 'id', $cause->issue) ?>#k<?php echo $cause->id ?>">
                        #p<?php echo $cause->id ?>
                </a>
            </td>
            <td>
            <?php echo $cause->content_cache ?>
            </td>
            </tr>
    <?php endforeach ?>
    </table>
<?php else: ?>
    <p><i><?php echo $bezlang['not_relevant'] ?></i></p>
<?php endif ?>