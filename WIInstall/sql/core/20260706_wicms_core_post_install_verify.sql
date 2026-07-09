/*
|--------------------------------------------------------------------------
| WICMS Core Post-Install Table Verification
|--------------------------------------------------------------------------
| Read-only smoke check. This does not install missing tables; it confirms
| the core tables expected by the WICMS frontend/admin bootstrap exist.
|--------------------------------------------------------------------------
*/
SELECT 'wi_site' AS table_name, COUNT(*) AS exists_count
FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_site'
UNION ALL SELECT 'wi_members', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_members'
UNION ALL SELECT 'wi_user_details', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_user_details'
UNION ALL SELECT 'wi_user_roles', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_user_roles'
UNION ALL SELECT 'wi_page', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_page'
UNION ALL SELECT 'wi_mod', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_mod'
UNION ALL SELECT 'wi_modules', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_modules'
UNION ALL SELECT 'wi_menu', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_menu'
UNION ALL SELECT 'wi_sidebar', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_sidebar'
UNION ALL SELECT 'wi_css', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_css'
UNION ALL SELECT 'wi_scripts', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_scripts'
UNION ALL SELECT 'wi_theme', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_theme'
UNION ALL SELECT 'wi_header', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_header'
UNION ALL SELECT 'wi_footer', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_footer'
UNION ALL SELECT 'wi_social', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_social'
UNION ALL SELECT 'wi_social_logins', COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_social_logins';
