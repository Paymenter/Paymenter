<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigurableGroup;
use App\Models\ConfigurableOption;
use App\Models\ConfigurableOptionInput;
use App\Models\OrderProductConfig;
use App\Models\Product;
use Illuminate\Http\Request;

class ConfigurableOptionController extends Controller
{
    /**
     * Display a listing of the configurable options.
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.configurable-options.index');
    }

    /**
     * Show the form for creating a new configurable option.
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $products = Product::all();
        return view('admin.configurable-options.create', compact('products'));
    }

    /**
     * Store a newly created configurable option in storage.
     * 
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|unique:configurable_option_groups',
            'description' => 'required|string',
            'products' => 'required|array',
        ]);
        $configurableGroup = ConfigurableGroup::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'products' => $data['products'],
        ]);

        return redirect()->route('admin.configurable-options.edit', $configurableGroup->id)->with('success', 'Configurable Option Group created successfully');
    }

    /**
     * Show the form for editing the specified configurable option.
     * 
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @return \Illuminate\View\View
     */
    public function edit(ConfigurableGroup $configurableOptionGroup): \Illuminate\View\View
    {
        $products = Product::all();
        $configurableOptions = $configurableOptionGroup->configurableOptions;
        return view('admin.configurable-options.edit', compact('configurableOptionGroup', 'products', 'configurableOptions'));
    }


