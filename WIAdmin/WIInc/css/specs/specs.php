<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WISpecs
 * Project: WI Ecosystem
 * File: specs.php
 * Location: /WIAdmin/WIInc/site/specs/specs.php
 * Type: Admin view
 * Layer: UI only
 * Purpose Area: WISpecs admin workspace
 * Version: 1.2.0
 * Created: 2026-06-01
 * Last Updated: 2026-06-01
 * Status: Production-ready Batch 1 refactor
 *
 * Summary:
 * WISpecs admin shell. This file renders the UI structure only. Data,
 * persistence and business rules are handled by WISpecsService and
 * WISpecsRepository through the plugin AJAX controller.
 */

declare(strict_types=1);
?>
<section class="wi-specs-shell" data-wispecs-root data-wispecs-endpoint="WIPlugin/WISpecs/WICore/WIAjax/WISpecsAjax.php">
    <header class="wi-specs-hero">
        <div>
            <p class="wi-specs-kicker">WISpecs</p>
            <h1>Specification Centre</h1>
            <p>Standalone menu, kitchen-spec and bar/cocktail control. Compliance, restaurant, rooms and bar plugins can read from WISpecs, but WISpecs remains the source of truth.</p>
        </div>
        <div class="wi-specs-hero-actions">
            <button type="button" class="wi-specs-btn wi-specs-btn--primary" data-wispecs-refresh>Refresh</button>
            <button type="button" class="wi-specs-btn" data-wispecs-new>New spec</button>
        </div>
    </header>

    <section class="wi-specs-context-bar" aria-label="WISpecs business, site and menu selectors">
        <label>Business<select data-wispecs-business><option value="">All businesses / standalone</option></select></label>
        <label>Site<select data-wispecs-site><option value="">All sites / global menu</option></select></label>
        <label>Menu<select data-wispecs-menu><option value="">All menus</option></select></label>
    </section>

    <nav class="wi-specs-tabs" aria-label="WISpecs sections">
        <button type="button" class="is-active" data-wispecs-tab="dashboard">Dashboard</button>
        <button type="button" data-wispecs-tab="specs">Specs</button>
        <button type="button" data-wispecs-tab="menus">Menus</button>
        <button type="button" data-wispecs-tab="availability">Availability</button>
        <button type="button" data-wispecs-tab="bridges">Bridges</button>
    </nav>

    <main class="wi-specs-main">
        <section data-wispecs-panel="dashboard">
            <section class="wi-specs-summary" data-wispecs-summary></section>
            <section class="wi-specs-note-grid">
                <article><h3>Standalone plugin</h3><p>WISpecs can run without Compliance installed.</p></article>
                <article><h3>Menu display</h3><p>Menus show what is currently on each menu and how many items are live.</p></article>
                <article><h3>Availability control</h3><p>Availability controls on-menu, off-menu, limited count, paused and out-of-stock states.</p></article>
            </section>
            <section class="wi-specs-install-health" data-wispecs-install-health hidden></section>
        </section>

        <section data-wispecs-panel="specs" hidden>
            <section class="wi-specs-toolbar">
                <input type="search" data-wispecs-search placeholder="Search specs, sections, ingredients or methods">
                <select data-wispecs-status>
                    <option value="all">All statuses</option>
                    <option value="active">Active</option>
                    <option value="draft">Draft</option>
                    <option value="pending_review">Pending review</option>
                    <option value="approved">Approved</option>
                    <option value="archived">Archived</option>
                </select>
            </section>

            <nav class="wi-specs-tabs wi-specs-tabs--inner" aria-label="Spec type filters">
                <button type="button" class="is-active" data-wispecs-domain="all">All menu specs</button>
                <button type="button" data-wispecs-domain="kitchen">Kitchen / Food</button>
                <button type="button" data-wispecs-domain="bar">Bar / Drinks / Cocktails</button>
            </nav>

            <nav class="wi-specs-section-tabs" aria-label="Spec menu sections" data-wispecs-section-tabs></nav>

            <section class="wi-specs-spec-centre">
                <aside class="wi-specs-item-rail" aria-label="Spec items" data-wispecs-list></aside>
                <section class="wi-specs-preview" data-wispecs-preview>
                    <div class="wi-specs-empty">Select a spec to view ingredients, method and menu status.</div>
                </section>
            </section>
        </section>

        <section data-wispecs-panel="menus" hidden>
            <section class="wi-specs-tab-intro">
                <h2>Menus</h2>
                <p>Menus are a read-only live display of what is currently on each menu. Use Availability to change item state, counts and out-of-stock status.</p>
            </section>
            <section class="wi-specs-menu-list" data-wispecs-menu-list></section>
            <section data-wispecs-menu-items></section>
        </section>

        <section data-wispecs-panel="availability" hidden>
            <section class="wi-specs-tab-intro">
                <h2>Availability</h2>
                <p>Set menu availability here. This is the control area for on-menu, available, limited count, paused, off-menu and out-of-stock states.</p>
            </section>
            <section data-wispecs-availability-list></section>
        </section>

        <section data-wispecs-panel="bridges" hidden>
            <section class="wi-specs-bridge-grid" data-wispecs-bridge></section>
        </section>
    </main>

    <section class="wi-specs-drawer" data-wispecs-detail hidden></section>
    <section class="wi-specs-drawer" data-wispecs-editor hidden></section>
</section>
