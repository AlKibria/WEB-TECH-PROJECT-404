<?php $pageTitle = 'Editorial Calendar'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editorial Calendar</h1>
        <p class="page-sub">See when your submitted articles are scheduled for publication.</p>
    </div>
</div>

<?php if (empty($calendar)): ?>
<div class="empty-state">
    <div class="empty-icon">📅</div>
    <h3>Nothing scheduled yet</h3>
    <p>Once an editor schedules one of your articles, it will appear here.</p>
    <a href="<?= BASE_URL ?>/index.php?page=author_articles&status=submitted" class="btn btn-outline">View Submitted Articles</a>
</div>
<?php else: ?>
<div class="card">
    <table class="articles-table">
        <thead>
            <tr>
                <th>Article</th>
                <th>Scheduled Date</th>
                <th>Status</th>
                <th>Scheduled By</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($calendar as $entry): ?>
        <tr>
            <td>
                <div class="art-title"><?= htmlspecialchars($entry['article_title']) ?></div>
            </td>
            <td>
                <strong><?= date('M d, Y', strtotime($entry['scheduled_date'])) ?></strong><br>
                <span style="font-size:.8rem;color:var(--text-muted);"><?= date('H:i', strtotime($entry['scheduled_date'])) ?></span>
                <?php if (strtotime($entry['scheduled_date']) > time()): ?>
                <span class="badge badge-approved" style="margin-left:.5rem;">Upcoming</span>
                <?php endif; ?>
            </td>
            <td><span class="badge badge-<?= $entry['article_status'] ?>"><?= ucfirst(str_replace('_',' ',$entry['article_status'])) ?></span></td>
            <td><?= htmlspecialchars($entry['editor_name']) ?></td>
            <td style="font-size:.85rem;color:var(--text-muted);"><?= htmlspecialchars($entry['note'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>Calendar View</h3></div>
    <div class="cal-grid">
        <?php
        $byDate = [];
        foreach ($calendar as $entry) {
            $d = date('Y-m-d', strtotime($entry['scheduled_date']));
            $byDate[$d][] = $entry;
        }
        $first = min(array_keys($byDate));
        $last  = max(array_keys($byDate));
        $start = new DateTime($first);
        $end   = new DateTime($last);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period   = new DatePeriod($start, $interval, $end);
        foreach ($period as $date):
            $key = $date->format('Y-m-d');
            $isToday = $key === date('Y-m-d');
        ?>
        <div class="cal-day <?= $isToday ? 'cal-today' : '' ?> <?= isset($byDate[$key]) ? 'cal-has-event' : '' ?>">
            <div class="cal-day-num"><?= $date->format('d') ?><span class="cal-month"><?= $date->format('M') ?></span></div>
            <?php if (isset($byDate[$key])): ?>
                <?php foreach ($byDate[$key] as $e): ?>
                <div class="cal-event"><?= htmlspecialchars(mb_substr($e['article_title'],0,25)) ?><?= strlen($e['article_title'])>25?'…':'' ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
