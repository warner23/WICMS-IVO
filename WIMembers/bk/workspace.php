<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Project: WI Ecosystem
| File: workspace.php
| Location: /WIMembers/workspace.php
| Type: Front-Side Page
| Layer: UI Shell
| Purpose Area: Adaptive Member Workspace
| Version: 1.0.0
| Created: 2026-05-08
| Last Updated: 2026-05-08
| Status: Active
| Batch: MB-01 — WIMembers Adaptive Workspace Shell
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Logged-in member workspace. This page is UI only. Data loads through
| WIMemberWorkspaceAjax.php and is rendered by WIMemberWorkspace.js.
|--------------------------------------------------------------------------
*/

if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Member Workspace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="WICore/WICSS/WIMemberWorkspace.css">
</head>
<body>

<main
    class="wi-member-workspace"
    data-wi-member-workspace="root"
    data-endpoint="WICore/WIAjax/WIMemberWorkspaceAjax.php"
>
    <section class="wi-member-loading" data-workspace-loading>
        Loading your workspace…
    </section>
</main>

<script src="../WIAdmin/WIInc/js/jquery_new.js"></script>
<script src="WICore/WIJ/WIMemberWorkspace.js"></script>

</body>
</html>