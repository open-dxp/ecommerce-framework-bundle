/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */



opendxp.registerNS("opendxp.bundle.ecommerce.startup");

/**
 * @private
 */
opendxp.bundle.ecommerce.startup = Class.create({

    useOrderDetailTab : true,

    initialize: function () {
        document.addEventListener(opendxp.events.preMenuBuild, this.preMenuBuild.bind(this));
        document.addEventListener(opendxp.events.postOpenObject, this.postOpenObject.bind(this));
    },

    preMenuBuild: function (e) {
        const perspectiveCfg = opendxp.globalmanager.get("perspective");
        if (!perspectiveCfg.inToolbar("ecommerce")) {
            return
        }
        const user = opendxp.globalmanager.get("user");
        let menu = e.detail.menu;

        let items = [];

        menu.ecommerce = {
            label: t('bundle_ecommerce_mainmenu'),
            iconCls: 'bundle_ecommerce_nav_icon_shopping_cart',
            priority: 42,
            items: items,
            shadow: false,
            cls: "opendxp_navigation_flyout"
        };

        let config = opendxp.bundle.EcommerceFramework.config;

        // pricing rules
        if (perspectiveCfg.inToolbar("ecommerce.rules")
            && user.isAllowed("bundle_ecommerce_pricing_rules")
            && (!config.menu || config.menu.pricing_rules.enabled)) {
            // add pricing rules to menu
            const pricingPanelId = "bundle_ecommerce_pricing_config";
            const item = {
                text: t("bundle_ecommerce_pricing_rules"),
                iconCls: "opendxp_nav_icon_commerce_pricing_rules",
                priority: 10,
                handler: function () {
                    try {
                        opendxp.globalmanager.get(pricingPanelId).activate();
                    }
                    catch (e) {
                        opendxp.globalmanager.add(pricingPanelId, new opendxp.bundle.EcommerceFramework.pricing.config.panel(pricingPanelId));
                    }
                }
            };

            items.push(item);
        }

        // order backend
        if (perspectiveCfg.inToolbar("ecommerce.orderbackend")
            && user.isAllowed("bundle_ecommerce_back-office_order")
            && (!config.menu || config.menu.order_list.enabled)) {
            const orderPanelId = "bundle_ecommerce_back-office_order";
            const item = {
                text: t("bundle_ecommerce_back-office_order"),
                iconCls: "opendxp_nav_icon_commerce_backoffice",
                priority: 20,
                handler: function () {
                    try {
                        opendxp.globalmanager.get(orderPanelId).activate();
                    }
                    catch (e) {
                        opendxp.globalmanager.add(orderPanelId, new opendxp.tool.genericiframewindow(orderPanelId, config.menu.order_list.route, "bundle_ecommerce_back-office_order", t('bundle_ecommerce_back-office_order')));
                    }
                }
            };

            items.push(item);
        }
        menu.ecommerce.items = items;
    },

    postOpenObject: function (e) {
        if (opendxp.globalmanager.get("user").isAllowed("bundle_ecommerce_pricing_rules")) {

            if (e.detail.type == "object" && e.detail.object.data.general.className == "OnlineShopVoucherSeries") {
                const tab = new opendxp.bundle.EcommerceFramework.VoucherSeriesTab(e.detail.object, e.detail.type);

                e.detail.object.tab.items.items[1].insert(1, tab.getLayout());
                e.detail.object.tab.items.items[1].updateLayout();
                opendxp.layout.refresh();
            }
        }

        if (opendxp.globalmanager.get("user").isAllowed("bundle_ecommerce_back-office_order")) {

            if (e.detail.type == "object" && e.detail.object.data.general.className == "OnlineShopOrder"  && this.useOrderDetailTab) {
                const tab = new opendxp.bundle.EcommerceFramework.OrderTab(e.detail.object, e.detail.type);
                e.detail.object.tab.items.items[1].insert(0, tab.getLayout());
                e.detail.object.tab.items.items[1].updateLayout();
                e.detail.object.tab.items.items[1].setActiveTab(0);
                opendxp.layout.refresh();
            }
        }
    }
});

const opendxpBundleEcommerce = new opendxp.bundle.ecommerce.startup();
