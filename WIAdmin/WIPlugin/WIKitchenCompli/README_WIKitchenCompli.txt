WIKitchenCompli
================

This package is a full structural refactor of the unfinished compliance plugin into a hospitality-focused compliance operating system foundation for WICMS.

What was kept intact
- Existing plugin folder structure was preserved.
- Legacy file names such as WIBlog.php / blog.php were retained where needed for compatibility.
- Existing theme and asset folders were not removed.

What was refactored
- Plugin metadata updated from blog scaffold to kitchen compliance.
- New PHP 8.2-safe compliance core added.
- Installer rewritten.
- AJAX endpoint rewritten around compliance actions.
- Legacy WIBlog class now acts as a compatibility wrapper around WICompliance.
- Admin dashboard and settings screens replaced with compliance-focused interfaces.
- SQL schema added for wizard, legal register, checklists, records, audits, documents, GDPR, reminders, archive, equipment, training and add-ons.
- Front-end/public compliance entry pages left in place and cleaned as routing shells.

Main entry points
- plugin.json
- Install/WIKitchenCompliInstall.php
- WICore/WIClass/WICompliance.php
- WICore/WIClass/WIAjax.php
- WICore/WIJ/WICompliance.js
- compliance/Blog.php
- compliance/WIBlog.php
- compliance/WIBlog_Options.php
- WI_Compliance.sql

Compatibility note
This package is designed to fit into your existing WICMS approach without changing the plugin structure. Since your core WICMS environment is custom, a few integration touchpoints may still need light alignment with your live WIdb / auth / admin shell classes.
