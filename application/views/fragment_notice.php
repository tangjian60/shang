<div class="list-group">
    <?php
    foreach ($Notices as $notice_item) {
        echo '<a href="' . base_url('pages/notice_info?id=' . $notice_item->id) . '" class="list-group-item new-item">';
        echo '<span class="badge new-badge">' . substr($notice_item->gmt_create, 0, 10) . '</span>';
        echo '<span class="glyphicon glyphicon-bullhorn"></span>' . $notice_item->title . '</a>';
    }
    ?>
</div>