    /**
     * Update the specified configurable option group in database.
     * 
     * @param  Request  $request
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ConfigurableGroup $configurableOptionGroup): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|unique:configurable_option_groups,name,' . $configurableOptionGroup->id,
            'description' => 'required|string',
            'products' => 'sometimes|array',
        ]);
        $configurableOptionGroup->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'products' => $data['products'] ?? [],
        ]);
        return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('success', 'Configurable Option Group updated successfully');
    }

    /**
     * Remove the specified configurable option group from database.
     * 
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ConfigurableGroup $configurableOptionGroup): \Illuminate\Http\RedirectResponse
    {
        // Get all orderproductconfig options
        $options = $configurableOptionGroup->configurableOptions;
        foreach ($options as $option) {
            // Get all orderproductconfig options
            $orderProductConfigOptions = OrderProductConfig::where('key', $option->id)->where('is_configurable_option', true)->get();
            // Delete all orderproductconfig options
            foreach ($orderProductConfigOptions as $orderProductConfigOption) {
                $orderProductConfigOption->delete();
            }

            // Also loop through all option inputs and delete them
            foreach ($option->configurableOptionInputs as $configurableOptionInput) {
                // Get all orderproductconfig options
                $orderProductConfigOptions = OrderProductConfig::where('value', $configurableOptionInput->id)->where('is_configurable_option', true)->get();
                // Delete all orderproductconfig options
                foreach ($orderProductConfigOptions as $orderProductConfigOption) {
                    $orderProductConfigOption->delete();
                }
                $configurableOptionInput->delete();
            }

            $option->delete();
        }

        $configurableOptionGroup->delete();

        return redirect()->route('admin.configurable-options')->with('success', 'Configurable Option Group deleted successfully');
    }


    /**
     * Create new configurable option.
     * 
     * @param  Request  $request
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOption(Request $request, ConfigurableGroup $configurableOptionGroup): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $item = $configurableOptionGroup->configurableOptions()->create([
            'name' => $data['name'],
            'type' => 'select',
            'order' => 0,
            'hidden' => false,
        ]);

        $option = $item->configurableOptionInputs()->create([
            'option_id' => $item->id,
            'name' => 'New Input',
            'order' => 0,
        ]);

        $option->configurableOptionInputPrice()->create();

        return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('success', 'Configurable Option created successfully');
    }

    /**
     * Update the specified configurable option in database.
     * 
     * @param  Request  $request
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @param  ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOption(Request $request, ConfigurableGroup $configurableOptionGroup, ConfigurableOption $configurableOption): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'order' => 'required|integer',
            'hidden' => 'required|boolean',
            'option' => 'sometimes|array',
        ]);
        $configurableOption->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'order' => $data['order'],
            'hidden' => $data['hidden'],
        ]);
        if (!isset($data['option'])) $data['option'] = [];
        foreach ($data['option'] as $optionInputId => $optionInputData) {
            $configurableOption->configurableOptionInputs()->find($optionInputId)->update([
                'name' => $optionInputData['name'],
                'order' => $optionInputData['order'],
                'hidden' => isset($optionInputData['hidden']) ? true : false,
            ]);

            $configurableOption->configurableOptionInputs()->find($optionInputId)->configurableOptionInputPrice()->update([
                'monthly' => $optionInputData['pricing']['monthly'],
                'quarterly' => $optionInputData['pricing']['quarterly'],
                'semi_annually' => $optionInputData['pricing']['semi_annually'],
                'annually' => $optionInputData['pricing']['annually'],
                'biennially' => $optionInputData['pricing']['biennially'],
                'triennially' => $optionInputData['pricing']['triennially'],
                'monthly_setup' => $optionInputData['pricing']['monthly_setup'],
                'quarterly_setup' => $optionInputData['pricing']['quarterly_setup'],
                'semi_annually_setup' => $optionInputData['pricing']['semi_annually_setup'],
                'annually_setup' => $optionInputData['pricing']['annually_setup'],
                'biennially_setup' => $optionInputData['pricing']['biennially_setup'],
                'triennially_setup' => $optionInputData['pricing']['triennially_setup'],
            ]);
        }

        return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('success', 'Configurable Option updated successfully')->with('open', $configurableOption->id);
    }

    /**
     * Delete the specified configurable option from database.
     * 
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @param  ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyOption(ConfigurableGroup $configurableOptionGroup, ConfigurableOption $configurableOption): \Illuminate\Http\RedirectResponse
    {
        // Get all orderproductconfig options
        $orderProductConfigOptions = OrderProductConfig::where('key', $configurableOption->id)->where('is_configurable_option', true)->get();
        // Delete all orderproductconfig options
        foreach ($orderProductConfigOptions as $orderProductConfigOption) {
            $orderProductConfigOption->delete();
        }

        // Also loop through all option inputs and delete them
        foreach ($configurableOption->configurableOptionInputs as $configurableOptionInput) {
            // Get all orderproductconfig options
            $orderProductConfigOptions = OrderProductConfig::where('value', $configurableOptionInput->id)->where('is_configurable_option', true)->get();
            // Delete all orderproductconfig options
            foreach ($orderProductConfigOptions as $orderProductConfigOption) {
                $orderProductConfigOption->delete();
            }
            $configurableOptionInput->delete();
        }


        $configurableOption->delete();
        return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('success', 'Configurable Option deleted successfully');
    }

    /**
     * Create new configurable option input.
     * 
     * @param  Request  $request
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @param  ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOptionInput(Request $request, ConfigurableGroup $configurableOptionGroup, ConfigurableOption $configurableOption): \Illuminate\Http\RedirectResponse
    {
        $optionInput = $configurableOption->configurableOptionInputs()->create([
            'option_id' => $configurableOption->id,
            'name' => 'New Input',
            'order' => 0,
        ]);
        $optionInput->configurableOptionInputPrice()->create();
        return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('success', 'Configurable Option Input created successfully')->with('open', $configurableOption->id);
    }

    /**
     * Delete the specified configurable option input from database.
     * 
     * @param  ConfigurableGroup  $configurableOptionGroup
     * @param  ConfigurableOption  $configurableOption
     * @param  ConfigurableOptionInput  $configurableOptionInput
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyOptionInput(ConfigurableGroup $configurableOptionGroup, ConfigurableOption $configurableOption, ConfigurableOptionInput $configurableOptionInput): \Illuminate\Http\RedirectResponse
    {
        // If the option input is lower then 1  
        if ($configurableOption->configurableOptionInputs()->count() < 2) {
            return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('error', 'You can not delete the last option input');
        }
        // Get all orderproductconfig options
        $orderProductConfigOptions = OrderProductConfig::where('value', $configurableOptionInput->id)->where('is_configurable_option', true)->get();
        // Delete all orderproductconfig options
        foreach ($orderProductConfigOptions as $orderProductConfigOption) {
            if ($orderProductConfigOption->value == $configurableOptionInput->id) {
                $orderProductConfigOption->delete();
            }
        }

        $configurableOptionInput->delete();
        return redirect()->route('admin.configurable-options.edit', $configurableOptionGroup->id)->with('success', 'Configurable Option Input deleted successfully')->with('open', $configurableOption->id);
    }
}
