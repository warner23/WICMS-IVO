<?php

declare(strict_types=1);

define('INCLUDE_CHECK', true);
require_once dirname(__DIR__) . '/WICore/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WI Kitchen Compliance</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f5f7fb;color:#17212f;margin:0}
        .wic-admin{max-width:1280px;margin:0 auto;padding:24px}
        .wic-header{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:24px}
        .wic-header h1{margin:0;font-size:30px}
        .wic-sub{color:#5f6b7a}
        .wic-layout{display:grid;grid-template-columns:260px 1fr;gap:20px}
        .wic-sidebar,.wic-main,.wic-panel,.wic-card{background:#fff;border:1px solid #d8e0ea;border-radius:16px;box-shadow:0 8px 28px rgba(12,35,64,.06)}
        .wic-sidebar{padding:20px}
        .wic-main{padding:20px}
        .wic-nav{list-style:none;margin:0;padding:0;display:grid;gap:10px}
        .wic-nav a{text-decoration:none;color:#17212f;font-weight:700;display:block;padding:10px 12px;border-radius:12px;background:#f7fafc}
        .wic-wrap{display:grid;gap:18px}
        .wic-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
        .wic-grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .wic-card,.wic-panel{padding:18px}
        .wic-label{display:block;color:#627284;font-size:13px;margin-bottom:10px}
        .wic-card strong{font-size:30px}
        .wic-table{width:100%;border-collapse:collapse}
        .wic-table th,.wic-table td{padding:10px;border-bottom:1px solid #e8edf3;text-align:left}
        .wic-list{margin:0;padding-left:18px}
        .wic-actions{display:flex;gap:12px;flex-wrap:wrap}
        .wic-btn{appearance:none;border:0;border-radius:12px;padding:11px 16px;background:#17212f;color:#fff;font-weight:700;cursor:pointer}
        .wic-forms{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:16px}
        .wic-form{display:grid;gap:10px}
        .wic-form input,.wic-form select,.wic-form textarea{width:100%;padding:10px 12px;border:1px solid #ced7e2;border-radius:10px;box-sizing:border-box}
        .wic-form-output{font-size:13px;color:#38526b;min-height:18px}
        @media (max-width:980px){.wic-layout,.wic-grid,.wic-grid-2,.wic-forms{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wic-admin">
    <div class="wic-header">
        <div>
            <h1>WI Kitchen Compliance</h1>
            <div class="wic-sub">Hospitality & catering compliance operating system for WICMS</div>
        </div>
    </div>
    <div class="wic-layout">
        <aside class="wic-sidebar">
            <h3>Menu</h3>
            <ul class="wic-nav">
                <?php foreach ($compliance->getMenu() as $key => $label): ?>
                    <li><a href="#<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </aside>
        <main class="wic-main">
            <section id="wic-dashboard"></section>
            <section class="wic-forms">
                <div class="wic-panel">
                    <h3>Setup Wizard</h3>
                    <form class="wic-form" data-wic-form="saveSetup">
                        <input type="text" name="business_name" placeholder="Business name" required>
                        <select name="business_type"><option>restaurant</option><option>cafe</option><option>pub</option><option>bar</option><option>catering</option></select>
                        <select name="risk_profile"><option>low</option><option selected>medium</option><option>high</option></select>
                        <input type="number" name="site_count" min="1" value="1">
                        <select name="retention_profile"><option>standard</option><option>enhanced</option></select>
                        <button class="wic-btn" type="submit">Save setup</button>
                        <div class="wic-form-output"></div>
                    </form>
                </div>
                <div class="wic-panel">
                    <h3>Add Checklist</h3>
                    <form class="wic-form" data-wic-form="addChecklist">
                        <input type="text" name="title" placeholder="Checklist title" required>
                        <select name="category"><option>opening</option><option>closing</option><option>delivery</option><option>cleaning</option><option>gdpr</option></select>
                        <select name="frequency"><option>daily</option><option>weekly</option><option>monthly</option></select>
                        <select name="assigned_role"><option>manager</option><option>chef</option><option>supervisor</option><option>staff</option></select>
                        <select name="evidence_required"><option value="1">Evidence required</option><option value="0">No evidence</option></select>
                        <button class="wic-btn" type="submit">Create checklist</button>
                        <div class="wic-form-output"></div>
                    </form>
                </div>
                <div class="wic-panel">
                    <h3>Add Reminder</h3>
                    <form class="wic-form" data-wic-form="addReminder">
                        <input type="text" name="title" placeholder="Reminder title" required>
                        <select name="priority"><option>low</option><option selected>medium</option><option>high</option><option>critical</option></select>
                        <input type="date" name="due_date" value="<?= date('Y-m-d'); ?>">
                        <select name="owner_role"><option>manager</option><option>supervisor</option><option>admin</option></select>
                        <button class="wic-btn" type="submit">Create reminder</button>
                        <div class="wic-form-output"></div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</div>
<script src="../WICore/WIJ/WICompliance.js"></script>
</body>
</html>
