<?php
/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: visitors.php
| Location: /WIAdmin/WIInc/
| Type: Admin Include
| Layer: Backend Admin UI
| Purpose Area: Visitor log dashboard
| Version: 1.1.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Renders the backend visitor log page using the canonical WIdb/PDO layer.
| Replaces the legacy WISite::WIVisitors() call, which no longer exists.
*/

$WIdb = isset($WIdb) && $WIdb instanceof WIdb ? $WIdb : WIdb::getInstance();

$esc = static function (mixed $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$table = 'wi_visitors_log';

$tableExists = method_exists($WIdb, 'tableExists') && $WIdb->tableExists($table);

$totalVisitors = 0;
$uniqueIps = 0;
$uniquePages = 0;
$uniqueCountries = 0;
$recentVisitors = [];
$topPages = [];
$topCountries = [];

if ($tableExists) {
    $totalRows = $WIdb->select("SELECT COUNT(*) AS total_count FROM `{$table}`");
    $totalVisitors = (int) ($totalRows[0]['total_count'] ?? 0);

    if ($WIdb->columnExists($table, 'ip')) {
        $ipRows = $WIdb->select("SELECT COUNT(DISTINCT `ip`) AS total_count FROM `{$table}` WHERE `ip` <> ''");
        $uniqueIps = (int) ($ipRows[0]['total_count'] ?? 0);
    }

    if ($WIdb->columnExists($table, 'page')) {
        $pageRows = $WIdb->select("SELECT COUNT(DISTINCT `page`) AS total_count FROM `{$table}` WHERE `page` <> ''");
        $uniquePages = (int) ($pageRows[0]['total_count'] ?? 0);

        $topPages = $WIdb->select(
            "SELECT `page`, COUNT(*) AS total_count
             FROM `{$table}`
             WHERE `page` <> ''
             GROUP BY `page`
             ORDER BY total_count DESC
             LIMIT 10"
        );
    }

    if ($WIdb->columnExists($table, 'country')) {
        $countryRows = $WIdb->select("SELECT COUNT(DISTINCT `country`) AS total_count FROM `{$table}` WHERE `country` <> ''");
        $uniqueCountries = (int) ($countryRows[0]['total_count'] ?? 0);

        $topCountries = $WIdb->select(
            "SELECT `country`, COUNT(*) AS total_count
             FROM `{$table}`
             WHERE `country` <> ''
             GROUP BY `country`
             ORDER BY total_count DESC
             LIMIT 10"
        );
    }

    $recentVisitors = $WIdb->select(
        "SELECT *
         FROM `{$table}`
         ORDER BY `id` DESC
         LIMIT 50"
    );
}
?>

<aside class="right-side">
    <section class="content-header">
        <h1>
            Visitors
            <small>Control panel</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Visitors</li>
        </ol>
    </section>

    <section class="content">
        <style>
            .wi-visitors-wrap {
                padding: 4px 0 24px;
            }

            .wi-visitors-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
                margin-bottom: 18px;
            }

            .wi-visitors-card {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                padding: 18px;
                box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            }

            .wi-visitors-card span {
                display: block;
                margin-bottom: 8px;
                color: #667085;
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .04em;
            }

            .wi-visitors-card strong {
                display: block;
                color: #101828;
                font-size: 28px;
                line-height: 1;
                font-weight: 850;
            }

            .wi-visitors-panel {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                padding: 18px;
                margin-bottom: 18px;
                box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            }

            .wi-visitors-panel h3 {
                margin: 0 0 14px;
                color: #101828;
                font-size: 18px;
                font-weight: 800;
            }

            .wi-visitors-table-wrap {
                width: 100%;
                overflow-x: auto;
            }

            .wi-visitors-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 760px;
            }

            .wi-visitors-table th,
            .wi-visitors-table td {
                padding: 12px 10px;
                border-bottom: 1px solid #eaecf0;
                text-align: left;
                vertical-align: top;
                font-size: 13px;
            }

            .wi-visitors-table th {
                color: #344054;
                background: #f8fafc;
                font-weight: 800;
            }

            .wi-visitors-table td {
                color: #475467;
            }

            .wi-visitors-split {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
            }

            .wi-visitors-empty {
                padding: 18px;
                border-radius: 14px;
                background: #f8fafc;
                color: #667085;
                line-height: 1.6;
            }

            @media (max-width: 991px) {
                .wi-visitors-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .wi-visitors-split {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 575px) {
                .wi-visitors-grid {
                    grid-template-columns: 1fr;
                }

                .wi-visitors-card {
                    padding: 16px;
                }
            }
        </style>

        <div class="wi-visitors-wrap">
            <?php if (!$tableExists): ?>
                <div class="wi-visitors-panel">
                    <h3>Visitor log unavailable</h3>
                    <div class="wi-visitors-empty">
                        The <code>wi_visitors_log</code> table was not found. Visitor tracking can be reviewed later when the tracking table is installed.
                    </div>
                </div>
            <?php else: ?>
                <div class="wi-visitors-grid">
                    <div class="wi-visitors-card">
                        <span>Total visits</span>
                        <strong><?= $esc($totalVisitors); ?></strong>
                    </div>

                    <div class="wi-visitors-card">
                        <span>Unique IPs</span>
                        <strong><?= $esc($uniqueIps); ?></strong>
                    </div>

                    <div class="wi-visitors-card">
                        <span>Pages visited</span>
                        <strong><?= $esc($uniquePages); ?></strong>
                    </div>

                    <div class="wi-visitors-card">
                        <span>Countries</span>
                        <strong><?= $esc($uniqueCountries); ?></strong>
                    </div>
                </div>

                <div class="wi-visitors-split">
                    <div class="wi-visitors-panel">
                        <h3>Top pages</h3>

                        <?php if ($topPages === []): ?>
                            <div class="wi-visitors-empty">No page data found yet.</div>
                        <?php else: ?>
                            <div class="wi-visitors-table-wrap">
                                <table class="wi-visitors-table">
                                    <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th>Visits</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($topPages as $row): ?>
                                        <tr>
                                            <td><?= $esc($row['page'] ?? ''); ?></td>
                                            <td><?= $esc($row['total_count'] ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="wi-visitors-panel">
                        <h3>Top countries</h3>

                        <?php if ($topCountries === []): ?>
                            <div class="wi-visitors-empty">No country data found yet.</div>
                        <?php else: ?>
                            <div class="wi-visitors-table-wrap">
                                <table class="wi-visitors-table">
                                    <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Visits</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($topCountries as $row): ?>
                                        <tr>
                                            <td><?= $esc($row['country'] ?? ''); ?></td>
                                            <td><?= $esc($row['total_count'] ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="wi-visitors-panel">
                    <h3>Recent visitors</h3>

                    <?php if ($recentVisitors === []): ?>
                        <div class="wi-visitors-empty">No visitor records found yet.</div>
                    <?php else: ?>
                        <div class="wi-visitors-table-wrap">
                            <table class="wi-visitors-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Page</th>
                                    <th>IP</th>
                                    <th>Country</th>
                                    <th>City</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($recentVisitors as $visitor): ?>
                                    <tr>
                                        <td><?= $esc($visitor['id'] ?? ''); ?></td>
                                        <td><?= $esc($visitor['page'] ?? ''); ?></td>
                                        <td><?= $esc($visitor['ip'] ?? ''); ?></td>
                                        <td><?= $esc($visitor['country'] ?? ''); ?></td>
                                        <td><?= $esc($visitor['city'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</aside>

<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
<script type="text/javascript" src="WICore/WIJ/WIStats.js"></script>