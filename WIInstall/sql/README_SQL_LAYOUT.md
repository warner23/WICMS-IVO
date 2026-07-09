# WICMS Installer SQL Layout

This package uses the newer ECMA-style `WIInstall` folder, but the main WICMS baseline SQL has been rebuilt from the Aurevia database export.

- `core/000_wicms_ivo_baseline.sql` is now a cleaned WICMS-only baseline derived from Aurevia core tables.
- `core/000_wicms_clean_aurevia_core_baseline.sql` is the same cleaned baseline with an explicit name.
- Compliance, Kitchen, Cyber, Labs, Electronics, Property, POS, HR/Org, Stock, scheduler and other plugin/industry SQL must live in each plugin's own `Install/` folder.
- The clean base does not ship `install.lock`.

The cleaned SQL keeps WICMS/admin/profile/site/legal/cookie/bug-reporter/media/package-manager schemas, but strips Compliance and installed plugin runtime rows.
