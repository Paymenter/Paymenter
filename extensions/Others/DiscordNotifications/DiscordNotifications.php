<?php

namespace Paymenter\Extensions\Others\DiscordNotifications;

use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\OrderResource;
use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\TicketResource;
use App\Admin\Resources\UserResource;
use App\Classes\Extension\Extension;
use App\Events\Invoice;
use App\Events\Order;
use App\Events\Service;
use App\Events\Ticket;
use App\Events\TicketMessage;
use App\Events\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class DiscordNotifications extends Extension
{
    private const events = [
        'Order Created' => Order\Finalized::class,
        'Order Updated' => Order\Updated::class,
        'User Created' => User\Created::class,
        'User Updated' => User\Updated::class,
        'Invoice Created' => Invoice\Finalized::class,
        'Invoice Updated' => Invoice\Updated::class,
        'Invoice Paid' => Invoice\Paid::class,
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
                        } catch (\Exception $e) {
                            // Log the error
                            if (config('settings.debug')) {
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
            'value' => $this->mapId($event->{$model}->id, $model . '_id'),
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
            'user_id' => UserResource::class,
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
            'User Created' => fn ($event) => $this->createdEvent($event, 'user', ['id', 'first_name', 'last_name', 'email']),
            'Order Created' => fn ($event) => $this->createdEvent($event, 'order', ['id', 'formattedTotal', 'user_id']),
            'Order Updated' => fn ($event) => $this->updatedEvent($event, 'order'),
            'Invoice Created' => fn ($event) => $this->createdEvent($event, 'invoice', ['id', 'formattedTotal', 'user_id']),
            'Invoice Updated' => fn ($event) => $this->updatedEvent($event, 'invoice'),
            'Invoice Paid' => fn ($event) => $this->createdEvent($event, 'invoice', ['id']),
            'Ticket Created' => fn ($event) => $this->createdEvent($event, 'ticket', ['id', 'user_id']),
            'Ticket Updated' => fn ($event) => $this->updatedEvent($event, 'ticket'),
            'Ticket Replied' => fn ($event) => $this->createdEvent($event, 'ticketMessage', ['ticket_id', 'message']),
            'Service Created' => fn ($event) => $this->createdEvent($event, 'service', ['id', 'user_id', 'formattedPrice']),
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
}
