# Azarniewicz Sylius InPost Plugin

Simple InPost Paczkomat integration for Sylius 2.0+

> **Inspired by** [BitBagCommerce/SyliusInPostPlugin](https://github.com/BitBagCommerce/SyliusInPostPlugin) but stripped of things unnecessary for a simple checkout with selecting an InPost point. This plugin focuses on the core functionality: allowing customers to select a paczkomat during checkout and displaying the selection in admin.

## Features

- ✅ InPost Paczkomat selection via official Geowidget
- ✅ Customer selects paczkomat during checkout
- ✅ Admin views selected paczkomat with image and address
- ✅ Phone number validation for InPost shipments
- ✅ Sylius 2.0+ Twig Hooks support

## Requirements

- PHP 8.3+
- Sylius 2.0+
- Symfony 6.4+ or 7.0+

## Installation

### 1. Install via Composer

Production install:

```bash
composer require azarniewicz/sylius-inpost-plugin
```

For local development with a path repository:

```bash
composer config repositories.azarnie-inpost path ../azarniewicz-sylius-inpost-plugin
composer require azarniewicz/sylius-inpost-plugin:@dev
```

### 2. Enable the Bundle

Add to `config/bundles.php`:

```php
Azarniewicz\SyliusInPostPlugin\AzarniewiczSyliusInPostPlugin::class => ['all' => true],
```

### 3. Import Routes

Add to `config/routes.yaml`:

```yaml
azarnie_inpost:
    resource: "@AzarniewiczSyliusInPostPlugin/Resources/config/routes.yaml"
```

### 4. Extend Order Entity

Update your `src/Entity/Order/Order.php`:

```php
<?php

declare(strict_types=1);

namespace App\Entity\Order;

use Azarniewicz\SyliusInPostPlugin\Entity\InPostPoint;
use Azarniewicz\SyliusInPostPlugin\Entity\InPostPointInterface;
use Azarniewicz\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use Azarniewicz\SyliusInPostPlugin\Model\OrderPointTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Order as BaseOrder;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder implements InPostPointsAwareInterface
{
    use OrderPointTrait;

    #[ORM\OneToOne(targetEntity: InPostPoint::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'point_id', nullable: true)]
    protected ?InPostPointInterface $point = null;
}
```

### 5. Create Migration

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

### 6. Configure Webpack

Add to `webpack.config.js`:

```javascript
const [inpostShop, inpostAdmin] = require('./vendor/azarniewicz/sylius-inpost-plugin/webpack.config.js');
module.exports = [...yourExistingConfigs, inpostShop, inpostAdmin];
```

The plugin auto-registers the required asset packages and Encore builds through the bundle extension.

### 7. Build Assets

```bash
npm install
npm run build
```

### 8. Override Templates

**Checkout** - `templates/bundles/SyliusShopBundle/checkout/select_shipping/content/form/shipments/shipment/choice.html.twig`:

```twig
{% for key, choice_form in form.method %}
    {% set method = form.method.vars.choices[key].data %}
    
    {# Your existing radio button/selection markup #}
    
    {% if method.code == azarniewicz_sylius_inpost_shipping_method_code %}
        <div class="mt-3" data-inpost-geowidget>
            {% include "@AzarniewiczSyliusInPostPlugin/Shop/Checkout/SelectShipping/_InPostGeowidget.html.twig" %}
        </div>
    {% endif %}
{% endfor %}
```

**Admin Order View** - `templates/bundles/SyliusAdminBundle/order/show/content/sections/customer/shipping_address.html.twig`:

```twig
{% if order.point and order.point.name %}
    <div class="mb-3">
        <strong>Paczkomat InPost:</strong> {{ order.point.name }}
    </div>
    <div data-inpost-preview data-point-code="{{ order.point.name }}">
        {% include "@AzarniewiczSyliusInPostPlugin/InPostGeowidgetPreview.html.twig" with {'order': order} %}
    </div>
{% else %}
    {# Display regular shipping address #}
{% endif %}
```

### 9. Configure Shipping Method

Create or update your shipping method so its code matches `shipping_method_code` from the plugin configuration (`inpost_point` by default):

```yaml
# config/fixtures/shipping_methods.yaml
shipping_method:
    inpost_point:
        name: 'Paczkomat InPost'
        code: 'inpost_point'
        enabled: true
        zone: 'PL'
        calculator:
            type: 'flat_rate'
            configuration:
                default:
                    amount: 1200  # 12.00 PLN
```

### 10. Optional Plugin Configuration

The production defaults work out of the box, but you can override them in `config/packages/azarniewicz_sylius_inpost.yaml`:

```yaml
azarniewicz_sylius_inpost:
    shipping_method_code: 'inpost_point'
    api_base_url: 'https://api-pl-points.easypack24.net/v1/points'
```

## Usage

1. Customer selects "Paczkomat InPost" shipping method during checkout
2. Geowidget appears with interactive map
3. Customer selects nearest paczkomat
4. Selected paczkomat code, image, and address are saved with order
5. Admin sees paczkomat details in order view (read-only)

## How It Works

### Checkout Flow

1. When customer selects InPost shipping method, JavaScript displays the geowidget
2. Customer clicks "Wybierz Paczkomat" to open the InPost map modal
3. After selecting a paczkomat, AJAX request saves it to the cart via `POST /inpost/point`
4. The selected paczkomat is displayed with image and address
5. Phone number validation ensures customer provided contact details

### Admin View

- Order detail page shows the selected paczkomat details
- JavaScript fetches full details (image, address) from InPost API
- Display is read-only in admin

## API Integration

The plugin fetches paczkomat details from InPost public API:
- **Endpoint**: `https://api-pl-points.easypack24.net/v1/points/{code}` by default, configurable via `api_base_url`
- **Usage**: Display paczkomat images and addresses
- **No authentication required** for public point data

## License

MIT
