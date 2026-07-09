# Foundation Package Manifest + Resync Standard

This standard makes each WI plugin/package self-describing. Do not manually paste dependency JSON into the database. Put it inside the plugin package and let the installer/resync service read it.

## Required files per modern package

```text
WIAdmin/WIPlugin/<PluginName>/
├── plugin.json
├── manifest.json                       optional but recommended for richer marketplace metadata
├── config/
│   ├── pack.json                       package identity / industry-pack setup info
│   ├── dependencies.json               required/optional integration contract
│   ├── sidebar.json                    sidebar/workspace behaviour
│   ├── features.json                   feature registry and package-tier mapping
│   ├── install-map.json                files/sql/assets to copy/register
│   ├── permissions.json                permission names/roles
│   └── package-tiers.json              basic/advanced/complete feature mapping where relevant
├── sql/
├── transfer/
├── content/
├── docs/
└── tests/
```

## Resync flow

1. Scan `WIAdmin/WIPlugin/*`.
2. Read `plugin.json`, `manifest.json`, and `config/*.json`.
3. Normalise to one manifest contract.
4. Upsert safe database metadata:
   - `wi_plugin`
   - `wi_package_manifest_registry`
   - `wi_compliance_addons` for pro add-ons
   - `wi_compliance_feature_registry`
   - `wi_compliance_package_features`
   - `wi_package_sidebar_registry`
   - `wi_sidebar` only for standalone workspace entries
   - `wi_compliance_package_resync_log`

## Sidebar rule

Standalone workspaces may appear as sidebar entries when installed/enabled. Premium add-ons should usually be embedded under their parent product instead of appearing as top-level plugins.

Examples:

- WISpecs, WIRotas, WIStock, WIRestaurant, WIRooms can be standalone workspace sidebar entries.
- WIOfflinePro, WIInspectorPro, WISMSAlerts should appear inside Compliance/Notifications when enabled, not as large top-level plugin buttons.

## Completion definition

A plugin/package is not complete just because files exist. Complete means:

- manifest/config present
- install-map present
- SQL/install/uninstall present
- required runtime/admin/public files transfer correctly
- package content/templates/rules are present where relevant
- enable/disable state is registered
- sidebar behaviour is correct
- options/settings can read clean package state
- smoke test passes after install and enable
