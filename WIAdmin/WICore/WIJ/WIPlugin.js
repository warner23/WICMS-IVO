var WIPlugin = {

    request: function (payload, onSuccess) {
        $.ajax({
            url: "WICore/WIClass/WIAjax.php",
            type: "POST",
            dataType: "json",
            data: payload,
            success: function (res) {
                console.log("WIPlugin response:", res);

                if (typeof onSuccess === "function") {
                    onSuccess(res);
                    return;
                }

                window.location.reload();
            },
            error: function (xhr) {
                console.log("WIPlugin AJAX error:", xhr.responseText);
                alert("Plugin request failed. Check console for details.");
            }
        });
    },

    install: function (plugin) {
        WIPlugin.request({
            action: "install_plugin",
            plugin: plugin
        });
    },

    uninstall: function (plugin) {
        WIPlugin.request({
            action: "uninstall_plugin",
            plugin: plugin
        });
    },

    enable: function (plugin) {
        WIPlugin.request({
            action: "enable_plugin",
            plugin: plugin
        });
    },

    disable: function (plugin) {
        WIPlugin.request({
            action: "disable_plugin",
            plugin: plugin
        });
    },

    createOrder: function (userId, pluginSlug, price, currency, gateway) {
        WIPlugin.request({
            action: "plugin_create_order",
            user_id: userId,
            plugin_slug: pluginSlug,
            price: price,
            currency: currency || "GBP",
            gateway: gateway || "manual"
        });
    },

    completePurchase: function (userId, pluginSlug, price, currency, gateway, licenseType, subscription, subscriptionPeriod) {
        WIPlugin.request({
            action: "plugin_complete_purchase",
            user_id: userId,
            plugin_slug: pluginSlug,
            price: price,
            currency: currency || "GBP",
            gateway: gateway || "manual",
            license_type: licenseType || "lifetime",
            subscription: subscription ? 1 : 0,
            subscription_period: subscriptionPeriod || "monthly"
        }, function (res) {
            console.log("Purchase complete:", res);

            if (res.status === "success") {
                alert(
                    "Purchase complete.\n" +
                    "Invoice: " + (res.data.invoice_number || "") + "\n" +
                    "License: " + (res.data.license_key || "")
                );
                window.location.reload();
                return;
            }

            alert("Purchase failed.");
        });
    },

    validateLicense: function (licenseKey, pluginSlug) {
        WIPlugin.request({
            action: "plugin_validate_license",
            license_key: licenseKey,
            plugin_slug: pluginSlug
        }, function (res) {
            alert(res.valid ? "License valid" : "License invalid");
        });
    },

    buy: function (pluginSlug) {
        var userId = window.WI_PLUGIN_USER_ID || 1;
        var price = window.WI_PLUGIN_PRICE || 0;
        var currency = window.WI_PLUGIN_CURRENCY || "GBP";

        WIPlugin.completePurchase(
            userId,
            pluginSlug,
            price,
            currency,
            "manual",
            "lifetime",
            false,
            "monthly"
        );
    }
};