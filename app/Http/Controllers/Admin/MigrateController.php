<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Extensions;
use App\Models\Orders;
use App\Models\User;

class MigrateController extends Controller
{
	public function index()
	{
		return view('admin.migrate.index');
	}

	public function blesta()
	{
		return view('admin.migrate.blesta');
	}

	public function whmcs()
	{
		return view('admin.migrate.whmcs');
	}

	public function whmcsImport(Request $request)
	{
		$host = $_POST['host'];
		$dbUsername = $_POST['dbUsername'];
		$dbPassword = $_POST['dbPassword'];
		$chosenOption = $_POST['chosenOption'];

		if ($chosenOption == 'clients') {
			$currency = null;
		} else {
			$currency = $_POST['currency'];
		}
		$replace = $_POST['replace'];
		
		$newChosenOption = 'Get' . ucFirst($chosenOption);

		if (!filter_var($request->host, FILTER_VALIDATE_URL)) {
			return back()->withErrors(['host' =>  'Invalid URL']);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host . '/includes/api.php');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			http_build_query(
				array(
					'action' => $newChosenOption,
					'identifier' => $dbUsername,
					'secret' => $dbPassword,
					'pid' => '0',
					'responsetype' => 'json',
					'limitnum' => '1000000',
				)
			)
		);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		$currency = strtoupper($currency);
		$output = json_decode($response, true);

		if($output == null) {
			return back()->withErrors(['No data found']);
		}

		$outboundProducts = array();
		$outboundOrders = array();
		$outboundClients = array();
		
		if ($newChosenOption == 'GetProducts') {
			foreach ($output['products']['product'] as $product) {
				if ($replace) {
					$existingProduct = Products::where('name', $product['name'])->first();
					if ($existingProduct) {
						$existingProduct->delete();
					}
				}
				if (Products::where('name', $product['name'])->exists()) {
					array_push($outboundProducts, $product['name']);
				} else {
					$newProdcut = new Products();
					$newProdcut->name = $product['name'];
					$newProdcut->description = $product['description'];
					if ($product['pricing'][$currency]['monthly'] == -1.00 || $product['pricing'][$currency]['monthly'] == -1.0 || $product['pricing'][$currency]['monthly'] == -1) {
						$newProdcut->price = 0;
					} else {
						$newProdcut->price = $product['pricing'][$currency]['monthly'];
					}
					$newProdcut->category_id = 1;
					$extension = Extensions::where('name', $product['module'])->first();
					if($extension) {
						$newProdcut->server_id = $extension->id;
					} else {
						$newProdcut->server_id = 0;
					}
					$newProdcut->image = 'null';
					$newProdcut->save();
				}
			}
			// dd($outboundProducts); // Testing Only
		}
		if ($newChosenOption == 'GetOrders') {
			foreach ($output['orders']['order'] as $order) {
				if ($replace == 'yes') {
					$existingOrder = Orders::where('id', $order['id'])->first();
					if ($existingOrder) {
						$existingOrder->delete();
					}
				}
				if (Orders::where('id', $order['id'])->exists()) {
					array_push($outboundOrders, $order['id']);
				} else {
					$newOrder = new Orders();
					$newOrder->id = $order['id'];
					foreach ($order['lineitems'] as $lineitemf) {
						foreach ($lineitemf as $lineitem) {
							$newOrder->products = $lineitem['product'];
						}
					}
					$newOrder->products = 'test';
					$newOrder->expiry_date = $order['date'];
					$newOrder->status = $order['status'];
					$newOrder->client = 9;
					if ($order['amount'] == 0.00 || $order['amount'] == 0.0 || $order['amount'] == 0) {
						$newOrder->total = 'Free';
					} else {
						$newOrder->total = $order['amount'];
					}
					$newOrder->created_at = $order['date'];
					$newOrder->updated_at = null;
					$newOrder->save();
				}
			}
			// dd($outboundOrders); // Testing Only
		}
		if ($newChosenOption == 'GetClients') { // Working as expected, do not change.
			foreach ($output['clients']['client'] as $client) {
				// This code deletes the user if that user already exists in the database.
				// This code is used to prevent duplicate entries.
				if ($replace == 'yes') {
					$existingClient = User::where('id', $client['id'])->first();
					if ($existingClient) {
						$existingClient->delete();
					}
				}
				// Checks if the client is in the database using the WHMCS ID
				if (User::where('id', $client['id'])->exists()) {
					array_push($outboundClients, $client['id']);
				} else {
					$newClient = new User();
					$newClient->id = $client['id'];
					$newClient->name = $client['firstname'] . ' ' . $client['lastname'];
					$newClient->email = $client['email'];
					$newClient->password = 'null';
					$newClient->created_at = $client['datecreated'];
					$newClient->updated_at = null;
					$newClient->save();
				}
			}
			// dd($outboundClients); // Testing Only
			if ($replace == 'yes') {
				return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully! Any duplicate data has been replaced. (Due to the nature of the API, passwords have not been imported.)');
			} else {
				return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully! (Due to the nature of the API, passwords have not been imported.)');
			}
		}
		// return; // Testing only
		if ($replace == 'yes') {
			return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully! All previous data has been replaced.');
		} else {
			return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully!');
		}
	}
}
