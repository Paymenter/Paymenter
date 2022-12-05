<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extensions;
use App\Models\OrderProducts;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;

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
		$replace = $_POST['replace'];
		$newChosenOption = 'Get' . ucFirst($chosenOption);

		if ($chosenOption == 'clients' || $chosenOption == 'orders') {
			$currency = null;
		} else {
			$currency = $_POST['currency'];
		}
		

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

		if (array_key_exists('message', $output)) {
			return back()->withErrors([$output['message']]);
		}
		
		if ($newChosenOption == 'GetProducts') {
			foreach ($output['products']['product'] as $product) {
				$newProdcut = new Products();

				if ($replace) {
					$existingProduct = Products::where('id', $product['pid'])->first();
					if ($existingProduct) {
						$existingProduct->delete();
						$newProdcut->id = $product['pid'];
					}
				}
				if (Products::where('id', $product['pid'])->exists()) {

				} else {
					$newProdcut->id = $product['pid'];
					$newProdcut->name = $product['name'];
					$newProdcut->description = $product['description'];
					if ($product['pricing'][$currency]['monthly'] == -1.00 || $product['pricing'][$currency]['monthly'] == -1.0 || $product['pricing'][$currency]['monthly'] == -1) {
						$newProdcut->price = 0;
					} else {
						$newProdcut->price = $product['pricing'][$currency]['monthly'];
					}
					$newProdcut->category_id = 2;
					$extension = Extensions::where('name', $product['module'])->first();
					if($extension) {
						$newProdcut->server_id = $extension->id;
					} else {
						$newProdcut->server_id = null;
					}
					$newProdcut->image = 'null';
					$newProdcut->save();
				}
			}
		}
		if ($newChosenOption == 'GetOrders') {
			foreach ($output['orders']['order'] as $order) {
				$che = curl_init();
				curl_setopt($che, CURLOPT_URL, $host . '/includes/api.php');
				curl_setopt($che, CURLOPT_POST, 1);
				curl_setopt(
					$che,
					CURLOPT_POSTFIELDS,
					http_build_query(
						array(
				            'action' => 'GetClientsProducts',
							'identifier' => $dbUsername,
							'secret' => $dbPassword,
				            'clientid' => $order['userid'],
				            'responsetype' => 'json',
				        )
				    )
				);
				curl_setopt($che, CURLOPT_RETURNTRANSFER, 1);
				$responseClientProducts = curl_exec($che);
				curl_close($che);
				$outputClientProducts = json_decode($responseClientProducts, true);

				if ($order['status'] == 'Pending') {
					$status = 'pending';
				} if ($order['status'] == 'Active') {
					$status = 'paid';
				} else {
					$status = strtolower($order['status']);
				}

				if ($replace == 'yes') {
					$existingOrder = Orders::where('id', $order['id'])->first();
					$existingOrderProduct = OrderProducts::where('order_id', $order['id'])->first();
					if ($existingOrder) {
						$existingOrder->delete();
					}
					if ($existingOrderProduct) {
						$existingOrderProduct->delete();
					}
				}

				$newOrder = new Orders();
				$newOrder->id = $order['id'];
				$expiry_date = $outputClientProducts['products']['product'][0]['nextduedate'] . ' 01:00:00';
				if ($expiry_date == '0000-00-00 01:00:00') {
					$expiry_date = '1970-01-01 01:00:00';
				}
				$newOrder->expiry_date = $expiry_date;
				$newOrder->created_at = $outputClientProducts['products']['product'][0]['regdate'] . ' 01:00:00';
				$newOrder->updated_at = '1970-01-01 01:00:00';
				$newOrder->status = $status;
				$newOrder->client = '67';
				$newOrder->total = $order['amount'];
				$newOrder->save();

				if ($replace == 'yes') {
					$existingOrderProduct = OrderProducts::where('order_id', $order['id'])->first();
					if ($existingOrderProduct) {
						$existingOrderProduct->delete();
					}
				}

				$newOrderProduct = new OrderProducts();
				$productID = $outputClientProducts['products']['product'][0]['pid'];
				$newOrderProduct->product_id = $productID;
				$newOrderProduct->id = $order['id'];
				$newOrderProduct->quantity = '1';
				$newOrderProduct->order_id = $order['id'];
				$newOrderProduct->save();
			}
		}
		$clientErrorArray = array();

		if ($newChosenOption == 'GetClients') {
			foreach ($output['clients']['client'] as $client) {
				if ($replace == 'yes') {
					$existingClient = User::where('id', $client['id'])->first();
					if ($existingClient) {
						$existingClient->delete();
					}
				}
				if (User::where('id', $client['id'])->exists()) {
					$clientErrorArray[] = $client['firstname'] . ' ' . $client['lastname'] . ' - ' . $client['email'];
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
			if (isset($clientErrorArray)) {
				$clientError = implode(', ', $clientErrorArray);
				$clientsAdded = count($output['clients']['client']) - count($clientErrorArray);
				$clientsAddedMessage = ($clientsAdded == 1) ? '1 client has' : $clientsAdded . ' clients have';
				return redirect()->route('admin.migrate.index')->with('error', $clientsAddedMessage . ' been added. The following clients already exist and have not been added: ' . $clientError);
			} else {
				if ($replace == 'yes') {
					return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully! Any duplicate data has been replaced. (Due to the nature of the API, passwords have not been imported.)');
				} else {
					return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully! (Due to the nature of the API, passwords have not been imported.)');
				}
			}
		}
		if ($replace == 'yes') {
			return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully! All previous data has been replaced.');
		} else {
			return redirect()->route('admin.migrate.index')->with('success', ucfirst($chosenOption) . ' imported successfully!');
		}
	}
}
