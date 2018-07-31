<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(Mailer $server, $i) {
?>
<tbody class="queue-mail-php-<?= $i ?> queue-mail-mailer-<?= $i ?>">
<tr>
    <th scope="row">
    </th>
    <td>
    </td>
</tr>
</tbody>
    <?php
};