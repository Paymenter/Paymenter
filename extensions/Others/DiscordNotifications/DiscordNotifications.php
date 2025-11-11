<?php

namespace Paymenter\Extensions\Others\DiscordNotifications;

use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\OrderResource;
use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\TicketResource;
use App\Admin\Resources\UserResource;
use App\Classes\Extension\Extension;
use App\Events\Invoice;
use App\Events\Invoice\Paid;
use App\Events\Order\Finalized;
use App\Events\Order\Updated;
use App\Events\Service;
use App\Events\Ticket;
use App\Events\TicketMessage;
use App\Events\User;
use App\Events\User\Created;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class DiscordNotifications extends Extension
{
    private const events = [
        'Order Created' => Finalized::class,
        'Order Updated' => Updated::class,
        'User Created' => Created::class,
        'User Updated' => User\Updated::class,
        'Invoice Created' => Invoice\Finalized::class,
        'Invoice Updated' => Invoice\Updated::class,
        'Invoice Paid' => Paid::class,
        'Ticket Created' => Ticket\Created::class,
        'Ticket Updated' => Ticket\Updated::class,
        'Ticket Replied' => TicketMessage\Created::class,
        'Service Created' => Service\Created::class,
        'Service Updated' => Service\Updated::class,
    ];

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     * @return array
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'webhook_url',
                'type' => 'text',
                'label' => 'Webhook URL',
                'required' => true,
            ],
            [
                'name' => 'ping_type',
                'type' => 'select',
                'label' => 'Ping Type',
                'description' => 'The type of user/role to ping',
                'required' => false,
                'options' => [
                    [
                        'label' => 'None',
                        'value' => 'none',
                    ],
                    [
                        'label' => 'User',
                        'value' => 'user',
                    ],
                    [
                        'label' => 'Role',
                        'value' => 'role',
                    ],
                ],
            ],
            [
                'name' => 'ping_id',
                'type' => 'text',
                'label' => 'Ping ID',
                'description' => 'The ID of the user/role to ping',
                'required' => false,
            ],
            [
                'name' => 'events',
                'type' => 'select',
                'multiple' => true,
                'label' => 'Events',
                'description' => 'The events to send notifications for',
                'required' => true,
                'database_type' => 'array',
                'options' => array_keys(self::events),
            ],
        ];
    }

    public function boot()
    {
        foreach ($this->config('events') as $eventType) {
            if (in_array($eventType, array_keys(self::events))) {
                Event::listen(
                    self::events[$eventType],
                    function ($event) use ($eventType) {
                        try {
                            $this->sendNotification($event, $eventType);
                        } catch (Exception $e) {
                            // Log the error
                            if (config('settings.debug', false)) {
                                throw $e;
                            }
                        }
                    }
                );
            }
        }
    }

    private function updatedEvent($event, $model)
    {
        $changedFields = [];
        $changedFields[] = [
            'name' => 'ID',
            'value' => $model === 'user'
                ? $event->{$model}->name
                : $this->mapId($event->{$model}->id, $model . '_id'),
            'inline' => true,
        ];
        foreach ($event->{$model}->getChanges() as $field => $value) {
            if (!in_array($field, ['created_at', 'updated_at', 'password', 'remember_token'])) {
                if (!is_string($value)) {
                    $value = json_encode($value);
                }
                // Replace _ with space and capitalize the first letter
                $changedFields[] = [
                    'name' => ucfirst(str_replace('_', ' ', $field)),
                    'value' => $event->{$model}->getOriginal($field) . ' -> ' . $value,
                    'inline' => true,
                ];
            }
            if ($field === 'password') {
                $changedFields[] = [
                    'name' => 'Password',
                    'value' => '********',
                    'inline' => true,
                ];
            }
        }

        return $changedFields;
    }

    private function createdEvent($event, $model, $fields = [])
    {
        if (count($fields) === 0) {
            return [];
        }
        $efields = [];
        foreach ($fields as $value) {
            $name = ucfirst(str_replace('_', ' ', $value));
            if (str_contains($value, '_id') || $value === 'id') {
                if ($value === 'id') {
                    $value = $this->mapId($event->{$model}->{$value}, $model . '_id');
                } elseif ($value === 'user_id') {
                    $value = '[User ' . $event->{$model}->user->name . '](' . UserResource::getUrl('edit', ['record' => $event->{$model}->user_id]) . ')';
                } else {
                    $value = $this->mapId($event->{$model}->{$value}, $value);
                }
            } else {
                $value = $event->{$model}->{$value};
            }
            // Replace _ with space and capitalize the first letter
            $efields[] = [
                'name' => $name,
                'value' => (string) $value,
                'inline' => true,
            ];
        }

        return $efields;
    }

    private function mapId($id, $name)
    {
        // Get the route name for the model
        $resources = [
            'order_id' => OrderResource::class,
            'invoice_id' => InvoiceResource::class,
            'ticket_id' => TicketResource::class,
            'service_id' => ServiceResource::class,
        ];

        // Check if the name is in the resources array
        if (array_key_exists($name, $resources)) {
            return '[' . ucfirst($resources[$name]::getModelLabel()) . ' #' . $id . '](' . $resources[$name]::getUrl('edit', ['record' => $id]) . ')';
        }

        // If no route name is found, return the ID
        return $id;
    }

    private function sendNotification($event, $eventType)
    {
        $fields = [
            'User Updated' => fn ($event) => $this->updatedEvent($event, 'user'),
            'User Created' => fn ($event) => $this->userCreated($event->user),
            'Order Created' => fn ($event) => $this->orderCreated($event->order),
            'Order Updated' => fn ($event) => $this->updatedEvent($event, 'order'),
            'Invoice Created' => fn ($event) => $this->invoiceCreated($event->invoice),
            'Invoice Updated' => fn ($event) => $this->updatedEvent($event, 'invoice'),
            'Invoice Paid' => fn ($event) => $this->createdEvent($event, 'invoice', ['id']),
            'Ticket Created' => fn ($event) => $this->createdEvent($event, 'ticket', ['id', 'user_id']),
            'Ticket Updated' => fn ($event) => $this->updatedEvent($event, 'ticket'),
            'Ticket Replied' => fn ($event) => $this->createdEvent($event, 'ticketMessage', ['ticket_id', 'message']),
            'Service Created' => fn ($event) => $this->serviceCreated($event->service),
            'Service Updated' => fn ($event) => $this->updatedEvent($event, 'service'),
        ];

        // Check if the event type is valid
        if (!array_key_exists($eventType, $fields)) {
            return;
        }

        $fields = $fields[$eventType]($event);
        $webhookUrl = $this->config('webhook_url');
        $pingType = $this->config('ping_type');
        $pingId = $this->config('ping_id');

        $message = [
            'content' => ' ',
            'embeds' => [
                [
                    'title' => ucfirst(str_replace('_', ' ', $eventType)),
                    'description' => "A new event has occurred: {$eventType}",
                    'fields' => $fields,
                    'color' => 0x00FF00,
                    'footer' => [
                        'text' => 'Paymenter Notifications',
                    ],
                    'timestamp' => now()->toIso8601String(),
                ],
            ],
        ];

        if ($pingType !== 'none' && $pingId) {
            $message['content'] = ($pingType === 'user') ? "<@{$pingId}>" : "<@&{$pingId}>";
        }

        // Send the notification to Discord
        Http::post($webhookUrl, $message)->json();
    }

    private function userCreated(\App\Models\User $user)
    {
        $fields = [
            [
                'name' => 'ID',
                'value' => $this->mapId($user->id, 'user_id'),
                'inline' => true,
            ],
            [
                'name' => 'Name',
                'value' => $user->name,
                'inline' => true,
            ],
            [
                'name' => 'Email',
                'value' => $user->email,
                'inline' => true,
            ],
        ];

        // Add properties
        foreach ($user->properties as $key => $value) {
            $fields[] = [
                'name' => ucfirst(str_replace('_', ' ', $key)),
                'value' => (string) $value,
                'inline' => true,
            ];
        }

        return $fields;
    }

    private function orderCreated(Order $order)
    {
        $fields = [
            [
                'name' => 'ID',
                'value' => $this->mapId($order->id, 'order_id'),
                'inline' => true,
            ],
            [
                'name' => 'User',
                'value' => '[User ' . $order->user->name . '](' . UserResource::getUrl('edit', ['record' => $order->user_id]) . ')',
                'inline' => true,
            ],
            [
                'name' => 'Total',
                'value' => (string) $order->formattedTotal,
                'inline' => true,
            ],
        ];

        foreach ($order->services as $service) {
            $fields[] = [
                'name' => $service->product->name,
                'value' => '[Service #' . $service->id . '](' . ServiceResource::getUrl('edit', ['record' => $service->id]) . ') (' . $service->formattedPrice . ')',
                'inline' => true,
            ];

        }

        return $fields;
    }

    private function serviceCreated(\App\Models\Service $service)
    {
        $fields = [
            [
                'name' => 'ID',
                'value' => $this->mapId($service->id, 'service_id'),
                'inline' => true,
            ],
            [
                'name' => 'User',
                'value' => '[User ' . $service->user->name . '](' . UserResource::getUrl('edit', ['record' => $service->user_id]) . ')',
                'inline' => true,
            ],
            [
                'name' => 'Product',
                'value' => $service->product->name,
                'inline' => true,
            ],
            [
                'name' => 'Price',
                'value' => (string) $service->formattedPrice,
                'inline' => true,
            ],
        ];

        return $fields;
    }

    private function invoiceCreated(\App\Models\Invoice $invoice)
    {
        $fields = [
            [
                'name' => 'ID',
                'value' => $this->mapId($invoice->id, 'invoice_id'),
                'inline' => true,
            ],
            [
                'name' => 'User',
                'value' => '[User ' . $invoice->user->name . '](' . UserResource::getUrl('edit', ['record' => $invoice->user_id]) . ')',
                'inline' => true,
            ],
            [
                'name' => 'Total',
                'value' => (string) $invoice->formattedTotal,
                'inline' => true,
            ],
        ];

        foreach ($invoice->items as $item) {
            $fields[] = [
                'name' => $item->description,
                'value' => $item->quantity . ' x ' . $item->formattedPrice,
                'inline' => true,
            ];
        }

        return $fields;
    }
}
