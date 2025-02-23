<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthDataRequest;
use App\Http\Requests\UpdateHealthDataRequest;
use App\Models\HealthData;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Auth;

use App\Models\VLKey;

class HealthDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ("health_data.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $publicKey = VLKey::all()->where('user_id', $userId)->first()->public_key;

        $apiUrl = env('API_URL', 'http://localhost:3001');
        $encryption_endpoint = '/api/encryptdata';

        $dataToEncrypt = $request->data;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($apiUrl . $encryption_endpoint, [
                    'publicKey' => $publicKey,
                    'data' => $dataToEncrypt
                ]);

        $encryptedData = "";
        if ($response->successful()) {
            // Create a new item with the decrypted data
            $encryptedData = $response->json()["result"];
        } else {
            // If decryption fails, add the original item to the array with message
            // Can definitely upgrade to using aes for now if this fails
            $encryptedData = "Failed to encrypt, so the data is lost as of now";
        }

        $blockchainUrl = env('BLOCKCHAIN_URL', 'http://localhost:3002');
        $endpoint = '/add-block';
        $response = Http::post($blockchainUrl . $endpoint, [
            'user_id' => $userId,
            'data' => $encryptedData
        ]);

        if ($response->successful()) {
            return response()->json($response->json(), 201);
        }

        return response()->json(['error' => 'Failed to add block'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        function decryptData($privateKey, $jsonData)
        {
            $apiUrl = env('API_URL', 'http://localhost:3001');
            $decryption_endpoint = '/api/decryptdata';
            $decryptedData = [];

            foreach ($jsonData as $item) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($apiUrl . $decryption_endpoint, [
                                'privateKey' => $privateKey,
                                'encryptedData' => $item["data"]
                            ]);
                    if ($response->successful()) {
                        $result = $response->json();
                        
                        // Create a new item with the decrypted data
                        $decryptedItem = [
                            'user_id' => $item['user_id'],
                            'data' => $result['result'] ?? null,
                        ];
                    } else {
                        // If decryption fails, add the original item to the array with message
                        $decryptedItem = [
                            'user_id' => $item['user_id'],
                            'data' => "Failed to decrypt"
                        ];
                    }
                    $decryptedData[] = $decryptedItem;
                } catch (\Exception $e) {
                    // If an exception occurs, add the original item to the array
                    $decryptedData[] = $item;
                    // You might want to log the error here
                }
            }

            return $decryptedData;
        }


        function constructPrivateKey($userId, $userKey)
        {
            $health_care_key = VLKey::all()->where('user_id', $userId)->first()->health_care_key;
            $user_key = $userKey;

            $apiUrl = env('API_URL', 'http://localhost:3001');
            $decryptkeys_endpoint = '/api/decryptkeys';

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($apiUrl . $decryptkeys_endpoint, [
                            'part1' => $health_care_key,
                            'part2' => $user_key,
                        ]);

                if ($response->successful()) {
                    $privateKey = (string)$response->json()["message"];
                    return $privateKey;
                } else {
                    return "Could not create private key";
                }
            } catch (\Exception $e) {
                return "Error occured while decrypting";
            }
        }

        // Actual implementation
        $userId = Auth::id();

        $blockchainUrl = env('BLOCKCHAIN_URL', 'http://localhost:3002');
        $endpoint = '/get-user-data';
        $response = Http::post($blockchainUrl . $endpoint, [
            'user_id' => $userId
        ]);
        $jsonData = $response->json();
        $userKey = "0801c728b6e33824c09e6f7d182c96f0a84f2fbcc8892c6f8112139a09afd7f98658791d7d3d6125954fe4890a6aed0bcb26efedd45213e620331a71fd422748f4dbcc67355434b7e62ea80a3ccd30e930d2e980f2103b32649853433abea69de9443c4424c40329296c2e9c22531c907b45b5bcd580ca123a2707c2a46a4abc51dc0ebab391b1c05e2e5015824e7965318f54ccad26539dc65f29befd9b9054a41a0fde2b63dac8ebc7756f46e27399131a18f9a241a77ff8c65aae59aa08ddd478b30d9049ddb5d8ea9bfa4b1b72d540388f2d7f519c60cfb654e09adaa3f25042f0b9d3be074f15e1de314b29ad3fe94a747fa320464b09697ea3777a07a4623420c806706f6e8152aa3ea2cd14ff914f61b4b7033279434b4d0b4043d82e3c372e77cd566fb48bcdedad2de0314dd2e83055634b170d086dcedb76773b83866346b9c304bbaabbf45d363e5e06b98697098fdacf919d9b5478664b49c4a9e5a7a692cf0968fb3ca733fea01f700055bcf3e847b541da5659a433bd7a6e3d5e7f2b9532c01427004ab2ca123b8d4b35821c6cd5ba563c1c834df15c1cb4e21148845d857ddc111bf7582a1c39fdd2a09f22c85a0ea443e8ba22161d7a8d98e5d579375046c1c742a9241333a2679141ffa550e8f76942cf1bf336e3df99bf8eadac288065d04a3eb41eecdd1990bf91243626c3de7e72f4dc21930540474c29d39c9ba659c29378fc28d9189c6b7dffe6068a1a2793066df7c2c738215b1fd26c4ae527c80a9dfaa03212802c574320182a6c9836326ce9f4c7fb9897145e9c93be7d7d99acdd728988564ab5b5b68976b3e5f4bb6eb83d884bdf96dbd80241ea4cbd2f416d9eed0697bac7c7e4c32fa370a2265204719e501d3adddcb03698f6c5bf90cd550a192b7dffc662fc5740dd3f2f57a90d41d42ba896d20ee5aebf718b2ddc928a520dc62b865b6ceb7aef4c3480230b7c58ff41d6d37eb4a0f6a3324270de87039a09fbfc707017043e5b6b93c6089f11141c32c6d85de46223fdc4e1398308d1548f7924803bb328a78941c01612e192decd08fe7148a4fcc876823872c9f4e9705c12b11da419bd3584b96960dc5d595ca3fa17f29caad1966df4ecfb6f0c4f846b3b8435ea75a9613757844e8a2ad7e17a4da0f575909cff8b38d1064c84b4334d9448a02e81da39538e276c5c5aac4fff88f017934a1dfc69d37912f54f36e070885fba6aaa470f6958287c85bdd8c4b1de50903942c35271d65a77cd9702a22048548680ab25d83fc533f5a46c731c952e7a9925cb2442934c05c3af0974e9f86d161e8ca0851d9a714d4a1397000fb97c82cc460e3e2c904597209994bf1f47ca3ebe8908473f60daf2771940e6f591d354985d1039b26204fa7795b3128f8bcf423993ac25f8bd6576adc30bb0590625e1a92114396f1dbe746fa3de67810679fc43cbc5ea247a1f24071d69afc292a65b873926fc28ab200097d938b5364a95489cb993644146f82eb47a779fc4d959adb55bdc7a2e6fd9df86b625b89d7b70e4763a588f3b1c2aaeb0cc991257e20742b0a92f2284092e1f25fe766bd8048141ee92ae5c7a8d7c2fa62b233ed59c100116464df3475b2a3d7be593fe78d206ad32145ab0570f19c8827f14c9bcbdb6aebddc190c53ba90026b22492159c03f4924a5a29fa40f7eea46a2803d54e766d8aa50ef16d815d6f26610dd4fa5f4972a5b7e60eceabe46afc7d42d9d5139e8b0a64b61ea9f9057faabe9ab4b57b233c7af0a7b5f4c08ba01ec82f5f2ea2abecfc602d311d0a1a9503ffff7c3b34cd86007c20b266876fcd440458330a5292e4fc6137535531d3d087f8c35b55712f427aa6209e95ef385498845a7e7e4dd56ad763731b78924e99cef2a2d2c52f7b00df0ce2d85aaa8d5e462dc2320ac64a11099cdcccf4216c662ffc3f1565c4a3df0b11be93e9286821caa9f681a8814e8534d6dfd6394aee843753e00cc2963fa4f5e901bd91a9a1b758eb4c8f8d17c05be813c8269701c452f94cb4af61f5a3bfc6a2b3afdf2bc24fff8e4fc26c3839b0b4ce5330848d66cd212dac3a57955fe6f95c3adda39fc47b9231a2cb9dca40257f7346494ae05ce105b6a70e4d112a6093f34ea69884d798024b58e68a3232f1ce5ab7ec7b27075c5d7b8490cb42598e87ccaca8f9ae82751164e832e88bc1025e48f60346dcea043d5136258c753ddfc019ce70f125a694302cfe60a1cd8a2a90c842aaf63b55f3a6efe52345efc659f216937f49c4880ebbd1829e0368cc50388af67b5df4075b267e155899a6e4e6d385c97393089ee1b7d0df4ee6280011496b3057550c7ef7ddf193c0d1877217332e5306c120f4020a08d2fa324480e21a83b703e11c324f40f2c58a898c0b356b6523ca9fda2f7e5170682384689c2f41a53536dc8d4e8a4e484a46542e8f7458851d1b3a9bce8990ee1a7ca00ef7dcaf738af92492eae334cea9a5387237392ba8913a2bfe6a45ea2ed519fed02a69b8cbcc3a885583f3352ca425cbbedc68bca3c6aec34cff20f5851183f3c7f3db0a2a8c9a885cf6f89fc8b91968eb2dab99aef5c4d583d4a843ff4d5aba4bfb09302893a31e8479a7b06a5bd4459727a4599363b02a73f5abf7c3861b2f37a44358e21ddf49f5de5a01f500a86ef3d75df514ab91b4bfb4f18b6ed35910c17411e17881636908b2e8ae1962a055b17a76a1af752a958dea9152f806c86be5767aa2b0075a5ef811370110888092cc8191154e2ed5cc03f6ab6a8ed15a7309b62af5dbb38ab0de0b659efb6d95d7897969d37d96f225c4485ecb306ac875b974302e8ff055158eb186ed551ed4329ac9ae31541e04d1fc2bbb44131e3514e966eff43e82b39fc39a74860b3cc2d9a4b18f673ff0929478774b27a0caad3b378bfb6a921e02e15e73d9d0824e1741655f30fbe3c30486ffdab445fc9fe022edf64f1f4e62f475c47bec519e95de212b6f8a47256317ad26507a3bd253ed0f9b07622f2495f257f939bb56347fbeb04b2d58a2b44b366b2aac0eb5a8eb5cf380c56f47d6aa6dff7ab9afa4b78f9cb6be2a2fc5e42576582cecf98e07a67dc49e6b6494d62bbcb60f6e2287b8f03d5b269d59594e05eecffef32eab8a4423eced21ac662b9f10805420398b03c128a029b2761bac5d6e1a7ca4b4a6d2c9b29e06eef4c8859572d179d4ada7c1a3110eb9ffbcb6a53d0c62371d04e8926d0d64b5e7c197cc061d51ed4d0aa635d9ef4540642b21f081d7250097d99aa7770c5637ee3ff38f2ce81356d0b3af9202a8b3f1a72abe05fedce7c3e465ea46148ff4157abe3de8e015f88542dec3174047b0330f24a393be8b9f38b3238668bbe399663c0ce3232894a0218ffef4e717f7e8ccc6119a842e94bc79ebd11d47f8f7f7f0f0ae9a85db772337200a95444d1f5f8d53318b417f01e6853c79e8feb57c97c860573a4a7adce2e008f7cc3f06901d016171213d980fee42f3f4b6ba6a96e157c178f6cd8d882be8897220b0a5313f9f6ce2aa3100ef75bfb5c8d82fd60243506195843181c95b16e950e8a9ef189930ecdc99a943a159b5da8734a0ae32c23022ff0dc23f5b1d67873feb6cf648b0b80ae20d9031ce5bfe9bea2dc6113d39863a4bf1803e41fad9ca37e33c6096daa0abd5dcbadff5492d69d144f3a5ff50c97437204778ed09ed2f7bba2e412caaf43bf43107453aa78418daff3f745560bfa9ab347f02535bc1288d67474337ed74d12976d14f3ed92a7b615838214aeaa0e960a39c9acfb4e5c7df6986b5adadd234c1ffe1c5e7286e57a669569dab12eb4ae908129c4370db14939f51101a0b798e8815acae6a02c6e2f63dd676ff47e9f1ca88ed93e56d6b3a327ba3ea66f3264e1295c3ae8cfa52c450cbbc21ac0f11480499f97ecca532e90223ea16a46532fc2d70d8f57d9777ef6da490471bf915a7c35c4ac6a8e4905fbdf5e0aee8fb8c8571a3f1b2d0fc1af96292f0d9df7ab73e421127adf00489aa3b73ae77295daa8ffc2478251724f77581239863f4311dd33d6fdeac2e53c3f66c4abf6bd91e02807bc1dbe865f1016e5903ebd108e404fb5a74d14b041278380099c4a281c1b40bd0aaeed58d13b1ebf603518a71db3f6c5a0bf5c8e6a7353c76044b1aac5f9615be40666b347278ab6f024194fd03e9c31def36cf0bd66bc5b15c1e77e0b220648ef807712deaec648679c01389fc994b716e3e9b3d3f6a71173c4912658ed0cdce2357c4a726e65583c53bde4223eb0a92fa6a5e99d7fe9e82db883d7f4854e90e98818833dfec489397551e68d00d9c9ccb99e503a424a0d98d7e49cd5ac3e3e61c1bc982a43a7a4bfcbce7b4253f29d07f706ebca7e2e255daee9ad8e31fa82e720c9a473e3a62184d4133bc796fdd2b9ac6d05d15f006d573c50c04a6cb9fccfd148f887f6761dbe5d3f8d96904a658131b4e745d274826601771b41dfdce0fe6a5cf0f0931f34671f13845ae958a5db96362d211c96c5ebf724039ef06ea8235058b6fe29b3ac81939daf6eeb98fe63eea30559d8dcd0af10a34499c6de700e9f604ee4a2d49a996be409610e0d5262f91cf7a6bb7d05980430476b5eecc91db2559d8ee3346f19e744a4a89be67b1c3942b51cd48ad616d09785ff3870e3dd0c653db4ab65a12bbaa89ba84b2643621412e39bfc8c1bc89893d0d7cb4cb1674caf4bce7ff0f3853589be7fa02c7d2a27842ec90499d69a4be5303aea1bd5c45829d277f565cbbce5854b39f7e6cd370eaf91ca50fc7faf179e084324712c02dd6dae51eb5424ed25712fa024cd87635e8b539762e41a";
        $private_key = constructPrivateKey($userId, $userKey);
        
        if ($response->successful()) {
            $decryptedData = decryptData($private_key, $jsonData);
            return $decryptedData;
        }

        return response()->json(['error' => 'Failed to retrieve user data'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthData $healthData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthDataRequest $request, HealthData $healthData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthData $healthData)
    {
        //
    }
}
