<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin', function (BreadcrumbTrail $trail) {
    $trail->push('Admin', route('admin.index'));
});

/*
* Admin >
*/

/*
 * Clients >
 */
Breadcrumbs::for('admin.clients', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Clients'), route('admin.clients'));
});

Breadcrumbs::for('admin.clients.edit', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.clients');
    $trail->push($user->name, route('admin.clients.edit', $user));
});

Breadcrumbs::for('admin.clients.products', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.clients.edit', $user);
    $trail->push(__('Services'), route('admin.clients.products', $user));
});

Breadcrumbs::for('admin.clients.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.clients');
    $trail->push(__('Create'), route('admin.clients.create'));
});
/*
* < Clients 
*/

/*
* Products >
*/
Breadcrumbs::for('admin.products', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Products'), route('admin.products'));
});

Breadcrumbs::for('admin.products.edit', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('admin.products');
    $trail->push($product->name, route('admin.products.edit', $product));
});

Breadcrumbs::for('admin.products.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.products');
    $trail->push(__('Create'), route('admin.products.create'));
});

Breadcrumbs::for('admin.products.pricing', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('admin.products.edit', $product);
    $trail->push(__('Pricing'), route('admin.products.pricing', $product));
});

Breadcrumbs::for('admin.products.extension', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('admin.products.edit', $product);
    $trail->push(__('Extension'), route('admin.products.extension', $product));
});

Breadcrumbs::for('admin.products.upgrade', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('admin.products.edit', $product);
    $trail->push(__('Upgrades'), route('admin.products.upgrade', $product));
});


/*
* < Products 
*/
/*
* Orders >
*/
Breadcrumbs::for('admin.orders', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Orders'), route('admin.orders'));
});

Breadcrumbs::for('admin.orders.show', function (BreadcrumbTrail $trail, $order) {
    $trail->parent('admin.orders');
    $trail->push($order->id . ' - ' . $order->user->first_name, route('admin.orders.show', $order));
});

/*
* < Orders 
*/
/*
* Invoices >
*/
Breadcrumbs::for('admin.invoices', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Invoices'), route('admin.invoices'));
});

Breadcrumbs::for('admin.invoices.show', function (BreadcrumbTrail $trail, $invoice) {
    $trail->parent('admin.invoices');
    $trail->push($invoice->id . ' - ' . $invoice->user->first_name, route('admin.invoices.show', $invoice));
});

Breadcrumbs::for('admin.invoices.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.invoices');
    $trail->push(__('Create'), route('admin.invoices.create'));
});

/*
* < Invoices 
*/
/*
* Configurable Options >
*/
Breadcrumbs::for('admin.configurable-options', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Configurable Options'), route('admin.configurable-options'));
});

Breadcrumbs::for('admin.configurable-options.edit', function (BreadcrumbTrail $trail, $configurableOption) {
    $trail->parent('admin.configurable-options');
    $trail->push($configurableOption->name, route('admin.configurable-options.edit', $configurableOption));
});

Breadcrumbs::for('admin.configurable-options.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.configurable-options');
    $trail->push(__('Create'), route('admin.configurable-options.create'));
});
/*
* < Configurable Options 
*/
/*
* Tickets >
*/
Breadcrumbs::for('admin.tickets', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Tickets'), route('admin.tickets'));
});

Breadcrumbs::for('admin.tickets.show', function (BreadcrumbTrail $trail, $ticket) {
    $trail->parent('admin.tickets');
    $trail->push($ticket->id . ' - ' . $ticket->title, route('admin.tickets.show', $ticket));
});

Breadcrumbs::for('admin.tickets.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.tickets');
    $trail->push(__('Create'), route('admin.tickets.create'));
});

/*
* < Tickets 
*/
/*
* Emails >
*/
Breadcrumbs::for('admin.email', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Emails'), route('admin.email'));
});

Breadcrumbs::for('admin.email.templates', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.email');
    $trail->push(__('Templates'), route('admin.email.templates'));
});

Breadcrumbs::for('admin.email.template', function (BreadcrumbTrail $trail, $template) {
    $trail->parent('admin.email.templates');
    $trail->push($template->name, route('admin.email.template', $template));
});
/*
* < Emails 
*/
/*
* Settings >
*/
Breadcrumbs::for('admin.settings', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Settings'), route('admin.settings'));
});
/*
* < Settings 
*/
/*
* Coupons >
*/
Breadcrumbs::for('admin.coupons', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Coupons'), route('admin.coupons'));
});

Breadcrumbs::for('admin.coupons.edit', function (BreadcrumbTrail $trail, $coupon) {
    $trail->parent('admin.coupons');
    $trail->push($coupon->code, route('admin.coupons.edit', $coupon));
});

Breadcrumbs::for('admin.coupons.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.coupons');
    $trail->push(__('Create'), route('admin.coupons.create'));
});
/*
* < Coupons 
*/
/*
* Roles >
*/
Breadcrumbs::for('admin.roles', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Roles'), route('admin.roles'));
});

Breadcrumbs::for('admin.roles.edit', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('admin.roles');
    $trail->push($role->name, route('admin.roles.edit', $role));
});

Breadcrumbs::for('admin.roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles');
    $trail->push(__('Create'), route('admin.roles.create'));
});
/*
* < Roles 
*/
/*
* Announcements >
*/
Breadcrumbs::for('admin.announcements', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Announcements'), route('admin.announcements'));
});

Breadcrumbs::for('admin.announcements.edit', function (BreadcrumbTrail $trail, $announcement) {
    $trail->parent('admin.announcements');
    $trail->push($announcement->title, route('admin.announcements.edit', $announcement));
});

Breadcrumbs::for('admin.announcements.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.announcements');
    $trail->push(__('Create'), route('admin.announcements.create'));
});

/*
* < Announcements 
*/
/*
* Taxes >
*/
Breadcrumbs::for('admin.taxes', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push(__('Taxes'), route('admin.taxes'));
});
