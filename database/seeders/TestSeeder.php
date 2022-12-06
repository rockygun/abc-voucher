<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\PurchaseTransaction;
use App\Models\Voucher;
use DB;

class TestSeeder extends Seeder
{
    protected $customers = [
        [
            "id" => 1,
            "first_name" => "Rocky",
            "last_name" => "Gunawan",
            "gender" => "Male",
            "date_of_birth" => "1995-05-16",
            "contact_number" => "087712596916",
            "email" => "gunawan.rocky16@gmail.com",
        ],
        [
            "id" => 2,
            "first_name" => "Lina",
            "last_name" => "Indrawati",
            "gender" => "Female",
            "date_of_birth" => "1997-02-13",
            "contact_number" => "081874111345",
            "email" => "lina.indrawati@gmail.com",
        ],
        [
            "id" => 3,
            "first_name" => "Dino",
            "last_name" => "Setiawan",
            "gender" => "Male",
            "date_of_birth" => "1994-11-01",
            "contact_number" => "082125999888",
            "email" => "setiawan.dino@gmail.com",
        ],
        [
            "id" => 4,
            "first_name" => "Akbar",
            "last_name" => "Hildan",
            "gender" => "Male",
            "date_of_birth" => "1995-01-25",
            "contact_number" => "0879985566774",
            "email" => "akbar.hildan@gmail.com",
        ],
        [
            "id" => 5,
            "first_name" => "Sherla",
            "last_name" => "Liman",
            "gender" => "Female",
            "date_of_birth" => "1996-10-10",
            "contact_number" => "089665748928",
            "email" => "sherla.liman@gmail.com",
        ],
    ];

    public function run()
    {
        foreach ($this->customers as $data) {
            $data["created_at"] = now();
            $data["updated_at"] = now();
            Customer::updateOrCreate(["id" => $data["id"]], $data);
        }
        
        $customerId = 1;
        for ($i = 1; $i < 30; $i++) {
            $totalSpent = rand(10,40);
            $subDays = rand(0,45);

            $transaction["id"] = $i;
            $transaction["customer_id"] = $customerId;
            $transaction["total_spent"] = $totalSpent;
            $transaction["total_saving"] = 0;
            $transaction["transaction_at"] = now()->subDays($subDays)->endOfDay();
            
            PurchaseTransaction::updateOrCreate(["id" => $transaction["id"]], $transaction);

            if($i%6 == 0 ){
                $customerId++;
            }
        }

        $need = 1000;
        $have = 0;
        while ($have < $need) {
            // generate multi value INSERT
            $sql = 'INSERT IGNORE INTO vouchers VALUES ';
            for ($i = 1; $i < $need; $i++) {
                $sql .= "(NULL". sprintf(",'%s',", $this->getToken()) . "NULL, 'ACTIVE', '" . now() . "','" .  now() . "'),";
            }
            $sql .= "(NULL" . sprintf(",'%s',", $this->getToken()) . "NULL, 'ACTIVE', '" . now() . "','" .  now() . "'
            )";
            
            DB::statement($sql);
            $count  = Voucher::count();

            $need -= $count;
            $have += $count;
        }
    }

    public static function getToken()
    {
        $token = "";
        $length = 20;
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max-1)];
        }
        
        return $token;
    }

    static function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }
}
