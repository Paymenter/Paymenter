<?php

namespace Tests\Feature;

use App\Models\LocationGroup;
use App\Models\LocationOption;
use App\Models\ProductLocationOffering;
use App\Models\ProviderLocationOffering;
use App\Models\ProviderLocationTarget;
use App\Models\Server;
use App\Models\Service;
use App\Models\User;
use App\Services\LocationAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_catalog_seed_creates_groups_and_preserves_legacy_ids(): void
    {
        $this->assertDatabaseHas('location_groups', [
            'code' => 'us',
            'name' => 'US',
        ]);

        $this->assertDatabaseHas('location_groups', [
            'code' => 'eu',
            'name' => 'EU',
        ]);

        $this->assertDatabaseHas('location_options', [
            'legacy_id' => 13,
            'code' => 'unknown',
            'status' => LocationOption::STATUS_HIDDEN,
        ]);

        $this->assertDatabaseHas('location_options', [
            'legacy_id' => 43,
            'code' => 'us',
            'status' => LocationOption::STATUS_DEPRECATED,
        ]);

        $this->assertDatabaseHas('location_options', [
            'legacy_id' => 78,
            'code' => 'united-states',
            'status' => LocationOption::STATUS_ACTIVE,
        ]);
    }

    public function test_provider_and_product_availability_resolve_provider_target(): void
    {
        [$provider, $offering, $productOffering] = $this->createLocationOffering();

        $providerLocations = LocationAvailabilityService::forProvider($provider->id, ProviderLocationOffering::SERVICE_VPS);
        $productLocations = LocationAvailabilityService::forProduct($productOffering->product_id, ProviderLocationOffering::SERVICE_VPS);
        $target = LocationAvailabilityService::resolveTarget($offering->id);

        $this->assertCount(1, $providerLocations);
        $this->assertSame($offering->id, $providerLocations->first()->id);
        $this->assertCount(1, $productLocations);
        $this->assertSame($productOffering->id, $productLocations->first()->id);
        $this->assertSame('us-east-1', $target->external_location_code);
    }

    public function test_disabled_and_hidden_locations_are_not_returned_for_checkout(): void
    {
        [, $offering, $productOffering] = $this->createLocationOffering();

        $offering->update(['enabled' => false]);
        $this->assertCount(0, LocationAvailabilityService::forProduct($productOffering->product_id));

        $offering->update(['enabled' => true]);
        $offering->locationOption->update(['status' => LocationOption::STATUS_HIDDEN]);
        $this->assertCount(0, LocationAvailabilityService::forProduct($productOffering->product_id));
    }

    public function test_snapshot_selection_stores_service_properties_independent_of_future_label_changes(): void
    {
        [, $offering, $productOffering] = $this->createLocationOffering();
        $product = $productOffering->product;
        $plan = $product->plans()->first();
        $service = Service::factory()->create([
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'user_id' => User::factory(),
            'status' => Service::STATUS_PENDING,
        ]);

        $snapshot = LocationAvailabilityService::snapshotSelection($service, $offering);
        $offering->locationOption->update(['display_name' => 'Renamed United States']);

        $this->assertSame('United States', $snapshot['display_name']);
        $this->assertDatabaseHas('properties', [
            'model_id' => $service->id,
            'model_type' => $service->getMorphClass(),
            'key' => 'display_name',
            'value' => 'United States',
        ]);
        $this->assertDatabaseHas('properties', [
            'model_id' => $service->id,
            'model_type' => $service->getMorphClass(),
            'key' => 'external_location_code',
            'value' => 'us-east-1',
        ]);
    }

    public function test_deleting_group_keeps_location_option_and_moves_it_to_no_group(): void
    {
        $group = LocationGroup::create([
            'code' => 'temp',
            'name' => 'Temp',
        ]);
        $option = LocationOption::create([
            'primary_group_id' => $group->id,
            'code' => 'temp-location',
            'display_name' => 'Temp Location',
            'option_type' => LocationOption::TYPE_GEO,
        ]);

        $group->delete();

        $this->assertDatabaseHas('location_options', [
            'id' => $option->id,
            'primary_group_id' => null,
        ]);
    }

    private function createLocationOffering(): array
    {
        $provider = Server::create([
            'name' => 'Test Provider',
            'extension' => 'TestProvider',
            'type' => 'server',
            'enabled' => true,
        ]);

        $location = LocationOption::where('code', 'united-states')->firstOrFail();

        $offering = ProviderLocationOffering::create([
            'provider_id' => $provider->id,
            'location_option_id' => $location->id,
            'service_type' => ProviderLocationOffering::SERVICE_VPS,
            'enabled' => true,
            'stock_state' => ProviderLocationOffering::STOCK_AVAILABLE,
        ]);

        ProviderLocationTarget::create([
            'provider_location_offering_id' => $offering->id,
            'external_location_code' => 'us-east-1',
            'external_name' => 'US East',
            'priority' => 10,
            'weight' => 100,
        ]);

        $productData = $this->createProduct([
            'server_id' => $provider->id,
        ]);

        $productOffering = ProductLocationOffering::create([
            'product_id' => $productData->product->id,
            'provider_location_offering_id' => $offering->id,
            'enabled' => true,
        ]);

        return [$provider, $offering, $productOffering];
    }
}
