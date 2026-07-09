<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIEmail.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Email
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIEmail
{
    public function confirmationEmail(string $email, string $key): bool
    {
        return true;
    }

    public function passwordResetEmail(string $email, string $key): bool
    {
        return true;
    }
}
